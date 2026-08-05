<?php

namespace App\Http\Controllers;

use App\Mail\LeaveRequest;
use App\Mail\LeaveResponse;
use App\Models\EmployeeRoster;
use App\Models\Leave;
use App\Models\LeaveImage;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

class LeaveController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Active office
    |--------------------------------------------------------------------------
    */

    private function activeOfficeId(Request $request): ?int
    {
        $user = $request->user();

        if (!$user) {
            return null;
        }

        $sessionOfficeId = (int) $request->session()->get(
            'active_office_id',
            0
        );

        if ($sessionOfficeId > 0) {
            return $sessionOfficeId;
        }

        if (!empty($user->office_id)) {
            return (int) $user->office_id;
        }

        if ($user->hasRole('owner')) {
            $officeId = Office::query()
                ->where('owner_id', $user->id)
                ->orderBy('id')
                ->value('id');

            return $officeId ? (int) $officeId : null;
        }

        if ($user->hasRole('super_admin')) {
            $officeId = Office::query()
                ->orderBy('id')
                ->value('id');

            return $officeId ? (int) $officeId : null;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Offices accessible by logged-in user
    |--------------------------------------------------------------------------
    */

    private function allowedOfficeIds(Request $request): array
    {
        $user = $request->user();

        if (!$user) {
            return [];
        }

        if ($user->hasRole('super_admin')) {
            return Office::query()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        }

        if ($user->hasRole('owner')) {
            return Office::query()
                ->where('owner_id', $user->id)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        }

        /*
         * Office switching permission वाले user को उसी owner के offices।
         */
        if (
            $user->can('switch offices')
            || $user->can('switch office')
        ) {
            $ownerId = null;

            if (!empty($user->office_id)) {
                $ownerId = Office::query()
                    ->whereKey($user->office_id)
                    ->value('owner_id');
            }

            if ($ownerId) {
                return Office::query()
                    ->where('owner_id', $ownerId)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all();
            }
        }

        return !empty($user->office_id)
            ? [(int) $user->office_id]
            : [];
    }

    /*
    |--------------------------------------------------------------------------
    | Valid selected office
    |--------------------------------------------------------------------------
    */

    private function selectedOfficeId(Request $request): ?int
    {
        $activeOfficeId = $this->activeOfficeId($request);
        $allowedOfficeIds = $this->allowedOfficeIds($request);

        if (
            $activeOfficeId
            && in_array(
                (int) $activeOfficeId,
                $allowedOfficeIds,
                true
            )
        ) {
            return (int) $activeOfficeId;
        }

        return !empty($allowedOfficeIds)
            ? (int) $allowedOfficeIds[0]
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Safe nested team hierarchy
    |--------------------------------------------------------------------------
    |
    | Collection recursion या push/prepend का उपयोग नहीं है।
    | Circular hierarchy आने पर visited IDs उसे रोक देंगे।
    |
    */

    private function teamHierarchyEmployeeIds(
        int $teamLeaderId,
        ?int $officeId = null
    ): array {
        $result = [$teamLeaderId];
        $visited = [$teamLeaderId => true];
        $currentLeaderIds = [$teamLeaderId];

        /*
         * Safety limit बहुत बड़ी गलत hierarchy से भी request को बचाती है।
         */
        $level = 0;
        $maximumLevels = 100;

        while (
            !empty($currentLeaderIds)
            && $level < $maximumLevels
        ) {
            $level++;

            $query = User::query()
                ->whereIn('team_leader_id', $currentLeaderIds)
                ->where('status', '1');

            if ($officeId) {
                $query->where('office_id', $officeId);
            }

            $childIds = $query
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();

            $nextLeaderIds = [];

            foreach ($childIds as $childId) {
                if ($childId <= 0) {
                    continue;
                }

                /*
                 * Self-reference और circular reference दोनों सुरक्षित।
                 */
                if (isset($visited[$childId])) {
                    continue;
                }

                $visited[$childId] = true;
                $result[] = $childId;
                $nextLeaderIds[] = $childId;
            }

            $currentLeaderIds = $nextLeaderIds;
        }

        return array_values(array_unique($result));
    }

    /*
    |--------------------------------------------------------------------------
    | Employees visible to logged-in user
    |--------------------------------------------------------------------------
    */

    private function allowedEmployeeIds(Request $request): array
    {
        $user = $request->user();

        if (!$user) {
            return [];
        }

        /*
         * Normal employee केवल खुद को देखेगा।
         */
        if (
            !$user->hasAnyRole([
                'super_admin',
                'owner',
                'admin',
                'team_leader',
            ])
        ) {
            return [(int) $user->id];
        }

        $officeId = $this->selectedOfficeId($request);

        /*
         * Team leader खुद और पूरी nested team देखेगा।
         */
        if ($user->hasRole('team_leader')) {
            return $this->teamHierarchyEmployeeIds(
                (int) $user->id,
                $officeId
            );
        }

        /*
         * Management selected office के सभी active employees देखेगा।
         */
        if (!$officeId) {
            return [];
        }

        return User::query()
            ->where('office_id', $officeId)
            ->where('status', '1')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Scope authorization
    |--------------------------------------------------------------------------
    */

    private function ensureLeaveInScope(
        Request $request,
        Leave $leave
    ): void {
        $allowedEmployeeIds = $this->allowedEmployeeIds($request);

        if (
            !in_array(
                (int) $leave->user_id,
                $allowedEmployeeIds,
                true
            )
        ) {
            abort(
                403,
                'This leave record is outside your allowed hierarchy.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Safe mail sender
    |--------------------------------------------------------------------------
    |
    | Mail server failure के कारण leave request fail नहीं होगी।
    |
    */

    private function sendLeaveRequestMail(
        ?string $email,
        Leave $leave
    ): void {
        if (empty($email)) {
            return;
        }

        try {
            Mail::to($email)->send(
                new LeaveRequest($leave)
            );
        } catch (Throwable $exception) {
            Log::error('Leave request email failed.', [
                'leave_id' => $leave->id,
                'email' => $email,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function sendLeaveResponseMail(
        ?string $email,
        Leave $leave
    ): void {
        if (empty($email)) {
            return;
        }

        try {
            Mail::to($email)->send(
                new LeaveResponse($leave)
            );
        } catch (Throwable $exception) {
            Log::error('Leave response email failed.', [
                'leave_id' => $leave->id,
                'email' => $email,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Leave listing
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $employeeIds = $this->allowedEmployeeIds($request);

        $query = Leave::query()
            ->with([
                'user:id,name,email,office_id,team_leader_id',
                'responsesBy:id,name',
            ]);

        if (empty($employeeIds)) {
            $query->whereRaw('1 = 0');
        } else {
            $query->whereIn('user_id', $employeeIds);
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                (string) $request->input('status')
            );
        }

        if ($request->filled('employee_id')) {
            $employeeId = (int) $request->input('employee_id');

            if (
                in_array(
                    $employeeId,
                    $employeeIds,
                    true
                )
            ) {
                $query->where('user_id', $employeeId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if ($request->filled('from_date')) {
            $query->whereDate(
                'start_date',
                '>=',
                $request->input('from_date')
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'end_date',
                '<=',
                $request->input('to_date')
            );
        }

        $leaves = $query
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.leave.index', [
            'leaves' => $leaves,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create form
    |--------------------------------------------------------------------------
    */

    public function create(
        Request $request,
        $employeeId = null
    ) {
        $allowedEmployeeIds = $this->allowedEmployeeIds($request);

        if ($employeeId !== null) {
            $employeeId = (int) $employeeId;

            if (
                !in_array(
                    $employeeId,
                    $allowedEmployeeIds,
                    true
                )
            ) {
                abort(
                    403,
                    'This employee is outside your allowed hierarchy.'
                );
            }
        }

        return view('dashboard.leave.create', [
            'employeeId' => $employeeId,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store leave
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => [
                'required',
                'string',
                'max:100',
            ],
            'is_paid' => [
                'required',
            ],
            'start_date' => [
                'required',
                'date',
            ],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'reason' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'image' => [
                'nullable',
                'array',
            ],
            'image.*' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120',
            ],
            'employee_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id'),
            ],
        ]);

        $loggedInUser = $request->user();
        $allowedEmployeeIds = $this->allowedEmployeeIds($request);

        $employeeId = $request->filled('employee_id')
            ? (int) $request->input('employee_id')
            : (int) $loggedInUser->id;

        if (
            !in_array(
                $employeeId,
                $allowedEmployeeIds,
                true
            )
        ) {
            abort(
                403,
                'This employee is outside your allowed hierarchy.'
            );
        }

        $employee = User::query()
            ->findOrFail($employeeId);

        if (empty($employee->office_id)) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Selected employee is not assigned to an office.'
                );
        }

        $startDate = Carbon::parse(
            $validated['start_date']
        )->startOfDay();

        $endDate = !empty($validated['end_date'])
            ? Carbon::parse($validated['end_date'])->startOfDay()
            : $startDate->copy();

        $dayCount = $startDate->diffInDays($endDate) + 1;

        DB::beginTransaction();

        try {
            $leave = Leave::query()->create([
                'user_id' => $employee->id,
                'office_id' => $employee->office_id,
                'leave_type' => $validated['leave_type'],
                'is_paid' => $validated['is_paid'],
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'reason' => $validated['reason'] ?? null,
                'day_count' => $dayCount,
                'status' => 'pending',
            ]);

            if ($request->hasFile('image')) {
                foreach ($request->file('image', []) as $image) {
                    if (
                        !$image
                        || !$image->isValid()
                    ) {
                        continue;
                    }

                    $path = $image->store('public/leave');

                    LeaveImage::query()->create([
                        'leave_id' => $leave->id,
                        'path' => str_replace(
                            'public',
                            '',
                            $path
                        ),
                    ]);
                }
            }

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('Leave creation failed.', [
                'employee_id' => $employeeId,
                'error' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Leave request could not be created. Please try again.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Notification recipients
        |--------------------------------------------------------------------------
        */

        $admin = User::query()
            ->where('office_id', $employee->office_id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->first();

        $superAdmin = User::role('super_admin')->first();

        $teamLeader = !empty($employee->team_leader_id)
            ? User::query()->find($employee->team_leader_id)
            : null;

        $recipientEmails = collect([
            $superAdmin?->email,
            $admin?->email,
            $teamLeader?->email,
        ])
            ->filter()
            ->unique()
            ->values();

        foreach ($recipientEmails as $email) {
            $this->sendLeaveRequestMail(
                (string) $email,
                $leave
            );
        }

        return back()->with(
            'success',
            'Leave request submitted successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Approve/reject leave
    |--------------------------------------------------------------------------
    */

    public function status(
        Request $request,
        Leave $leave,
        $status,
        $type = null
    ) {
        $this->ensureLeaveInScope($request, $leave);

        $allowedStatuses = [
            'pending',
            'approved',
            'rejected',
            'cancelled',
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            abort(422, 'Invalid leave status.');
        }

        DB::beginTransaction();

        try {
            $leave->update([
                'status' => $status,
                'responses_by' => $request->user()->id,
                'approve_as' => $type,
            ]);

            if ($status === 'approved') {
                $startDate = Carbon::parse(
                    $leave->start_date
                )->startOfDay();

                $endDate = Carbon::parse(
                    $leave->end_date ?? $leave->start_date
                )->startOfDay();

                /*
                 * Excessively large bad date ranges से request को बचाना।
                 */
                $maximumRosterDays = 366;

                if (
                    $startDate->diffInDays($endDate) + 1
                    > $maximumRosterDays
                ) {
                    throw new \RuntimeException(
                        'Leave range exceeds 366 days.'
                    );
                }

                $currentDate = $startDate->copy();

                while ($currentDate->lte($endDate)) {
                    EmployeeRoster::query()->updateOrCreate(
                        [
                            'employee_id' => $leave->user_id,
                            'duty_date' => $currentDate->toDateString(),
                        ],
                        [
                            'office_id' => $leave->office_id,
                            'status' => 'leave',
                            'leave_id' => $leave->id,
                            'shift_name' => null,
                            'shift_start' => null,
                            'shift_end' => null,
                            'remarks' => 'Leave approved',
                            'created_by' => $request->user()->id,
                        ]
                    );

                    $currentDate->addDay();
                }
            } else {
                /*
                 * Approved leave बाद में reject/cancel हो तो उससे बनी roster
                 * leave entries हटेंगी।
                 */
                EmployeeRoster::query()
                    ->where('leave_id', $leave->id)
                    ->where('status', 'leave')
                    ->delete();
            }

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error('Leave status update failed.', [
                'leave_id' => $leave->id,
                'status' => $status,
                'error' => $exception->getMessage(),
            ]);

            return back()->with(
                'error',
                'Leave status could not be updated.'
            );
        }

        $leave->loadMissing('user');

        $employeeEmail = $leave->user?->email1
            ?? $leave->user?->email;

        $this->sendLeaveResponseMail(
            $employeeEmail,
            $leave
        );

        return back()->with(
            'success',
            'Leave status updated successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Show leave
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        $id
    ) {
        $leave = Leave::query()
            ->with([
                'user',
                'responsesBy',
            ])
            ->findOrFail($id);

        $this->ensureLeaveInScope($request, $leave);

        return view('dashboard.leave.show', [
            'leave' => $leave,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Save response
    |--------------------------------------------------------------------------
    */

    public function response(
        Request $request,
        Leave $leave
    ) {
        $this->ensureLeaveInScope($request, $leave);

        $validated = $request->validate([
            'status' => [
                'nullable',
                Rule::in([
                    'pending',
                    'approved',
                    'rejected',
                    'cancelled',
                ]),
            ],
            'approve_as' => [
                'nullable',
                'string',
                'max:100',
            ],
            'response' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $updateData = [
            'responses_by' => $request->user()->id,
        ];

        if (array_key_exists('status', $validated)) {
            $updateData['status'] = $validated['status'];
        }

        if (array_key_exists('approve_as', $validated)) {
            $updateData['approve_as'] = $validated['approve_as'];
        }

        /*
         * केवल तभी लगाएं जब leaves table में response column मौजूद है।
         * आपके form में response field नहीं है तो यह block नहीं चलेगा।
         */
        if (
            array_key_exists('response', $validated)
            && $validated['response'] !== null
        ) {
            $updateData['response'] = $validated['response'];
        }

        try {
            $leave->update($updateData);
        } catch (Throwable $exception) {
            Log::error('Leave response update failed.', [
                'leave_id' => $leave->id,
                'error' => $exception->getMessage(),
            ]);

            return back()->with(
                'error',
                'Leave response could not be saved.'
            );
        }

        $leave->loadMissing('user');

        $employeeEmail = $leave->user?->email1
            ?? $leave->user?->email;

        $this->sendLeaveResponseMail(
            $employeeEmail,
            $leave
        );

        return back()->with(
            'success',
            'Leave response saved successfully.'
        );
    }
}