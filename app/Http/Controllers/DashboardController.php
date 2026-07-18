<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Leave;
use App\Models\LunchBreak;
use App\Models\Off;
use App\Models\Office;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /**
     * Employee page me jo active office resolution use ho raha hai,
     * dashboard me bhi bilkul wahi logic rakha gaya hai.
     */
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

        if (! $user) {
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
            return in_array($activeOfficeId, $switchableOfficeIds, true)
                ? [$activeOfficeId]
                : [];
        }

        return $switchableOfficeIds;
    }

    /**
     * IMPORTANT:
     * Ye query EmployeeController ke officeEmployeesQuery + default active status
     * ke bilkul same hai. Isi se employee page aur dashboard ka total same rahega.
     */
    private function dashboardEmployeesQuery(Request $request): Builder
    {
        $officeIds = $this->allowedOfficeIds($request);

        return User::query()
            ->with([
                'office:id,name',
                'department:id,name',
                'roles:id,name',
            ])
            ->when(
                ! empty($officeIds),
                fn (Builder $query) => $query->whereIn('office_id', $officeIds),
                fn (Builder $query) => $query->whereRaw('1 = 0')
            )
            ->where('status', '1');
    }

    private function sortEmployeesHierarchically(Collection $employees): Collection
    {
        $employees = collect($employees);
        $grouped = $employees->groupBy('team_leader_id');
        $sorted = collect();

        $appendChildren = function ($leaderId) use (&$appendChildren, $grouped, &$sorted): void {
            if (! isset($grouped[$leaderId])) {
                return;
            }

            foreach ($grouped[$leaderId]->sortBy('name') as $employee) {
                if (! $sorted->contains('id', $employee->id)) {
                    $sorted->push($employee);
                    $appendChildren($employee->id);
                }
            }
        };

        if (isset($grouped[null])) {
            foreach ($grouped[null]->sortBy('name') as $employee) {
                if (! $sorted->contains('id', $employee->id)) {
                    $sorted->push($employee);
                    $appendChildren($employee->id);
                }
            }
        }

        foreach ($employees->whereNotIn('id', $sorted->pluck('id'))->sortBy('name') as $employee) {
            if (! $sorted->contains('id', $employee->id)) {
                $sorted->push($employee);
                $appendChildren($employee->id);
            }
        }

        return $sorted->unique('id')->values();
    }

    private function monthlySummary(User $user, array $officeIds): array
    {
        $monthStart = now()->startOfMonth()->startOfDay();
        $periodEnd = now()->endOfDay();

        $days = $monthStart->diffInDays($periodEnd->copy()->startOfDay()) + 1;
        $sundays = 0;

        for ($date = $monthStart->copy(); $date->lte($periodEnd); $date->addDay()) {
            if ($date->isSunday()) {
                $sundays++;
            }
        }

        $offDates = Off::query()
            ->when(
                ! empty($officeIds),
                fn (Builder $query) => $query->whereIn('office_id', $officeIds),
                fn (Builder $query) => $query->whereRaw('1 = 0')
            )
            ->whereDate('date', '>=', $monthStart->toDateString())
            ->whereDate('date', '<=', $periodEnd->toDateString())
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->unique();

        $nonSundayOffs = $offDates
            ->reject(fn ($date) => Carbon::parse($date)->isSunday())
            ->count();

        $records = AttendanceRecord::query()
            ->where('user_id', $user->id)
            ->whereNotNull('check_in')
            ->whereBetween('check_in', [$monthStart, $periodEnd])
            ->get(['check_in'])
            ->map(fn ($record) => Carbon::parse($record->check_in)->toDateString())
            ->unique()
            ->count();

        $workingDays = max(0, $days - $sundays - $nonSundayOffs);

        return [
            'days' => $days,
            'sundays' => $sundays,
            'offs' => $nonSundayOffs,
            'working_days' => $workingDays,
            'records' => $records,
            'attendance_percentage' => $workingDays > 0
                ? round(($records / $workingDays) * 100, 1)
                : 0,
        ];
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();
        $activeOfficeId = $this->activeOfficeId($request);
        $allowedOfficeIds = $this->allowedOfficeIds($request);

        /*
         * Employee page jaisa exact employee result:
         * office filter + status = 1
         */
        $employees = $this->dashboardEmployeesQuery($request)->get();
        $employees = $this->sortEmployeesHierarchically($employees);

        $employeeIds = $employees
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $offices = Office::query()
            ->when(
                ! empty($allowedOfficeIds),
                fn (Builder $query) => $query->whereIn('id', $allowedOfficeIds),
                fn (Builder $query) => $query->whereRaw('1 = 0')
            )
            ->orderBy('name')
            ->get();

        $todayAttendanceRecord = AttendanceRecord::query()
            ->where('user_id', $user->id)
            ->where(function (Builder $query) use ($today) {
                $query->whereDate('check_in', $today)
                    ->orWhere(function (Builder $subQuery) use ($today) {
                        $subQuery->whereNull('check_in')
                            ->whereDate('created_at', $today);
                    });
            })
            ->latest('id')
            ->first();

        $break = $todayAttendanceRecord
            ? LunchBreak::query()
                ->where('attendance_record_id', $todayAttendanceRecord->id)
                ->latest('id')
                ->first()
            : null;

        /*
         * Present count employee IDs par hota hai.
         * Ek employee ki multiple entries ho tab bhi ek hi present count hoga.
         * Normal attendance me check_in date use hogi.
         * Purane/manual record me check_in null ho to created_at fallback hoga.
         */
        $todayCheckIn = $employeeIds->isEmpty()
            ? collect()
            : AttendanceRecord::query()
                ->with([
                    'user:id,name,email,phone,office_id,department_id,photo',
                    'user.office:id,name',
                    'user.department:id,name',
                ])
                ->whereIn('user_id', $employeeIds->all())
                ->where(function (Builder $query) use ($today) {
                    $query->whereDate('check_in', $today)
                        ->orWhere(function (Builder $subQuery) use ($today) {
                            $subQuery->whereNull('check_in')
                                ->whereDate('created_at', $today);
                        });
                })
                ->orderByDesc('id')
                ->get()
                ->unique('user_id')
                ->values();

        $leaves = $employeeIds->isEmpty()
            ? collect()
            : Leave::query()
                ->with('user:id,name,email,phone,office_id,department_id,photo')
                ->whereIn('user_id', $employeeIds->all())
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->whereRaw('LOWER(status) = ?', ['approved'])
                ->latest('id')
                ->get()
                ->unique('user_id')
                ->values();

        $presentIds = $todayCheckIn
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $leaveIds = $leaves
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        // Present employee ko leave ya absent me dobara count nahi karna hai.
        $effectiveLeaveIds = $leaveIds->diff($presentIds)->values();
        $absentIds = $employeeIds
            ->diff($presentIds)
            ->diff($effectiveLeaveIds)
            ->values();

        $totalEmployees = $employeeIds->count();
        $presentEmployees = $presentIds->count();
        $onLeaveEmployees = $effectiveLeaveIds->count();
        $absentEmployees = $absentIds->count();

        $checkedOutEmployees = $todayCheckIn
            ->filter(fn ($record) => ! empty($record->check_out))
            ->count();

        $currentlyWorkingEmployees = $todayCheckIn
            ->filter(fn ($record) => empty($record->check_out))
            ->count();

        $lateEmployees = $todayCheckIn
            ->filter(fn ($record) => (bool) ($record->is_late ?? $record->late ?? false))
            ->count();

        $attendancePercentage = $totalEmployees > 0
            ? round(($presentEmployees / $totalEmployees) * 100, 1)
            : 0;

        $absentEmployeesList = $employees
            ->whereIn('id', $absentIds->all())
            ->values();

        $onLeaveEmployeesList = $employees
            ->whereIn('id', $effectiveLeaveIds->all())
            ->values();

        $lastMonth = now()->subMonthNoOverflow();

        $lastMonthPayouts = $employeeIds->isEmpty()
            ? collect()
            : Salary::query()
                ->with('user:id,name,email,phone,office_id')
                ->whereIn('user_id', $employeeIds->all())
                ->whereYear('month', $lastMonth->year)
                ->whereMonth('month', $lastMonth->month)
                ->latest('id')
                ->get();

        $totalLastMonthPayout = $lastMonthPayouts->sum(function ($salary) {
            return (float) (
                $salary->net_salary
                ?? $salary->paid_amount
                ?? $salary->amount
                ?? 0
            );
        });

        $data = $this->monthlySummary($user, $allowedOfficeIds);

        return view('dashboard.dashboard', compact(
            'user',
            'offices',
            'employees',
            'employeeIds',
            'todayAttendanceRecord',
            'break',
            'todayCheckIn',
            'leaves',
            'lastMonthPayouts',
            'allowedOfficeIds',
            'activeOfficeId',
            'totalEmployees',
            'presentEmployees',
            'onLeaveEmployees',
            'absentEmployees',
            'attendancePercentage',
            'checkedOutEmployees',
            'currentlyWorkingEmployees',
            'lateEmployees',
            'totalLastMonthPayout',
            'data',
            'absentEmployeesList',
            'onLeaveEmployeesList'
        ));
    }


    public function currentMonth($startDate, $endDate, User $user): array
{
    $startDate = Carbon::parse($startDate)->startOfDay();
    $endDate = Carbon::parse($endDate)->endOfDay();

    // Future dates को current time तक सीमित रखें
    if ($endDate->greaterThan(now())) {
        $endDate = now()->endOfDay();
    }

    /*
    |--------------------------------------------------------------------------
    | User के office IDs
    |--------------------------------------------------------------------------
    */

    if ($user->hasRole('super_admin')) {
        $officeIds = Office::query()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    } elseif ($user->hasRole('owner')) {
        $officeIds = Office::query()
            ->where('owner_id', $user->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    } elseif ($user->office_id) {
        $officeIds = [(int) $user->office_id];
    } else {
        $officeIds = [];
    }

    /*
    |--------------------------------------------------------------------------
    | Total calendar days
    |--------------------------------------------------------------------------
    */

    $totalDays = $startDate
        ->copy()
        ->startOfDay()
        ->diffInDays($endDate->copy()->startOfDay()) + 1;

    /*
    |--------------------------------------------------------------------------
    | Sundays
    |--------------------------------------------------------------------------
    */

    $sundays = 0;

    for (
        $date = $startDate->copy();
        $date->lte($endDate);
        $date->addDay()
    ) {
        if ($date->isSunday()) {
            $sundays++;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Office holidays
    |--------------------------------------------------------------------------
    */

    $offDates = Off::query()
        ->when(
            !empty($officeIds),
            fn (Builder $query) => $query->whereIn('office_id', $officeIds),
            fn (Builder $query) => $query->whereRaw('1 = 0')
        )
        ->whereDate('date', '>=', $startDate->toDateString())
        ->whereDate('date', '<=', $endDate->toDateString())
        ->pluck('date')
        ->map(fn ($date) => Carbon::parse($date)->toDateString())
        ->unique();

    // Sunday को दोबारा holiday में count नहीं करेंगे
    $nonSundayOffs = $offDates
        ->reject(fn ($date) => Carbon::parse($date)->isSunday())
        ->count();

    /*
    |--------------------------------------------------------------------------
    | User attendance records
    |--------------------------------------------------------------------------
    */

    $attendanceRecords = AttendanceRecord::query()
        ->where('user_id', $user->id)
        ->where(function (Builder $query) use ($startDate, $endDate) {
            $query
                ->whereBetween('check_in', [$startDate, $endDate])
                ->orWhere(function (Builder $subQuery) use ($startDate, $endDate) {
                    $subQuery
                        ->whereNull('check_in')
                        ->whereBetween('created_at', [$startDate, $endDate]);
                });
        })
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Unique present days
    |--------------------------------------------------------------------------
    */

    $presentDays = $attendanceRecords
        ->map(function ($record) {
            $attendanceDate = $record->check_in ?? $record->created_at;

            return $attendanceDate
                ? Carbon::parse($attendanceDate)->toDateString()
                : null;
        })
        ->filter()
        ->unique()
        ->count();

    /*
    |--------------------------------------------------------------------------
    | Checked out, late and working counts
    |--------------------------------------------------------------------------
    */

    $checkedOutDays = $attendanceRecords
        ->filter(fn ($record) => !empty($record->check_out))
        ->count();

    $currentlyWorking = $attendanceRecords
        ->filter(function ($record) {
            return !empty($record->check_in)
                && empty($record->check_out);
        })
        ->count();

    $lateDays = $attendanceRecords
        ->filter(function ($record) {
            return (bool) ($record->is_late ?? $record->late ?? false);
        })
        ->count();

    /*
    |--------------------------------------------------------------------------
    | Approved leaves
    |--------------------------------------------------------------------------
    */

    $approvedLeaves = Leave::query()
        ->where('user_id', $user->id)
        ->whereDate('start_date', '<=', $endDate->toDateString())
        ->whereDate('end_date', '>=', $startDate->toDateString())
        ->whereRaw('LOWER(status) = ?', ['approved'])
        ->get();

    $leaveDates = collect();

    foreach ($approvedLeaves as $leave) {
        $leaveStart = Carbon::parse($leave->start_date)->startOfDay();
        $leaveEnd = Carbon::parse($leave->end_date)->startOfDay();

        if ($leaveStart->lt($startDate)) {
            $leaveStart = $startDate->copy();
        }

        if ($leaveEnd->gt($endDate)) {
            $leaveEnd = $endDate->copy();
        }

        for (
            $date = $leaveStart->copy();
            $date->lte($leaveEnd);
            $date->addDay()
        ) {
            if (!$date->isSunday()) {
                $leaveDates->push($date->toDateString());
            }
        }
    }

    $leaveDays = $leaveDates->unique()->count();

    /*
    |--------------------------------------------------------------------------
    | Final calculation
    |--------------------------------------------------------------------------
    */

    $workingDays = max(
        0,
        $totalDays - $sundays - $nonSundayOffs
    );

    $absentDays = max(
        0,
        $workingDays - $presentDays - $leaveDays
    );

    $attendancePercentage = $workingDays > 0
        ? round(($presentDays / $workingDays) * 100, 1)
        : 0;

    return [
        'start_date'            => $startDate->toDateString(),
        'end_date'              => $endDate->toDateString(),
        'days'                  => $totalDays,
        'total_days'            => $totalDays,
        'sundays'               => $sundays,
        'offs'                  => $nonSundayOffs,
        'working_days'          => $workingDays,
        'records'               => $presentDays,
        'present_days'          => $presentDays,
        'leave_days'            => $leaveDays,
        'absent_days'           => $absentDays,
        'late_days'             => $lateDays,
        'checked_out_records'   => $checkedOutDays,
        'currently_working'     => $currentlyWorking,
        'attendance_percentage' => $attendancePercentage,
    ];
}
}