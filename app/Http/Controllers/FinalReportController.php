<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Leave;
use App\Models\Off;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\Office;
use Illuminate\Database\Eloquent\Builder;



class FinalReportController extends Controller
{

    // public function index(Request $request)
    // {
    //     $fromMonth = $request->filled('from_month')
    //         ? $request->from_month
    //         : Carbon::now()->format('Y-m');

    //     $toMonth = $request->filled('to_month')
    //         ? $request->to_month
    //         : Carbon::now()->format('Y-m');

    //     $startOfMonth = Carbon::parse($fromMonth . '-01')->startOfMonth()->startOfDay();
    //     $endOfMonth   = Carbon::parse($toMonth . '-01')->endOfMonth()->endOfDay();

    //     if ($startOfMonth->gt($endOfMonth)) {
    //         [$startOfMonth, $endOfMonth] = [$endOfMonth, $startOfMonth];
    //         [$fromMonth, $toMonth] = [$toMonth, $fromMonth];
    //     }

    //     $dates = collect();
    //     $loopDate = $startOfMonth->copy();

    //     while ($loopDate->lte($endOfMonth)) {
    //         $dates->push((object)[
    //             'date' => $loopDate->copy(),
    //         ]);
    //         $loopDate->addDay();
    //     }

    //     $monthGroups = $dates->groupBy(function ($item) {
    //         return $item->date->format('Y-m');
    //     })->map(function ($monthDates, $monthKey) {
    //         $officeDays = 0;
    //         $sundayCount = 0;

    //         foreach ($monthDates as $dateObj) {
    //             $d = Carbon::parse($dateObj->date);

    //             if ($d->isSunday()) {
    //                 $sundayCount++;
    //             } else {
    //                 $officeDays++;
    //             }
    //         }

    //         return (object)[
    //             'month_key'    => $monthKey,
    //             'month_label'  => Carbon::parse($monthKey . '-01')->format('M-Y'),
    //             'dates'        => $monthDates->values(),
    //             'officeDays'   => $officeDays,
    //             'sundayCount'  => $sundayCount,
    //         ];
    //     })->values();

    //     // office-wise + hierarchical employee list
    //     $allEmployees = collect(HomeController::employeeList())->values();

    //     $users = $allEmployees;

    //     if ($request->filled('status')) {
    //         $users = $users->where('status', $request->status);
    //     } else {
    //         $users = $users->where('status', '1');
    //     }

    //     if ($request->filled('department_id')) {
    //         $users = $users->where('department_id', $request->department_id);
    //     }

    //     if ($request->filled('employee_id')) {
    //         $users = $users->where('id', $request->employee_id);
    //     }

    //     $users = $users->values();

    //     $employeeIds = $users->pluck('id')->filter()->unique()->values();
    //     $officeIds   = $users->pluck('office_id')->filter()->unique()->values();

    //     // Attendance preload
    //     $attendanceRecords = AttendanceRecord::query()
    //         ->when($employeeIds->isNotEmpty(), function ($q) use ($employeeIds) {
    //             $q->whereIn('user_id', $employeeIds);
    //         }, function ($q) {
    //             $q->whereRaw('1 = 0');
    //         })
    //         ->whereBetween('check_in', [$startOfMonth, $endOfMonth])
    //         ->get();

    //     $attendanceMap = $attendanceRecords->keyBy(function ($record) {
    //         return $record->user_id . '_' . Carbon::parse($record->check_in)->format('Y-m-d');
    //     });

    //     // Leaves preload
    //     $leaves = Leave::query()
    //         ->when($employeeIds->isNotEmpty(), function ($q) use ($employeeIds) {
    //             $q->whereIn('user_id', $employeeIds);
    //         }, function ($q) {
    //             $q->whereRaw('1 = 0');
    //         })
    //         ->whereDate('start_date', '<=', $endOfMonth->toDateString())
    //         ->whereDate('end_date', '>=', $startOfMonth->toDateString())
    //         ->get();

    //     $leaveMap = collect();

    //     foreach ($leaves as $leave) {
    //         $leaveStart = Carbon::parse($leave->start_date)->startOfDay();
    //         $leaveEnd   = Carbon::parse($leave->end_date)->endOfDay();

    //         if ($leaveStart->lt($startOfMonth)) {
    //             $leaveStart = $startOfMonth->copy();
    //         }

    //         if ($leaveEnd->gt($endOfMonth)) {
    //             $leaveEnd = $endOfMonth->copy();
    //         }

    //         $cursor = $leaveStart->copy();

    //         while ($cursor->lte($leaveEnd)) {
    //             $leaveMap->put(
    //                 $leave->user_id . '_' . $cursor->format('Y-m-d'),
    //                 $leave
    //             );
    //             $cursor->addDay();
    //         }
    //     }

    //     // Office offs preload
    //     $offs = Off::query()
    //         ->when($officeIds->isNotEmpty(), function ($q) use ($officeIds) {
    //             $q->whereIn('office_id', $officeIds);
    //         }, function ($q) {
    //             $q->whereRaw('1 = 0');
    //         })
    //         ->where('is_off', '1')
    //         ->whereDate('date', '>=', $startOfMonth->toDateString())
    //         ->whereDate('date', '<=', $endOfMonth->toDateString())
    //         ->get();

    //     $offMap = $offs->keyBy(function ($off) {
    //         return $off->office_id . '_' . Carbon::parse($off->date)->format('Y-m-d');
    //     });

    //     $departments = Department::all();

    //     return view('dashboard.finalReport.index1', [
    //         'dates'             => $dates,
    //         'monthGroups'       => $monthGroups,
    //         'attendanceRecords' => $attendanceRecords,
    //         'attendanceMap'     => $attendanceMap,
    //         'leaveMap'          => $leaveMap,
    //         'offMap'            => $offMap,
    //         'users'             => $users,
    //         'allEmployees'      => $allEmployees,
    //         'fromMonth'         => $fromMonth,
    //         'toMonth'           => $toMonth,
    //         'departments'       => $departments,
    //     ]);
    // }




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
    | Allowed office IDs
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
    | Selected office
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
    | Basic employee query
    |--------------------------------------------------------------------------
    */

    private function employeeBaseQuery(): Builder
    {
        return User::query()
            ->select([
                'id',
                'name',
                'email',
                'phone',
                'status',
                'office_id',
                'department_id',
                'team_leader_id',
                'check_in_time',
                'check_out_time',
            ])
            ->with([
                'office:id,name',
                'department:id,name',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Safe nested team IDs
    |--------------------------------------------------------------------------
    |
    | Recursive Collection push/prepend का इस्तेमाल नहीं है।
    | Circular hierarchy visited IDs से रुक जाएगी।
    |
    */

    private function nestedTeamIds(
        int $leaderId,
        ?int $officeId
    ): array {
        $result = [$leaderId];
        $visited = [$leaderId => true];
        $currentLeaderIds = [$leaderId];

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

            $nextLevelIds = [];

            foreach ($childIds as $childId) {
                if (
                    $childId <= 0
                    || isset($visited[$childId])
                ) {
                    continue;
                }

                $visited[$childId] = true;
                $result[] = $childId;
                $nextLevelIds[] = $childId;
            }

            $currentLeaderIds = $nextLevelIds;
        }

        return array_values(array_unique($result));
    }

    /*
    |--------------------------------------------------------------------------
    | Users allowed for report
    |--------------------------------------------------------------------------
    */

    private function reportUsers(Request $request): Collection
    {
        $loggedInUser = $request->user();

        if (!$loggedInUser) {
            return collect();
        }

        $officeId = $this->selectedOfficeId($request);

        /*
         * Normal employee केवल खुद।
         */
        if (
            !$loggedInUser->hasAnyRole([
                'super_admin',
                'owner',
                'admin',
                'team_leader',
            ])
        ) {
            return $this->employeeBaseQuery()
                ->whereKey($loggedInUser->id)
                ->get()
                ->map(function ($employee) {
                    $employee->hierarchy_level = 0;

                    return $employee;
                });
        }

        /*
         * Team leader खुद और पूरी nested hierarchy।
         */
        if ($loggedInUser->hasRole('team_leader')) {
            $employeeIds = $this->nestedTeamIds(
                (int) $loggedInUser->id,
                $officeId
            );

            $employees = $this->employeeBaseQuery()
                ->whereIn('id', $employeeIds)
                ->where('status', '1')
                ->get();

            return $this->sortUsersHierarchically(
                $employees,
                (int) $loggedInUser->id
            );
        }

        /*
         * Management selected office के सभी employees।
         */
        if (!$officeId) {
            return collect();
        }

        $employees = $this->employeeBaseQuery()
            ->where('office_id', $officeId)
            ->get();

        return $this->sortUsersHierarchically($employees);
    }

    /*
    |--------------------------------------------------------------------------
    | Safe hierarchy sorting
    |--------------------------------------------------------------------------
    */

    private function sortUsersHierarchically(
        Collection $employees,
        ?int $forcedRootId = null
    ): Collection {
        $employees = $employees
            ->filter(fn ($employee) => !empty($employee->id))
            ->unique('id')
            ->values();

        if ($employees->isEmpty()) {
            return collect();
        }

        $employeeById = $employees->keyBy(
            fn ($employee) => (int) $employee->id
        );

        $childrenByLeader = [];

        foreach ($employees as $employee) {
            $employeeId = (int) $employee->id;

            $leaderId = !empty($employee->team_leader_id)
                ? (int) $employee->team_leader_id
                : 0;

            /*
             * Self-reference को root बना दें।
             */
            if ($leaderId === $employeeId) {
                $leaderId = 0;
            }

            /*
             * Current result में leader मौजूद नहीं है तो root।
             */
            if (
                $leaderId > 0
                && !$employeeById->has($leaderId)
            ) {
                $leaderId = 0;
            }

            $childrenByLeader[$leaderId][] = $employee;
        }

        /*
         * हर hierarchy group में alphabetical sorting।
         */
        foreach ($childrenByLeader as &$children) {
            usort(
                $children,
                fn ($first, $second) => strcasecmp(
                    (string) ($first->name ?? ''),
                    (string) ($second->name ?? '')
                )
            );
        }

        unset($children);

        $result = [];
        $visited = [];
        $processing = [];

        $appendEmployee = function (
            $employee,
            int $level
        ) use (
            &$appendEmployee,
            &$result,
            &$visited,
            &$processing,
            $childrenByLeader
        ): void {
            $employeeId = (int) $employee->id;

            /*
             * पहले से output में मौजूद।
             */
            if (isset($visited[$employeeId])) {
                return;
            }

            /*
             * Circular hierarchy।
             */
            if (isset($processing[$employeeId])) {
                return;
            }

            $processing[$employeeId] = true;

            $employee->hierarchy_level = $level;

            /*
             * Array append Collection push की तुलना में lightweight है।
             */
            $result[] = $employee;
            $visited[$employeeId] = true;

            foreach (
                $childrenByLeader[$employeeId] ?? []
                as $child
            ) {
                $appendEmployee($child, $level + 1);
            }

            unset($processing[$employeeId]);
        };

        /*
         * Team leader report में logged-in leader सबसे पहले।
         */
        if (
            $forcedRootId
            && $employeeById->has($forcedRootId)
        ) {
            $appendEmployee(
                $employeeById->get($forcedRootId),
                0
            );
        }

        /*
         * सामान्य root employees।
         */
        foreach ($childrenByLeader[0] ?? [] as $rootEmployee) {
            $appendEmployee($rootEmployee, 0);
        }

        /*
         * Broken/circular hierarchy वाले बचे employees।
         */
        foreach ($employees as $employee) {
            $employeeId = (int) $employee->id;

            if (!isset($visited[$employeeId])) {
                $appendEmployee($employee, 0);
            }
        }

        return collect($result)->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Report page
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Month validation
        |--------------------------------------------------------------------------
        */

        $currentMonth = now()->format('Y-m');

        try {
            $fromMonth = $request->filled('from_month')
                ? Carbon::createFromFormat(
                    'Y-m',
                    $request->input('from_month')
                )->format('Y-m')
                : $currentMonth;
        } catch (\Throwable $exception) {
            $fromMonth = $currentMonth;
        }

        try {
            $toMonth = $request->filled('to_month')
                ? Carbon::createFromFormat(
                    'Y-m',
                    $request->input('to_month')
                )->format('Y-m')
                : $currentMonth;
        } catch (\Throwable $exception) {
            $toMonth = $currentMonth;
        }

        $startOfMonth = Carbon::createFromFormat(
            'Y-m',
            $fromMonth
        )
            ->startOfMonth()
            ->startOfDay();

        $endOfMonth = Carbon::createFromFormat(
            'Y-m',
            $toMonth
        )
            ->endOfMonth()
            ->endOfDay();

        if ($startOfMonth->gt($endOfMonth)) {
            [$startOfMonth, $endOfMonth] = [
                $endOfMonth->copy()->startOfMonth()->startOfDay(),
                $startOfMonth->copy()->endOfMonth()->endOfDay(),
            ];

            [$fromMonth, $toMonth] = [
                $toMonth,
                $fromMonth,
            ];
        }

        /*
         * बहुत बड़ा report request server timeout कर सकता है।
         * अधिकतम 12 महीने।
         */
        $monthDifference = $startOfMonth->diffInMonths(
            $endOfMonth
        );

        if ($monthDifference > 11) {
            $endOfMonth = $startOfMonth
                ->copy()
                ->addMonths(11)
                ->endOfMonth()
                ->endOfDay();

            $toMonth = $endOfMonth->format('Y-m');
        }

        /*
        |--------------------------------------------------------------------------
        | Date collection
        |--------------------------------------------------------------------------
        */

        $dateItems = [];

        $cursor = $startOfMonth->copy();

        while ($cursor->lte($endOfMonth)) {
            $dateItems[] = (object) [
                'date' => $cursor->copy(),
            ];

            $cursor->addDay();
        }

        $dates = collect($dateItems);

        /*
        |--------------------------------------------------------------------------
        | Month groups
        |--------------------------------------------------------------------------
        */

        $monthGroups = $dates
            ->groupBy(
                fn ($item) => $item->date->format('Y-m')
            )
            ->map(function ($monthDates, $monthKey) {
                $sundayCount = $monthDates
                    ->filter(
                        fn ($item) => $item->date->isSunday()
                    )
                    ->count();

                return (object) [
                    'month_key' => $monthKey,
                    'month_label' => Carbon::createFromFormat(
                        'Y-m',
                        $monthKey
                    )->format('M-Y'),
                    'dates' => $monthDates->values(),
                    'officeDays' => $monthDates->count() - $sundayCount,
                    'sundayCount' => $sundayCount,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Allowed users with hierarchy
        |--------------------------------------------------------------------------
        */

        $allEmployees = $this->reportUsers($request);

        $users = $allEmployees;

        /*
        |--------------------------------------------------------------------------
        | Status filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $selectedStatus = (string) $request->input('status');

            $users = $users->filter(
                fn ($employee) =>
                    (string) ($employee->status ?? '')
                    === $selectedStatus
            );
        } else {
            $users = $users->filter(
                fn ($employee) =>
                    (string) ($employee->status ?? '') === '1'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Department filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('department_id')) {
            $departmentId = (int) $request->input(
                'department_id'
            );

            $users = $users->filter(
                fn ($employee) =>
                    (int) ($employee->department_id ?? 0)
                    === $departmentId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Employee filter with access validation
        |--------------------------------------------------------------------------
        */

        if ($request->filled('employee_id')) {
            $employeeId = (int) $request->input('employee_id');

            $allowedEmployeeIds = $allEmployees
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            if (
                in_array(
                    $employeeId,
                    $allowedEmployeeIds,
                    true
                )
            ) {
                $users = $users->filter(
                    fn ($employee) =>
                        (int) $employee->id === $employeeId
                );
            } else {
                $users = collect();
            }
        }

        $users = $users->values();

        $employeeIds = $users
            ->pluck('id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $officeIds = $users
            ->pluck('office_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Attendance records
        |--------------------------------------------------------------------------
        */

        $attendanceRecords = AttendanceRecord::query()
            ->select([
                'id',
                'user_id',
                'check_in',
                'check_out',
                'duration',
                'late',
                'day_type',
            ])
            ->when(
                $employeeIds->isNotEmpty(),
                fn ($query) => $query->whereIn(
                    'user_id',
                    $employeeIds->all()
                ),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->where(function ($query) use (
                $startOfMonth,
                $endOfMonth
            ) {
                $query
                    ->whereBetween('check_in', [
                        $startOfMonth,
                        $endOfMonth,
                    ])
                    ->orWhereBetween('check_out', [
                        $startOfMonth,
                        $endOfMonth,
                    ]);
            })
            ->orderBy('user_id')
            ->orderBy('check_in')
            ->get();

        /*
         * Same user/date पर multiple records हों तो latest ID रखेंगे।
         */
        $attendanceMap = $attendanceRecords
            ->sortByDesc('id')
            ->unique(function ($record) {
                $attendanceDate = !empty($record->check_in)
                    ? Carbon::parse($record->check_in)->format('Y-m-d')
                    : Carbon::parse($record->check_out)->format('Y-m-d');

                return $record->user_id . '_' . $attendanceDate;
            })
            ->keyBy(function ($record) {
                $attendanceDate = !empty($record->check_in)
                    ? Carbon::parse($record->check_in)->format('Y-m-d')
                    : Carbon::parse($record->check_out)->format('Y-m-d');

                return $record->user_id . '_' . $attendanceDate;
            });

        /*
        |--------------------------------------------------------------------------
        | Leaves
        |--------------------------------------------------------------------------
        */

        $leaves = Leave::query()
            ->select([
                'id',
                'user_id',
                'office_id',
                'leave_type',
                'is_paid',
                'start_date',
                'end_date',
                'status',
                'day_count',
            ])
            ->when(
                $employeeIds->isNotEmpty(),
                fn ($query) => $query->whereIn(
                    'user_id',
                    $employeeIds->all()
                ),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->where('start_date', '<=', $endOfMonth->toDateString())
            ->where('end_date', '>=', $startOfMonth->toDateString())
            ->orderBy('start_date')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Leave map
        |--------------------------------------------------------------------------
        */

        $leaveMapArray = [];

        foreach ($leaves as $leave) {
            try {
                $leaveStart = Carbon::parse(
                    $leave->start_date
                )->startOfDay();

                $leaveEnd = Carbon::parse(
                    $leave->end_date ?? $leave->start_date
                )->startOfDay();
            } catch (\Throwable $exception) {
                continue;
            }

            if ($leaveStart->lt($startOfMonth)) {
                $leaveStart = $startOfMonth->copy();
            }

            if ($leaveEnd->gt($endOfMonth)) {
                $leaveEnd = $endOfMonth->copy()->startOfDay();
            }

            if ($leaveStart->gt($leaveEnd)) {
                continue;
            }

            $leaveCursor = $leaveStart->copy();

            while ($leaveCursor->lte($leaveEnd)) {
                $leaveMapArray[
                    $leave->user_id
                    . '_'
                    . $leaveCursor->format('Y-m-d')
                ] = $leave;

                $leaveCursor->addDay();
            }
        }

        $leaveMap = collect($leaveMapArray);

        /*
        |--------------------------------------------------------------------------
        | Office off days
        |--------------------------------------------------------------------------
        */

        $offs = Off::query()
            ->select([
                'id',
                'office_id',
                'date',
                'is_off',
            ])
            ->when(
                $officeIds->isNotEmpty(),
                fn ($query) => $query->whereIn(
                    'office_id',
                    $officeIds->all()
                ),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->where('is_off', '1')
            ->whereBetween('date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString(),
            ])
            ->get();

        $offMap = $offs->keyBy(function ($off) {
            return $off->office_id
                . '_'
                . Carbon::parse($off->date)->format('Y-m-d');
        });

        /*
        |--------------------------------------------------------------------------
        | Departments
        |--------------------------------------------------------------------------
        */

        $departments = Department::query()
            ->select([
                'id',
                'name',
            ])
            ->orderBy('name')
            ->get();

        return view('dashboard.finalReport.index1', [
            'dates' => $dates,
            'monthGroups' => $monthGroups,
            'attendanceRecords' => $attendanceRecords,
            'attendanceMap' => $attendanceMap,
            'leaveMap' => $leaveMap,
            'offMap' => $offMap,
            'users' => $users,
            'allEmployees' => $allEmployees,
            'fromMonth' => $fromMonth,
            'toMonth' => $toMonth,
            'departments' => $departments,
        ]);
    }
}
