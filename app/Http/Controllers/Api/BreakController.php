<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\LunchBreak;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BreakController extends Controller
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
        /*
         * EmployeeController ke jaise active office session se lo.
         * API route me session na ho to direct user office fallback.
         */
        if ($request->hasSession()) {
            $sessionOfficeId = $request->session()->get('active_office_id');

            if ($sessionOfficeId && (int) $sessionOfficeId > 0) {
                return (int) $sessionOfficeId;
            }
        }

        /*
         * Agar User model me activeOfficeId method hai to use bhi support karo.
         */
        if ($request->user() && method_exists($request->user(), 'activeOfficeId')) {
            $activeOfficeId = $request->user()->activeOfficeId();

            if ($activeOfficeId && (int) $activeOfficeId > 0) {
                return (int) $activeOfficeId;
            }
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

        /*
         * Agar normal employee API hit kare to sirf apna data mile.
         * Admin/owner/super_admin/switch permission wale selected office ke employees dekh sakte hain.
         */
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

        if (!$this->canManageEmployees($authUser) && (int) $employee->id !== (int) $authUser->id) {
            abort(403, 'You are not allowed to access this employee.');
        }
    }

    public function index(Request $request)
    {
        $date = $request->date
            ? Carbon::parse($request->date)->toDateString()
            : today()->toDateString();

        /*
         * Main fix:
         * EmployeeController ke same allowed office logic se employees.
         * Default me sirf active employees: status = 1.
         */
        $employees = $this->activeEmployees($request);

        $employeeIds = $employees->pluck('id')->toArray();

        $breaks = collect();

        if (!empty($employeeIds)) {
            $breaks = LunchBreak::whereDate('created_at', $date)
                ->whereIn('user_id', $employeeIds)
                ->orderBy('created_at')
                ->get()
                ->groupBy('user_id');
        }

        foreach ($employees as $employee) {
            $employee->breaks = $breaks->get($employee->id, collect())->values();
        }

        return response()->json([
            'success' => true,
            'date' => $date,
            'employees' => $employees,
        ], 200);
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
            $employee = $request->user();
        }

        $employee->loadMissing('office');

        $this->ensureEmployeeAllowed($request, $employee);

        $record = AttendanceRecord::whereDate('created_at', Carbon::today())
            ->where('user_id', $employee->id)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Error: You can\'t take a break without checking in.',
            ], 400);
        }

        $runningBreak = LunchBreak::whereDate('created_at', Carbon::today())
            ->where('user_id', $employee->id)
            ->whereNull('end_time')
            ->latest()
            ->first();

        if ($runningBreak) {
            return response()->json([
                'success' => false,
                'message' => 'Break already started. Please end current break first.',
                'break' => $runningBreak,
            ], 400);
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

        return response()->json([
            'success' => true,
            'message' => 'Break started successfully',
            'break' => $break->fresh(),
        ], 200);
    }

    public function stop(Request $request, User $employee = null)
    {
        $request->validate([
            'break_id' => 'required|exists:lunch_breaks,id',
            'image' => 'nullable',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'distance' => 'nullable',
        ]);

        $break = LunchBreak::findOrFail($request->break_id);

        $breakUser = User::with('office')->findOrFail($break->user_id);

        if ($employee && (int) $employee->id !== (int) $breakUser->id) {
            abort(403, 'This break does not belong to selected employee.');
        }

        $this->ensureEmployeeAllowed($request, $breakUser);

        if ($break->end_time) {
            return response()->json([
                'success' => false,
                'message' => 'This break is already ended.',
                'data' => $break,
            ], 400);
        }

        try {
            $break->update([
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

            return response()->json([
                'success' => true,
                'message' => 'Break ended successfully',
                'data' => $break->fresh(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to end break. Please try again!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function latestBreak(Request $request, User $employee = null)
    {
        if ($employee) {
            $user = $employee;
        } else {
            $user = $request->user();
        }

        $user->loadMissing('office');

        $this->ensureEmployeeAllowed($request, $user);

        $break = LunchBreak::whereDate('created_at', Carbon::today())
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($break) {
            return response()->json([
                'success' => true,
                'data' => $break,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'No lunch break record found.',
        ], 404);
    }

    public function employeeBreak(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'date' => 'nullable|date',
        ]);

        $employee = User::with('office')->findOrFail($validated['employee_id']);

        $this->ensureEmployeeAllowed($request, $employee);

        $date = isset($validated['date'])
            ? Carbon::parse($validated['date'])->toDateString()
            : today()->toDateString();

        $breaks = LunchBreak::where('user_id', $employee->id)
            ->whereDate('created_at', $date)
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'date' => $date,
            'employee' => $employee,
            'breaks' => $breaks,
        ], 200);
    }
}