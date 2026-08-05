<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Leave;
use App\Models\Off;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Throwable;

class FinalReportController extends Controller
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

        if (method_exists($user, 'activeOfficeId')) {
            $activeOfficeId = $user->activeOfficeId();

            if ($activeOfficeId) {
                return (int) $activeOfficeId;
            }
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
    | Allowed offices
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
    | Employee base query
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
    | Database-level iterative traversal.
    | Circular hierarchy आने पर visited IDs loop रोक देंगे।
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
                ->whereIn(
                    'team_leader_id',
                    $currentLeaderIds
                )
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
    | Safe iterative hierarchy sorting
    |--------------------------------------------------------------------------
    |
    | इसमें recursive closure, Collection push और prepend नहीं है।
    |
    */

    private function sortUsersHierarchically(
        Collection $employees,
        ?int $forcedRootId = null
    ): Collection {
        $employees = $employees
            ->filter(function ($employee) {
                return !empty($employee->id);
            })
            ->unique('id')
            ->values();

        if ($employees->isEmpty()) {
            return collect();
        }

        $employeeById = [];

        foreach ($employees as $employee) {
            $employeeById[(int) $employee->id] = $employee;
        }

        $childrenByLeader = [];

        foreach ($employees as $employee) {
            $employeeId = (int) $employee->id;

            $leaderId = !empty($employee->team_leader_id)
                ? (int) $employee->team_leader_id
                : 0;

            /*
             * Self-reference employee को root बनाएंगे।
             */
            if ($leaderId === $employeeId) {
                $leaderId = 0;
            }

            /*
             * Current employee list में leader नहीं है तो root।
             */
            if (
                $leaderId > 0
                && !isset($employeeById[$leaderId])
            ) {
                $leaderId = 0;
            }

            if (!isset($childrenByLeader[$leaderId])) {
                $childrenByLeader[$leaderId] = [];
            }

            $childrenByLeader[$leaderId][] = $employee;
        }

        /*
         * हर hierarchy level पर name sorting।
         */
        foreach ($childrenByLeader as &$children) {
            usort(
                $children,
                function ($first, $second) {
                    return strcasecmp(
                        (string) ($first->name ?? ''),
                        (string) ($second->name ?? '')
                    );
                }
            );
        }

        unset($children);

        $result = [];
        $visited = [];

        $processRoot = function (
            $rootEmployee,
            int $rootLevel = 0
        ) use (
            &$result,
            &$visited,
            $childrenByLeader
        ): void {
            $stack = [
                [
                    'employee' => $rootEmployee,
                    'level' => $rootLevel,
                ],
            ];

            while (!empty($stack)) {
                $current = array_pop($stack);

                $employee = $current['employee'];
                $level = (int) $current['level'];
                $employeeId = (int) $employee->id;

                if (isset($visited[$employeeId])) {
                    continue;
                }

                $visited[$employeeId] = true;

                /*
                 * Blade में hierarchy indentation के लिए।
                 */
                $employee->hierarchy_level = $level;

                $result[] = $employee;

                $children = $childrenByLeader[$employeeId] ?? [];

                /*
                 * Stack LIFO है, इसलिए reverse order में push।
                 */
                for (
                    $index = count($children) - 1;
                    $index >= 0;
                    $index--
                ) {
                    $child = $children[$index];
                    $childId = (int) $child->id;

                    if (isset($visited[$childId])) {
                        continue;
                    }

                    $stack[] = [
                        'employee' => $child,
                        'level' => $level + 1,
                    ];
                }
            }
        };

        /*
         * Team leader report में logged-in leader सबसे ऊपर।
         */
        if (
            $forcedRootId
            && isset($employeeById[$forcedRootId])
        ) {
            $processRoot(
                $employeeById[$forcedRootId],
                0
            );
        }

        /*
         * Normal roots।
         */
        foreach ($childrenByLeader[0] ?? [] as $rootEmployee) {
            $processRoot($rootEmployee, 0);
        }

        /*
         * Circular/broken hierarchy में छूटे employees।
         */
        foreach ($employees as $employee) {
            $employeeId = (int) $employee->id;

            if (!isset($visited[$employeeId])) {
                $processRoot($employee, 0);
            }
        }

        return collect($result)->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Visible report users
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
         * Normal employee केवल अपना report देखेगा।
         */
        if (
            !$loggedInUser->hasAnyRole([
                'super_admin',
                'owner',
                'admin',
                'team_leader',
            ])
        ) {
            $employee = $this->employeeBaseQuery()
                ->whereKey($loggedInUser->id)
                ->first();

            if (!$employee) {
                return collect();
            }

            $employee->hierarchy_level = 0;

            return collect([$employee]);
        }

        /*
         * Team leader: खुद + पूरी nested team।
         */
        if ($loggedInUser->hasRole('team_leader')) {
            if (
                !$officeId
                || (int) $loggedInUser->office_id
                    !== (int) $officeId
            ) {
                return collect();
            }

            $employeeIds = $this->nestedTeamIds(
                (int) $loggedInUser->id,
                $officeId
            );

            $employees = $this->employeeBaseQuery()
                ->whereIn('id', $employeeIds)
                ->where('office_id', $officeId)
                ->where('status', '1')
                ->get();

            return $this->sortUsersHierarchically(
                $employees,
                (int) $loggedInUser->id
            );
        }

        /*
         * Management: selected office के employees।
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
                    (string) $request->input('from_month')
                )->format('Y-m')
                : $currentMonth;
        } catch (Throwable $exception) {
            $fromMonth = $currentMonth;
        }

        try {
            $toMonth = $request->filled('to_month')
                ? Carbon::createFromFormat(
                    'Y-m',
                    (string) $request->input('to_month')
                )->format('Y-m')
                : $currentMonth;
        } catch (Throwable $exception) {
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

        /*
         * Reverse range को सही करें।
         */
        if ($startOfMonth->gt($endOfMonth)) {
            $originalStart = $startOfMonth->copy();
            $originalEnd = $endOfMonth->copy();

            $startOfMonth = $originalEnd
                ->startOfMonth()
                ->startOfDay();

            $endOfMonth = $originalStart
                ->endOfMonth()
                ->endOfDay();

            [$fromMonth, $toMonth] = [
                $toMonth,
                $fromMonth,
            ];
        }

        /*
         * Server protection: अधिकतम 12 महीने।
         */
        if (
            $startOfMonth->diffInMonths($endOfMonth)
            > 11
        ) {
            $endOfMonth = $startOfMonth
                ->copy()
                ->addMonths(11)
                ->endOfMonth()
                ->endOfDay();

            $toMonth = $endOfMonth->format('Y-m');
        }

        /*
        |--------------------------------------------------------------------------
        | Report dates
        |--------------------------------------------------------------------------
        */

        $dateItems = [];
        $dateCursor = $startOfMonth->copy();

        while ($dateCursor->lte($endOfMonth)) {
            $dateItems[] = (object) [
                'date' => $dateCursor->copy(),
            ];

            $dateCursor->addDay();
        }

        $dates = collect($dateItems);

        /*
        |--------------------------------------------------------------------------
        | Month groups
        |--------------------------------------------------------------------------
        */

        $monthGroups = $dates
            ->groupBy(function ($item) {
                return $item->date->format('Y-m');
            })
            ->map(function ($monthDates, $monthKey) {
                $sundayCount = 0;

                foreach ($monthDates as $dateItem) {
                    if ($dateItem->date->isSunday()) {
                        $sundayCount++;
                    }
                }

                return (object) [
                    'month_key' => $monthKey,
                    'month_label' => Carbon::createFromFormat(
                        'Y-m',
                        $monthKey
                    )->format('M-Y'),
                    'dates' => $monthDates->values(),
                    'officeDays' =>
                        $monthDates->count() - $sundayCount,
                    'sundayCount' => $sundayCount,
                ];
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Visible employees
        |--------------------------------------------------------------------------
        */

        $allEmployees = $this->reportUsers($request);
        $users = $allEmployees;

        /*
        |--------------------------------------------------------------------------
        | Status filter
        |--------------------------------------------------------------------------
        */

        $selectedStatus = $request->filled('status')
            ? (string) $request->input('status')
            : '1';

        $users = $users
            ->filter(function ($employee) use ($selectedStatus) {
                return (string) ($employee->status ?? '')
                    === $selectedStatus;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Department filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('department_id')) {
            $departmentId = (int) $request->input(
                'department_id'
            );

            $users = $users
                ->filter(function ($employee) use (
                    $departmentId
                ) {
                    return (int) (
                        $employee->department_id ?? 0
                    ) === $departmentId;
                })
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | Employee filter with hierarchy authorization
        |--------------------------------------------------------------------------
        */

        if ($request->filled('employee_id')) {
            $employeeId = (int) $request->input(
                'employee_id'
            );

            $allowedEmployeeIds = $allEmployees
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();

            if (
                in_array(
                    $employeeId,
                    $allowedEmployeeIds,
                    true
                )
            ) {
                $users = $users
                    ->filter(function ($employee) use (
                        $employeeId
                    ) {
                        return (int) $employee->id
                            === $employeeId;
                    })
                    ->values();
            } else {
                $users = collect();
            }
        }

        $employeeIds = $users
            ->pluck('id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $officeIds = $users
            ->pluck('office_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Attendance records
        |--------------------------------------------------------------------------
        */

        $attendanceQuery = AttendanceRecord::query()
            ->select([
                'id',
                'user_id',
                'check_in',
                'check_out',
                'duration',
                'late',
                'day_type',
            ])
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
            });

        if (empty($employeeIds)) {
            $attendanceQuery->whereRaw('1 = 0');
        } else {
            $attendanceQuery->whereIn(
                'user_id',
                $employeeIds
            );
        }

        $attendanceRecords = $attendanceQuery
            ->orderBy('user_id')
            ->orderBy('check_in')
            ->get();

        /*
         * Latest attendance per employee/date map।
         */
        $attendanceMapArray = [];

        foreach ($attendanceRecords as $record) {
            try {
                $recordDate = !empty($record->check_in)
                    ? Carbon::parse(
                        $record->check_in
                    )->format('Y-m-d')
                    : Carbon::parse(
                        $record->check_out
                    )->format('Y-m-d');
            } catch (Throwable $exception) {
                continue;
            }

            $mapKey = (int) $record->user_id
                . '_'
                . $recordDate;

            if (
                !isset($attendanceMapArray[$mapKey])
                || (int) $record->id
                    > (int) $attendanceMapArray[$mapKey]->id
            ) {
                $attendanceMapArray[$mapKey] = $record;
            }
        }

        $attendanceMap = collect($attendanceMapArray);

        /*
        |--------------------------------------------------------------------------
        | Leave records
        |--------------------------------------------------------------------------
        */

        $leaveQuery = Leave::query()
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
            ->where(
                'start_date',
                '<=',
                $endOfMonth->toDateString()
            )
            ->where(
                'end_date',
                '>=',
                $startOfMonth->toDateString()
            )
            ->orderBy('start_date');

        if (empty($employeeIds)) {
            $leaveQuery->whereRaw('1 = 0');
        } else {
            $leaveQuery->whereIn(
                'user_id',
                $employeeIds
            );
        }

        $leaves = $leaveQuery->get();

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
                    $leave->end_date
                    ?? $leave->start_date
                )->startOfDay();
            } catch (Throwable $exception) {
                continue;
            }

            if ($leaveStart->lt($startOfMonth)) {
                $leaveStart = $startOfMonth
                    ->copy()
                    ->startOfDay();
            }

            if ($leaveEnd->gt($endOfMonth)) {
                $leaveEnd = $endOfMonth
                    ->copy()
                    ->startOfDay();
            }

            if ($leaveStart->gt($leaveEnd)) {
                continue;
            }

            $leaveCursor = $leaveStart->copy();

            while ($leaveCursor->lte($leaveEnd)) {
                $leaveMapArray[
                    (int) $leave->user_id
                    . '_'
                    . $leaveCursor->format('Y-m-d')
                ] = $leave;

                $leaveCursor->addDay();
            }
        }

        $leaveMap = collect($leaveMapArray);

        /*
        |--------------------------------------------------------------------------
        | Office off records
        |--------------------------------------------------------------------------
        */

        $offQuery = Off::query()
            ->select([
                'id',
                'office_id',
                'date',
                'is_off',
            ])
            ->where('is_off', '1')
            ->whereBetween('date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString(),
            ]);

        if (empty($officeIds)) {
            $offQuery->whereRaw('1 = 0');
        } else {
            $offQuery->whereIn(
                'office_id',
                $officeIds
            );
        }

        $offs = $offQuery->get();

        $offMapArray = [];

        foreach ($offs as $off) {
            try {
                $offDate = Carbon::parse(
                    $off->date
                )->format('Y-m-d');
            } catch (Throwable $exception) {
                continue;
            }

            $offMapArray[
                (int) $off->office_id
                . '_'
                . $offDate
            ] = $off;
        }

        $offMap = collect($offMapArray);

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