<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\LunchBreak;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class LunchBreakController extends Controller
{
    private function hasSwitchOfficeAccess($user): bool
    {
        return $user->hasRole('super_admin')
            || $user->hasRole('owner')
            || $user->can('switch offices')
            || $user->can('switch office');
    }

    private function canManageEmployees($user): bool
    {
        return $user->hasRole('super_admin')
            || $user->hasRole('owner')
            || $user->hasRole('admin')
            || $user->can('switch offices')
            || $user->can('switch office')
            || $user->can('view employees')
            || $user->can('manage employees');
    }

    private function activeOfficeId(Request $request): ?int
    {
        $sessionOfficeId = $request->session()->get('active_office_id');

        if ($sessionOfficeId && (int) $sessionOfficeId > 0) {
            return (int) $sessionOfficeId;
        }

        $userOfficeId = $request->user()?->office_id;

        return $userOfficeId && (int) $userOfficeId > 0
            ? (int) $userOfficeId
            : null;
    }

    private function switchableOfficeIds(Request $request): array
    {
        $user = $request->user();

        if (!$user) {
            return [];
        }

        if ($user->hasRole('super_admin')) {
            return Office::query()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        }

        if ($user->hasRole('owner')) {
            return Office::query()
                ->where('owner_id', $user->id)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        }

        if ($user->can('switch offices') || $user->can('switch office')) {
            $currentOffice = $user->office;

            if ($currentOffice && $currentOffice->owner_id) {
                return Office::query()
                    ->where('owner_id', $currentOffice->owner_id)
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->toArray();
            }
        }

        return $user->office_id && (int) $user->office_id > 0
            ? [(int) $user->office_id]
            : [];
    }

    private function allowedOfficeIds(Request $request): array
    {
        $switchableOfficeIds = $this->switchableOfficeIds($request);
        $activeOfficeId = $this->activeOfficeId($request);

        if ($activeOfficeId) {
            return in_array((int) $activeOfficeId, $switchableOfficeIds, true)
                ? [(int) $activeOfficeId]
                : [];
        }

        return $switchableOfficeIds;
    }

    private function officeEmployeesQuery(Request $request)
    {
        $officeIds = $this->allowedOfficeIds($request);

        return User::query()
            ->with('office')
            ->when(!empty($officeIds), function ($q) use ($officeIds) {
                $q->whereIn('office_id', $officeIds);
            }, function ($q) {
                $q->whereRaw('1 = 0');
            });
    }

    private function sortEmployeesHierarchically($employees)
    {
        $employees = collect($employees);

        $grouped = $employees->groupBy('team_leader_id');
        $sorted = collect();

        $appendChildren = function ($leaderId) use (&$appendChildren, $grouped, &$sorted) {
            if (!isset($grouped[$leaderId])) {
                return;
            }

            foreach ($grouped[$leaderId]->sortBy('name') as $employee) {
                $sorted->push($employee);
                $appendChildren($employee->id);
            }
        };

        if (isset($grouped[null])) {
            foreach ($grouped[null]->sortBy('name') as $employee) {
                $sorted->push($employee);
                $appendChildren($employee->id);
            }
        }

        $remaining = $employees->whereNotIn('id', $sorted->pluck('id'));

        foreach ($remaining->sortBy('name') as $employee) {
            $sorted->push($employee);
            $appendChildren($employee->id);
        }

        return $sorted->unique('id')->values();
    }

    private function activeEmployees(Request $request)
    {
        $authUser = $request->user();

        $employees = $this->officeEmployeesQuery($request)
            ->where('status', '1')
            ->orderBy('name')
            ->get();

        $employees = $this->sortEmployeesHierarchically($employees);

        if (!$this->canManageEmployees($authUser)) {
            $employees = $employees
                ->filter(function ($employee) use ($authUser) {
                    return (int) $employee->id === (int) $authUser->id;
                })
                ->values();
        }

        return $employees;
    }

    private function ensureEmployeeAllowed(Request $request, User $employee): void
    {
        $authUser = $request->user();

        if (!$authUser) {
            abort(401, 'Unauthenticated.');
        }

        $allowedOfficeIds = $this->allowedOfficeIds($request);

        if (empty($allowedOfficeIds)) {
            abort(403, 'Please select active office first.');
        }

        if (!in_array((int) $employee->office_id, $allowedOfficeIds, true)) {
            abort(403, 'This employee does not belong to the selected office.');
        }

        if ((string) $employee->status !== '1') {
            abort(403, 'This employee is inactive.');
        }

        if ($employee->office && $employee->office->status === 'inactive') {
            abort(403, 'This employee office is inactive.');
        }

        if (!$this->canManageEmployees($authUser) && (int) $employee->id !== (int) $authUser->id) {
            abort(403, 'You are not allowed to access this employee.');
        }
    }

    public function start(Request $request, User $employee = null)
    {
        $request->validate([
            'image' => 'nullable',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'distance' => 'nullable',
            'reason' => 'nullable|string',
        ]);

        if (!$employee) {
            $employee = auth()->user();
        }

        $employee->loadMissing('office');

        $this->ensureEmployeeAllowed($request, $employee);

        $record = AttendanceRecord::whereDate('created_at', Carbon::today())
            ->where('user_id', $employee->id)
            ->first();

        if (!$record) {
            return back()->with('error', 'Error, You can\'t take break without check-in');
        }

        $runningBreak = LunchBreak::whereDate('created_at', Carbon::today())
            ->where('user_id', $employee->id)
            ->whereNull('end_time')
            ->latest()
            ->first();

        if ($runningBreak) {
            return back()->with('error', 'Break already started. Please end current break first.');
        }

        $break = LunchBreak::create([
            'attendance_record_id' => $record->id,
            'user_id' => $employee->id,
            'start_time' => Carbon::now()->format('H:i:s'),
            'start_latitude' => $request->latitude,
            'start_longitude' => $request->longitude,
            'reason' => $request->reason,
            'start_distance' => $request->distance,
        ]);

        if ($request->hasFile('image')) {
            $photo = $request->file('image')->store('public/images');
            $break->start_image = str_replace('public/', '', $photo);
            $break->save();
        }

        return redirect('home')->with('success', 'Break start successfully');
    }

    public function stop(Request $request, LunchBreak $break)
    {
        $request->validate([
            'image' => 'nullable',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'distance' => 'nullable',
        ]);

        $employee = User::with('office')->findOrFail($break->user_id);

        $this->ensureEmployeeAllowed($request, $employee);

        if ($break->end_time) {
            return redirect('home')->with('error', 'This break is already ended.');
        }

        $status = $break->update([
            'end_time' => Carbon::now()->format('H:i:s'),
            'end_latitude' => $request->latitude,
            'end_longitude' => $request->longitude,
            'end_distance' => $request->distance,
        ]);

        if ($request->hasFile('image')) {
            $photo = $request->file('image')->store('public/images');
            $break->end_image = str_replace('public/', '', $photo);
            $break->save();
        }

        if ($status) {
            $request->session()->flash('success', 'Break end successfully');
        } else {
            $request->session()->flash('error', 'Failed, Try again!');
        }

        return redirect('home');
    }

    // public function index(Request $request)
    // {
    //     $date = $request->date
    //         ? Carbon::parse($request->date)->toDateString()
    //         : Carbon::today()->toDateString();

    //     /*
    //      * Main fix:
    //      * Pehle HomeController::employeeList() aa raha tha.
    //      * Usme inactive/register request wale users bhi aa rahe the.
    //      * Ab EmployeeController jaisa office + status filter use ho raha hai.
    //      */
    //     $users = $this->activeEmployees($request);

    //     return view('dashboard.break.index', compact('users', 'date'));
    // }

    public function index(Request $request)
    {
        $date = $request->date
            ? Carbon::parse($request->date)->toDateString()
            : Carbon::today()->toDateString();

        $usersCollection = $this->activeEmployees($request);

        $perPage = 10;
        $currentPage = Paginator::resolveCurrentPage();

        $users = new LengthAwarePaginator(
            $usersCollection->forPage($currentPage, $perPage)->values(),
            $usersCollection->count(),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $userIds = $users->getCollection()->pluck('id')->toArray();

        $breaks = LunchBreak::whereDate('created_at', $date)
            ->whereIn('user_id', $userIds)
            ->orderBy('created_at')
            ->get()
            ->groupBy('user_id');

        $users->getCollection()->transform(function ($user) use ($breaks) {
            $user->breaks = $breaks->get($user->id, collect())->values();
            return $user;
        });

        return view('dashboard.break.index', compact('users', 'date'));
    }

    public function form(User $employee = null, LunchBreak $break = null)
    {
        if ($employee) {
            $this->ensureEmployeeAllowed(request(), $employee);
        }

        if ($break) {
            $breakUser = User::with('office')->findOrFail($break->user_id);
            $this->ensureEmployeeAllowed(request(), $breakUser);
        }

        return view('dashboard.break.form', compact('break', 'employee'));
    }
}