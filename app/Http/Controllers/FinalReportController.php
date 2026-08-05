<?php

// namespace App\Http\Controllers;

// use App\Models\AttendanceRecord;
// use App\Models\Department;
// use App\Models\Leave;
// use App\Models\Off;
// use App\Models\User;
// use Carbon\Carbon;
// use Illuminate\Http\Request;
// use Illuminate\Support\Collection;
// use App\Models\Office;
// use Illuminate\Database\Eloquent\Builder;



// class FinalReportController extends Controller
// {

//     // public function index(Request $request)
//     // {
//     //     $fromMonth = $request->filled('from_month')
//     //         ? $request->from_month
//     //         : Carbon::now()->format('Y-m');

//     //     $toMonth = $request->filled('to_month')
//     //         ? $request->to_month
//     //         : Carbon::now()->format('Y-m');

//     //     $startOfMonth = Carbon::parse($fromMonth . '-01')->startOfMonth()->startOfDay();
//     //     $endOfMonth   = Carbon::parse($toMonth . '-01')->endOfMonth()->endOfDay();

//     //     if ($startOfMonth->gt($endOfMonth)) {
//     //         [$startOfMonth, $endOfMonth] = [$endOfMonth, $startOfMonth];
//     //         [$fromMonth, $toMonth] = [$toMonth, $fromMonth];
//     //     }

//     //     $dates = collect();
//     //     $loopDate = $startOfMonth->copy();

//     //     while ($loopDate->lte($endOfMonth)) {
//     //         $dates->push((object)[
//     //             'date' => $loopDate->copy(),
//     //         ]);
//     //         $loopDate->addDay();
//     //     }

//     //     $monthGroups = $dates->groupBy(function ($item) {
//     //         return $item->date->format('Y-m');
//     //     })->map(function ($monthDates, $monthKey) {
//     //         $officeDays = 0;
//     //         $sundayCount = 0;

//     //         foreach ($monthDates as $dateObj) {
//     //             $d = Carbon::parse($dateObj->date);

//     //             if ($d->isSunday()) {
//     //                 $sundayCount++;
//     //             } else {
//     //                 $officeDays++;
//     //             }
//     //         }

//     //         return (object)[
//     //             'month_key'    => $monthKey,
//     //             'month_label'  => Carbon::parse($monthKey . '-01')->format('M-Y'),
//     //             'dates'        => $monthDates->values(),
//     //             'officeDays'   => $officeDays,
//     //             'sundayCount'  => $sundayCount,
//     //         ];
//     //     })->values();

//     //     // office-wise + hierarchical employee list
//     //     $allEmployees = collect(HomeController::employeeList())->values();

//     //     $users = $allEmployees;

//     //     if ($request->filled('status')) {
//     //         $users = $users->where('status', $request->status);
//     //     } else {
//     //         $users = $users->where('status', '1');
//     //     }

//     //     if ($request->filled('department_id')) {
//     //         $users = $users->where('department_id', $request->department_id);
//     //     }

//     //     if ($request->filled('employee_id')) {
//     //         $users = $users->where('id', $request->employee_id);
//     //     }

//     //     $users = $users->values();

//     //     $employeeIds = $users->pluck('id')->filter()->unique()->values();
//     //     $officeIds   = $users->pluck('office_id')->filter()->unique()->values();

//     //     // Attendance preload
//     //     $attendanceRecords = AttendanceRecord::query()
//     //         ->when($employeeIds->isNotEmpty(), function ($q) use ($employeeIds) {
//     //             $q->whereIn('user_id', $employeeIds);
//     //         }, function ($q) {
//     //             $q->whereRaw('1 = 0');
//     //         })
//     //         ->whereBetween('check_in', [$startOfMonth, $endOfMonth])
//     //         ->get();

//     //     $attendanceMap = $attendanceRecords->keyBy(function ($record) {
//     //         return $record->user_id . '_' . Carbon::parse($record->check_in)->format('Y-m-d');
//     //     });

//     //     // Leaves preload
//     //     $leaves = Leave::query()
//     //         ->when($employeeIds->isNotEmpty(), function ($q) use ($employeeIds) {
//     //             $q->whereIn('user_id', $employeeIds);
//     //         }, function ($q) {
//     //             $q->whereRaw('1 = 0');
//     //         })
//     //         ->whereDate('start_date', '<=', $endOfMonth->toDateString())
//     //         ->whereDate('end_date', '>=', $startOfMonth->toDateString())
//     //         ->get();

//     //     $leaveMap = collect();

//     //     foreach ($leaves as $leave) {
//     //         $leaveStart = Carbon::parse($leave->start_date)->startOfDay();
//     //         $leaveEnd   = Carbon::parse($leave->end_date)->endOfDay();

//     //         if ($leaveStart->lt($startOfMonth)) {
//     //             $leaveStart = $startOfMonth->copy();
//     //         }

//     //         if ($leaveEnd->gt($endOfMonth)) {
//     //             $leaveEnd = $endOfMonth->copy();
//     //         }

//     //         $cursor = $leaveStart->copy();

//     //         while ($cursor->lte($leaveEnd)) {
//     //             $leaveMap->put(
//     //                 $leave->user_id . '_' . $cursor->format('Y-m-d'),
//     //                 $leave
//     //             );
//     //             $cursor->addDay();
//     //         }
//     //     }

//     //     // Office offs preload
//     //     $offs = Off::query()
//     //         ->when($officeIds->isNotEmpty(), function ($q) use ($officeIds) {
//     //             $q->whereIn('office_id', $officeIds);
//     //         }, function ($q) {
//     //             $q->whereRaw('1 = 0');
//     //         })
//     //         ->where('is_off', '1')
//     //         ->whereDate('date', '>=', $startOfMonth->toDateString())
//     //         ->whereDate('date', '<=', $endOfMonth->toDateString())
//     //         ->get();

//     //     $offMap = $offs->keyBy(function ($off) {
//     //         return $off->office_id . '_' . Carbon::parse($off->date)->format('Y-m-d');
//     //     });

//     //     $departments = Department::all();

//     //     return view('dashboard.finalReport.index1', [
//     //         'dates'             => $dates,
//     //         'monthGroups'       => $monthGroups,
//     //         'attendanceRecords' => $attendanceRecords,
//     //         'attendanceMap'     => $attendanceMap,
//     //         'leaveMap'          => $leaveMap,
//     //         'offMap'            => $offMap,
//     //         'users'             => $users,
//     //         'allEmployees'      => $allEmployees,
//     //         'fromMonth'         => $fromMonth,
//     //         'toMonth'           => $toMonth,
//     //         'departments'       => $departments,
//     //     ]);
//     // }




//     /*
//     |--------------------------------------------------------------------------
//     | Active office
//     |--------------------------------------------------------------------------
//     */

//     private function activeOfficeId(Request $request): ?int
//     {
//         $user = $request->user();

//         if (!$user) {
//             return null;
//         }

//         $sessionOfficeId = (int) $request->session()->get(
//             'active_office_id',
//             0
//         );

//         if ($sessionOfficeId > 0) {
//             return $sessionOfficeId;
//         }

//         if (!empty($user->office_id)) {
//             return (int) $user->office_id;
//         }

//         if ($user->hasRole('owner')) {
//             $officeId = Office::query()
//                 ->where('owner_id', $user->id)
//                 ->orderBy('id')
//                 ->value('id');

//             return $officeId ? (int) $officeId : null;
//         }

//         if ($user->hasRole('super_admin')) {
//             $officeId = Office::query()
//                 ->orderBy('id')
//                 ->value('id');

//             return $officeId ? (int) $officeId : null;
//         }

//         return null;
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | Allowed office IDs
//     |--------------------------------------------------------------------------
//     */

//     private function allowedOfficeIds(Request $request): array
//     {
//         $user = $request->user();

//         if (!$user) {
//             return [];
//         }

//         if ($user->hasRole('super_admin')) {
//             return Office::query()
//                 ->pluck('id')
//                 ->map(fn ($id) => (int) $id)
//                 ->values()
//                 ->all();
//         }

//         if ($user->hasRole('owner')) {
//             return Office::query()
//                 ->where('owner_id', $user->id)
//                 ->pluck('id')
//                 ->map(fn ($id) => (int) $id)
//                 ->values()
//                 ->all();
//         }

//         if (
//             $user->can('switch offices')
//             || $user->can('switch office')
//         ) {
//             $ownerId = null;

//             if (!empty($user->office_id)) {
//                 $ownerId = Office::query()
//                     ->whereKey($user->office_id)
//                     ->value('owner_id');
//             }

//             if ($ownerId) {
//                 return Office::query()
//                     ->where('owner_id', $ownerId)
//                     ->pluck('id')
//                     ->map(fn ($id) => (int) $id)
//                     ->values()
//                     ->all();
//             }
//         }

//         return !empty($user->office_id)
//             ? [(int) $user->office_id]
//             : [];
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | Selected office
//     |--------------------------------------------------------------------------
//     */

//     private function selectedOfficeId(Request $request): ?int
//     {
//         $activeOfficeId = $this->activeOfficeId($request);
//         $allowedOfficeIds = $this->allowedOfficeIds($request);

//         if (
//             $activeOfficeId
//             && in_array(
//                 (int) $activeOfficeId,
//                 $allowedOfficeIds,
//                 true
//             )
//         ) {
//             return (int) $activeOfficeId;
//         }

//         return !empty($allowedOfficeIds)
//             ? (int) $allowedOfficeIds[0]
//             : null;
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | Basic employee query
//     |--------------------------------------------------------------------------
//     */

//     private function employeeBaseQuery(): Builder
//     {
//         return User::query()
//             ->select([
//                 'id',
//                 'name',
//                 'email',
//                 'phone',
//                 'status',
//                 'office_id',
//                 'department_id',
//                 'team_leader_id',
//                 'check_in_time',
//                 'check_out_time',
//             ])
//             ->with([
//                 'office:id,name',
//                 'department:id,name',
//             ]);
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | Safe nested team IDs
//     |--------------------------------------------------------------------------
//     |
//     | Recursive Collection push/prepend का इस्तेमाल नहीं है।
//     | Circular hierarchy visited IDs से रुक जाएगी।
//     |
//     */

//     private function nestedTeamIds(
//         int $leaderId,
//         ?int $officeId
//     ): array {
//         $result = [$leaderId];
//         $visited = [$leaderId => true];
//         $currentLeaderIds = [$leaderId];

//         $level = 0;
//         $maximumLevels = 100;

//         while (
//             !empty($currentLeaderIds)
//             && $level < $maximumLevels
//         ) {
//             $level++;

//             $query = User::query()
//                 ->whereIn('team_leader_id', $currentLeaderIds)
//                 ->where('status', '1');

//             if ($officeId) {
//                 $query->where('office_id', $officeId);
//             }

//             $childIds = $query
//                 ->pluck('id')
//                 ->map(fn ($id) => (int) $id)
//                 ->values()
//                 ->all();

//             $nextLevelIds = [];

//             foreach ($childIds as $childId) {
//                 if (
//                     $childId <= 0
//                     || isset($visited[$childId])
//                 ) {
//                     continue;
//                 }

//                 $visited[$childId] = true;
//                 $result[] = $childId;
//                 $nextLevelIds[] = $childId;
//             }

//             $currentLeaderIds = $nextLevelIds;
//         }

//         return array_values(array_unique($result));
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | Users allowed for report
//     |--------------------------------------------------------------------------
//     */

//     private function reportUsers(Request $request): Collection
//     {
//         $loggedInUser = $request->user();

//         if (!$loggedInUser) {
//             return collect();
//         }

//         $officeId = $this->selectedOfficeId($request);

//         /*
//          * Normal employee केवल खुद।
//          */
//         if (
//             !$loggedInUser->hasAnyRole([
//                 'super_admin',
//                 'owner',
//                 'admin',
//                 'team_leader',
//             ])
//         ) {
//             return $this->employeeBaseQuery()
//                 ->whereKey($loggedInUser->id)
//                 ->get()
//                 ->map(function ($employee) {
//                     $employee->hierarchy_level = 0;

//                     return $employee;
//                 });
//         }

//         /*
//          * Team leader खुद और पूरी nested hierarchy।
//          */
//         if ($loggedInUser->hasRole('team_leader')) {
//             $employeeIds = $this->nestedTeamIds(
//                 (int) $loggedInUser->id,
//                 $officeId
//             );

//             $employees = $this->employeeBaseQuery()
//                 ->whereIn('id', $employeeIds)
//                 ->where('status', '1')
//                 ->get();

//             return $this->sortUsersHierarchically(
//                 $employees,
//                 (int) $loggedInUser->id
//             );
//         }

//         /*
//          * Management selected office के सभी employees।
//          */
//         if (!$officeId) {
//             return collect();
//         }

//         $employees = $this->employeeBaseQuery()
//             ->where('office_id', $officeId)
//             ->get();

//         return $this->sortUsersHierarchically($employees);
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | Safe hierarchy sorting
//     |--------------------------------------------------------------------------
//     */

//     private function sortUsersHierarchically(
//         Collection $employees,
//         ?int $forcedRootId = null
//     ): Collection {
//         $employees = $employees
//             ->filter(fn ($employee) => !empty($employee->id))
//             ->unique('id')
//             ->values();

//         if ($employees->isEmpty()) {
//             return collect();
//         }

//         $employeeById = $employees->keyBy(
//             fn ($employee) => (int) $employee->id
//         );

//         $childrenByLeader = [];

//         foreach ($employees as $employee) {
//             $employeeId = (int) $employee->id;

//             $leaderId = !empty($employee->team_leader_id)
//                 ? (int) $employee->team_leader_id
//                 : 0;

//             /*
//              * Self-reference को root बना दें।
//              */
//             if ($leaderId === $employeeId) {
//                 $leaderId = 0;
//             }

//             /*
//              * Current result में leader मौजूद नहीं है तो root।
//              */
//             if (
//                 $leaderId > 0
//                 && !$employeeById->has($leaderId)
//             ) {
//                 $leaderId = 0;
//             }

//             $childrenByLeader[$leaderId][] = $employee;
//         }

//         /*
//          * हर hierarchy group में alphabetical sorting।
//          */
//         foreach ($childrenByLeader as &$children) {
//             usort(
//                 $children,
//                 fn ($first, $second) => strcasecmp(
//                     (string) ($first->name ?? ''),
//                     (string) ($second->name ?? '')
//                 )
//             );
//         }

//         unset($children);

//         $result = [];
//         $visited = [];
//         $processing = [];

//         $appendEmployee = function (
//             $employee,
//             int $level
//         ) use (
//             &$appendEmployee,
//             &$result,
//             &$visited,
//             &$processing,
//             $childrenByLeader
//         ): void {
//             $employeeId = (int) $employee->id;

//             /*
//              * पहले से output में मौजूद।
//              */
//             if (isset($visited[$employeeId])) {
//                 return;
//             }

//             /*
//              * Circular hierarchy।
//              */
//             if (isset($processing[$employeeId])) {
//                 return;
//             }

//             $processing[$employeeId] = true;

//             $employee->hierarchy_level = $level;

//             /*
//              * Array append Collection push की तुलना में lightweight है।
//              */
//             $result[] = $employee;
//             $visited[$employeeId] = true;

//             foreach (
//                 $childrenByLeader[$employeeId] ?? []
//                 as $child
//             ) {
//                 $appendEmployee($child, $level + 1);
//             }

//             unset($processing[$employeeId]);
//         };

//         /*
//          * Team leader report में logged-in leader सबसे पहले।
//          */
//         if (
//             $forcedRootId
//             && $employeeById->has($forcedRootId)
//         ) {
//             $appendEmployee(
//                 $employeeById->get($forcedRootId),
//                 0
//             );
//         }

//         /*
//          * सामान्य root employees।
//          */
//         foreach ($childrenByLeader[0] ?? [] as $rootEmployee) {
//             $appendEmployee($rootEmployee, 0);
//         }

//         /*
//          * Broken/circular hierarchy वाले बचे employees।
//          */
//         foreach ($employees as $employee) {
//             $employeeId = (int) $employee->id;

//             if (!isset($visited[$employeeId])) {
//                 $appendEmployee($employee, 0);
//             }
//         }

//         return collect($result)->values();
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | Report page
//     |--------------------------------------------------------------------------
//     */

//     public function index(Request $request)
//     {
//         /*
//         |--------------------------------------------------------------------------
//         | Month validation
//         |--------------------------------------------------------------------------
//         */

//         $currentMonth = now()->format('Y-m');

//         try {
//             $fromMonth = $request->filled('from_month')
//                 ? Carbon::createFromFormat(
//                     'Y-m',
//                     $request->input('from_month')
//                 )->format('Y-m')
//                 : $currentMonth;
//         } catch (\Throwable $exception) {
//             $fromMonth = $currentMonth;
//         }

//         try {
//             $toMonth = $request->filled('to_month')
//                 ? Carbon::createFromFormat(
//                     'Y-m',
//                     $request->input('to_month')
//                 )->format('Y-m')
//                 : $currentMonth;
//         } catch (\Throwable $exception) {
//             $toMonth = $currentMonth;
//         }

//         $startOfMonth = Carbon::createFromFormat(
//             'Y-m',
//             $fromMonth
//         )
//             ->startOfMonth()
//             ->startOfDay();

//         $endOfMonth = Carbon::createFromFormat(
//             'Y-m',
//             $toMonth
//         )
//             ->endOfMonth()
//             ->endOfDay();

//         if ($startOfMonth->gt($endOfMonth)) {
//             [$startOfMonth, $endOfMonth] = [
//                 $endOfMonth->copy()->startOfMonth()->startOfDay(),
//                 $startOfMonth->copy()->endOfMonth()->endOfDay(),
//             ];

//             [$fromMonth, $toMonth] = [
//                 $toMonth,
//                 $fromMonth,
//             ];
//         }

//         /*
//          * बहुत बड़ा report request server timeout कर सकता है।
//          * अधिकतम 12 महीने।
//          */
//         $monthDifference = $startOfMonth->diffInMonths(
//             $endOfMonth
//         );

//         if ($monthDifference > 11) {
//             $endOfMonth = $startOfMonth
//                 ->copy()
//                 ->addMonths(11)
//                 ->endOfMonth()
//                 ->endOfDay();

//             $toMonth = $endOfMonth->format('Y-m');
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Date collection
//         |--------------------------------------------------------------------------
//         */

//         $dateItems = [];

//         $cursor = $startOfMonth->copy();

//         while ($cursor->lte($endOfMonth)) {
//             $dateItems[] = (object) [
//                 'date' => $cursor->copy(),
//             ];

//             $cursor->addDay();
//         }

//         $dates = collect($dateItems);

//         /*
//         |--------------------------------------------------------------------------
//         | Month groups
//         |--------------------------------------------------------------------------
//         */

//         $monthGroups = $dates
//             ->groupBy(
//                 fn ($item) => $item->date->format('Y-m')
//             )
//             ->map(function ($monthDates, $monthKey) {
//                 $sundayCount = $monthDates
//                     ->filter(
//                         fn ($item) => $item->date->isSunday()
//                     )
//                     ->count();

//                 return (object) [
//                     'month_key' => $monthKey,
//                     'month_label' => Carbon::createFromFormat(
//                         'Y-m',
//                         $monthKey
//                     )->format('M-Y'),
//                     'dates' => $monthDates->values(),
//                     'officeDays' => $monthDates->count() - $sundayCount,
//                     'sundayCount' => $sundayCount,
//                 ];
//             })
//             ->values();

//         /*
//         |--------------------------------------------------------------------------
//         | Allowed users with hierarchy
//         |--------------------------------------------------------------------------
//         */

//         $allEmployees = $this->reportUsers($request);

//         $users = $allEmployees;

//         /*
//         |--------------------------------------------------------------------------
//         | Status filter
//         |--------------------------------------------------------------------------
//         */

//         if ($request->filled('status')) {
//             $selectedStatus = (string) $request->input('status');

//             $users = $users->filter(
//                 fn ($employee) =>
//                     (string) ($employee->status ?? '')
//                     === $selectedStatus
//             );
//         } else {
//             $users = $users->filter(
//                 fn ($employee) =>
//                     (string) ($employee->status ?? '') === '1'
//             );
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Department filter
//         |--------------------------------------------------------------------------
//         */

//         if ($request->filled('department_id')) {
//             $departmentId = (int) $request->input(
//                 'department_id'
//             );

//             $users = $users->filter(
//                 fn ($employee) =>
//                     (int) ($employee->department_id ?? 0)
//                     === $departmentId
//             );
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Employee filter with access validation
//         |--------------------------------------------------------------------------
//         */

//         if ($request->filled('employee_id')) {
//             $employeeId = (int) $request->input('employee_id');

//             $allowedEmployeeIds = $allEmployees
//                 ->pluck('id')
//                 ->map(fn ($id) => (int) $id)
//                 ->all();

//             if (
//                 in_array(
//                     $employeeId,
//                     $allowedEmployeeIds,
//                     true
//                 )
//             ) {
//                 $users = $users->filter(
//                     fn ($employee) =>
//                         (int) $employee->id === $employeeId
//                 );
//             } else {
//                 $users = collect();
//             }
//         }

//         $users = $users->values();

//         $employeeIds = $users
//             ->pluck('id')
//             ->filter()
//             ->map(fn ($id) => (int) $id)
//             ->unique()
//             ->values();

//         $officeIds = $users
//             ->pluck('office_id')
//             ->filter()
//             ->map(fn ($id) => (int) $id)
//             ->unique()
//             ->values();

//         /*
//         |--------------------------------------------------------------------------
//         | Attendance records
//         |--------------------------------------------------------------------------
//         */

//         $attendanceRecords = AttendanceRecord::query()
//             ->select([
//                 'id',
//                 'user_id',
//                 'check_in',
//                 'check_out',
//                 'duration',
//                 'late',
//                 'day_type',
//             ])
//             ->when(
//                 $employeeIds->isNotEmpty(),
//                 fn ($query) => $query->whereIn(
//                     'user_id',
//                     $employeeIds->all()
//                 ),
//                 fn ($query) => $query->whereRaw('1 = 0')
//             )
//             ->where(function ($query) use (
//                 $startOfMonth,
//                 $endOfMonth
//             ) {
//                 $query
//                     ->whereBetween('check_in', [
//                         $startOfMonth,
//                         $endOfMonth,
//                     ])
//                     ->orWhereBetween('check_out', [
//                         $startOfMonth,
//                         $endOfMonth,
//                     ]);
//             })
//             ->orderBy('user_id')
//             ->orderBy('check_in')
//             ->get();

//         /*
//          * Same user/date पर multiple records हों तो latest ID रखेंगे।
//          */
//         $attendanceMap = $attendanceRecords
//             ->sortByDesc('id')
//             ->unique(function ($record) {
//                 $attendanceDate = !empty($record->check_in)
//                     ? Carbon::parse($record->check_in)->format('Y-m-d')
//                     : Carbon::parse($record->check_out)->format('Y-m-d');

//                 return $record->user_id . '_' . $attendanceDate;
//             })
//             ->keyBy(function ($record) {
//                 $attendanceDate = !empty($record->check_in)
//                     ? Carbon::parse($record->check_in)->format('Y-m-d')
//                     : Carbon::parse($record->check_out)->format('Y-m-d');

//                 return $record->user_id . '_' . $attendanceDate;
//             });

//         /*
//         |--------------------------------------------------------------------------
//         | Leaves
//         |--------------------------------------------------------------------------
//         */

//         $leaves = Leave::query()
//             ->select([
//                 'id',
//                 'user_id',
//                 'office_id',
//                 'leave_type',
//                 'is_paid',
//                 'start_date',
//                 'end_date',
//                 'status',
//                 'day_count',
//             ])
//             ->when(
//                 $employeeIds->isNotEmpty(),
//                 fn ($query) => $query->whereIn(
//                     'user_id',
//                     $employeeIds->all()
//                 ),
//                 fn ($query) => $query->whereRaw('1 = 0')
//             )
//             ->where('start_date', '<=', $endOfMonth->toDateString())
//             ->where('end_date', '>=', $startOfMonth->toDateString())
//             ->orderBy('start_date')
//             ->get();

//         /*
//         |--------------------------------------------------------------------------
//         | Leave map
//         |--------------------------------------------------------------------------
//         */

//         $leaveMapArray = [];

//         foreach ($leaves as $leave) {
//             try {
//                 $leaveStart = Carbon::parse(
//                     $leave->start_date
//                 )->startOfDay();

//                 $leaveEnd = Carbon::parse(
//                     $leave->end_date ?? $leave->start_date
//                 )->startOfDay();
//             } catch (\Throwable $exception) {
//                 continue;
//             }

//             if ($leaveStart->lt($startOfMonth)) {
//                 $leaveStart = $startOfMonth->copy();
//             }

//             if ($leaveEnd->gt($endOfMonth)) {
//                 $leaveEnd = $endOfMonth->copy()->startOfDay();
//             }

//             if ($leaveStart->gt($leaveEnd)) {
//                 continue;
//             }

//             $leaveCursor = $leaveStart->copy();

//             while ($leaveCursor->lte($leaveEnd)) {
//                 $leaveMapArray[
//                     $leave->user_id
//                     . '_'
//                     . $leaveCursor->format('Y-m-d')
//                 ] = $leave;

//                 $leaveCursor->addDay();
//             }
//         }

//         $leaveMap = collect($leaveMapArray);

//         /*
//         |--------------------------------------------------------------------------
//         | Office off days
//         |--------------------------------------------------------------------------
//         */

//         $offs = Off::query()
//             ->select([
//                 'id',
//                 'office_id',
//                 'date',
//                 'is_off',
//             ])
//             ->when(
//                 $officeIds->isNotEmpty(),
//                 fn ($query) => $query->whereIn(
//                     'office_id',
//                     $officeIds->all()
//                 ),
//                 fn ($query) => $query->whereRaw('1 = 0')
//             )
//             ->where('is_off', '1')
//             ->whereBetween('date', [
//                 $startOfMonth->toDateString(),
//                 $endOfMonth->toDateString(),
//             ])
//             ->get();

//         $offMap = $offs->keyBy(function ($off) {
//             return $off->office_id
//                 . '_'
//                 . Carbon::parse($off->date)->format('Y-m-d');
//         });

//         /*
//         |--------------------------------------------------------------------------
//         | Departments
//         |--------------------------------------------------------------------------
//         */

//         $departments = Department::query()
//             ->select([
//                 'id',
//                 'name',
//             ])
//             ->orderBy('name')
//             ->get();

//         return view('dashboard.finalReport.index1', [
//             'dates' => $dates,
//             'monthGroups' => $monthGroups,
//             'attendanceRecords' => $attendanceRecords,
//             'attendanceMap' => $attendanceMap,
//             'leaveMap' => $leaveMap,
//             'offMap' => $offMap,
//             'users' => $users,
//             'allEmployees' => $allEmployees,
//             'fromMonth' => $fromMonth,
//             'toMonth' => $toMonth,
//             'departments' => $departments,
//         ]);
//     }
// }




namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Office;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserSalary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class EmployeeController extends Controller
{


    // private function activeOfficeId(Request $request): ?int
    // {
    //     if ($request->session()->has('active_office_id')) {
    //         return (int) $request->session()->get('active_office_id');
    //     }

    //     return $request->user()?->office_id ? (int) $request->user()->office_id : null;
    // }

    // private function allowedOfficeIds(Request $request): array
    // {
    //     $user = $request->user();

    //     if (!$user) {
    //         return [];
    //     }

    //     $activeOfficeId = $this->activeOfficeId($request);

    //     if ($user->hasRole('super_admin')) {
    //         return $activeOfficeId ? [$activeOfficeId] : [];
    //     }

    //     if ($user->hasRole('owner')) {
    //         if (!$activeOfficeId) {
    //             return [];
    //         }

    //         $isOwnerOffice = Office::where('id', $activeOfficeId)
    //             ->where('owner_id', $user->id)
    //             ->exists();

    //         return $isOwnerOffice ? [$activeOfficeId] : [];
    //     }

    //     if ($user->hasRole('admin')) {
    //         return $user->office_id ? [(int) $user->office_id] : [];
    //     }

    //     if ($user->office_id) {
    //         return [(int) $user->office_id];
    //     }

    //     return [];
    // }

    // private function officeEmployeesQuery(Request $request)
    // {
    //     $officeIds = $this->allowedOfficeIds($request);

    //     return User::query()->when(!empty($officeIds), function ($q) use ($officeIds) {
    //         $q->whereIn('office_id', $officeIds);
    //     }, function ($q) {
    //         $q->whereRaw('1 = 0');
    //     });
    // }








private function hasSwitchOfficeAccess(User $user): bool
{
    return $user->hasRole('super_admin')
        || $user->hasRole('owner')
        || $user->can('switch offices')
        || $user->can('switch office');
}

/*
|--------------------------------------------------------------------------
| Active office ID
|--------------------------------------------------------------------------
*/

private function activeOfficeId(Request $request): ?int
{
    $user = $request->user();

    if (!$user) {
        return null;
    }

    $sessionOfficeId = (int) $request->session()->get('active_office_id', 0);

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
| Offices available to logged-in user
|--------------------------------------------------------------------------
*/

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

    if ($user->can('switch offices') || $user->can('switch office')) {
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

private function allowedOfficeIds(Request $request): array
{
    return $this->switchableOfficeIds($request);
}

/*
|--------------------------------------------------------------------------
| Selected office
|--------------------------------------------------------------------------
*/

private function selectedOfficeId(Request $request): ?int
{
    $allowedOfficeIds = $this->allowedOfficeIds($request);

    if (empty($allowedOfficeIds)) {
        return null;
    }

    if ($request->filled('office_id')) {
        $requestedOfficeId = (int) $request->input('office_id');

        if (in_array($requestedOfficeId, $allowedOfficeIds, true)) {
            return $requestedOfficeId;
        }
    }

    $activeOfficeId = $this->activeOfficeId($request);

    if (
        $activeOfficeId
        && in_array((int) $activeOfficeId, $allowedOfficeIds, true)
    ) {
        return (int) $activeOfficeId;
    }

    return (int) $allowedOfficeIds[0];
}

/*
|--------------------------------------------------------------------------
| Base employee query
|--------------------------------------------------------------------------
*/

private function employeeBaseQuery(): Builder
{
    return User::query()->with([
        'office',
        'department',
        'teamLeader',
    ]);
}

private function officeEmployeesQuery(Request $request): Builder
{
    $officeId = $this->selectedOfficeId($request);

    return $this->employeeBaseQuery()
        ->when(
            $officeId,
            fn (Builder $query) => $query->where('office_id', $officeId),
            fn (Builder $query) => $query->whereRaw('1 = 0')
        );
}

/*
|--------------------------------------------------------------------------
| Safe hierarchy sorting
|--------------------------------------------------------------------------
|
| One database query ke baad hierarchy memory me linear traversal se banti hai.
| visited IDs duplicate/circular hierarchy ko rok dete hain.
| Recursive database queries use nahi hoti, isliye execution-time risk kam hai.
|
*/

private function sortEmployeesHierarchically(
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

        if ($leaderId === $employeeId) {
            $leaderId = 0;
        }

        if ($leaderId > 0 && !$employeeById->has($leaderId)) {
            $leaderId = 0;
        }

        $childrenByLeader[$leaderId][] = $employee;
    }

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

    $appendTree = function ($rootEmployee, int $rootLevel = 0) use (
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
            $node = array_pop($stack);
            $employee = $node['employee'];
            $level = (int) $node['level'];
            $employeeId = (int) $employee->id;

            if ($employeeId <= 0 || isset($visited[$employeeId])) {
                continue;
            }

            $visited[$employeeId] = true;
            $employee->hierarchy_level = $level;
            $result[] = $employee;

            $children = $childrenByLeader[$employeeId] ?? [];

            for ($index = count($children) - 1; $index >= 0; $index--) {
                $stack[] = [
                    'employee' => $children[$index],
                    'level' => $level + 1,
                ];
            }
        }
    };

    if ($forcedRootId && $employeeById->has($forcedRootId)) {
        $appendTree($employeeById->get($forcedRootId), 0);
    }

    foreach ($childrenByLeader[0] ?? [] as $rootEmployee) {
        $appendTree($rootEmployee, 0);
    }

    foreach ($employees as $employee) {
        $employeeId = (int) $employee->id;

        if (!isset($visited[$employeeId])) {
            $appendTree($employee, 0);
        }
    }

    return collect($result)->values();
}

/*
|--------------------------------------------------------------------------
| Team leader nested hierarchy
|--------------------------------------------------------------------------
|
| Saare office employees ek baar load hote hain. Uske baad team IDs memory me
| breadth-first traversal se milti hain. Isme per-level DB query nahi chalti.
|
*/

private function teamHierarchyEmployees(
    Collection $officeEmployees,
    int $leaderId
): Collection {
    $officeEmployees = $officeEmployees
        ->filter(fn ($employee) => !empty($employee->id))
        ->unique('id')
        ->values();

    if ($officeEmployees->isEmpty()) {
        return collect();
    }

    $childrenByLeader = [];

    foreach ($officeEmployees as $employee) {
        $parentId = !empty($employee->team_leader_id)
            ? (int) $employee->team_leader_id
            : 0;

        $childrenByLeader[$parentId][] = (int) $employee->id;
    }

    $allowedIds = [];
    $visited = [];
    $queue = [$leaderId];
    $position = 0;

    while (isset($queue[$position])) {
        $employeeId = (int) $queue[$position];
        $position++;

        if ($employeeId <= 0 || isset($visited[$employeeId])) {
            continue;
        }

        $visited[$employeeId] = true;
        $allowedIds[$employeeId] = true;

        foreach ($childrenByLeader[$employeeId] ?? [] as $childId) {
            if (!isset($visited[$childId])) {
                $queue[] = $childId;
            }
        }
    }

    $teamEmployees = $officeEmployees
        ->filter(
            fn ($employee) => isset($allowedIds[(int) $employee->id])
        )
        ->values();

    return $this->sortEmployeesHierarchically(
        $teamEmployees,
        $leaderId
    );
}

/*
|--------------------------------------------------------------------------
| Employees visible to logged-in user
|--------------------------------------------------------------------------
|
| employee    => sirf khud
| team_leader => khud + poori nested team
| management  => selected office ke sabhi employees
|
*/

private function visibleEmployees(
    Request $request,
    bool $activeOnly = false
): Collection {
    $loggedInUser = $request->user();

    if (!$loggedInUser) {
        return collect();
    }

    $managementRoles = [
        'super_admin',
        'owner',
        'admin',
        'team_leader',
    ];

    if (!$loggedInUser->hasAnyRole($managementRoles)) {
        $query = $this->employeeBaseQuery()
            ->whereKey($loggedInUser->id);

        if ($activeOnly) {
            $query->where('status', '1');
        }

        return $query
            ->get()
            ->map(function ($employee) {
                $employee->hierarchy_level = 0;

                return $employee;
            });
    }

    $officeId = $this->selectedOfficeId($request);

    if (!$officeId) {
        return collect();
    }

    $query = $this->employeeBaseQuery()
        ->where('office_id', $officeId);

    if ($activeOnly) {
        $query->where('status', '1');
    }

    $officeEmployees = $query->get();

    if ($loggedInUser->hasRole('team_leader')) {
        return $this->teamHierarchyEmployees(
            $officeEmployees,
            (int) $loggedInUser->id
        );
    }

    return $this->sortEmployeesHierarchically($officeEmployees);
}



    // public function index(Request $request)
    // {
    //     $query = $this->officeEmployeesQuery($request);

    //     if ($request->filled('q')) {
    //         $q = trim($request->q);
    //         $query->where(function ($qq) use ($q) {
    //             $qq->where('name', 'like', "%{$q}%")
    //                 ->orWhere('email', 'like', "%{$q}%")
    //                 ->orWhere('phone', 'like', "%{$q}%");
    //         });
    //     }

    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     } else {
    //         $query->where('status', '1');
    //     }

    //     if ($request->filled('department_id')) {
    //         $query->where('department_id', $request->department_id);
    //     }

    //     $allowedOfficeIds = $this->allowedOfficeIds($request);

    //     if ($request->filled('office_id')) {
    //         $requestedOfficeId = (int) $request->office_id;

    //         if (in_array($requestedOfficeId, $allowedOfficeIds, true)) {
    //             $query->where('office_id', $requestedOfficeId);
    //         } else {
    //             $query->whereRaw('1 = 0');
    //         }
    //     }

    //     if ($request->filled('office_unassigned') && $request->office_unassigned == '1') {
    //         $query->whereNull('office_id');
    //     }

    //     $employees = $query->get();

    //     // hierarchy order preserve
    //     $employees = $this->sortEmployeesHierarchically($employees);

    //     $departments = Department::all();

    //     $offices = Office::when(!empty($allowedOfficeIds), function ($q) use ($allowedOfficeIds) {
    //             $q->whereIn('id', $allowedOfficeIds);
    //         })
    //         ->orderBy('name')
    //         ->get();

    //     $unassignedCount = (clone $this->officeEmployeesQuery($request))
    //         ->whereNull('office_id')
    //         ->count();

    //     $perPage = 25;
    //     $currentPage = Paginator::resolveCurrentPage();

    //     $paginatedEmployees = new LengthAwarePaginator(
    //         $employees->forPage($currentPage, $perPage)->values(),
    //         $employees->count(),
    //         $perPage,
    //         $currentPage,
    //         [
    //             'path' => Paginator::resolveCurrentPath(),
    //             'query' => $request->query(),
    //         ]
    //     );

    //     return view('dashboard.employee.index', [
    //         'employees' => $paginatedEmployees,
    //         'departments' => $departments,
    //         'offices' => $offices,
    //         'unassignedCount' => $unassignedCount,
    //     ]);
    // }


public function index(Request $request)
{
    $allowedOfficeIds = $this->allowedOfficeIds($request);

    /*
    |--------------------------------------------------------------------------
    | Role-wise visible employees
    |--------------------------------------------------------------------------
    */

    $employees = $this->visibleEmployees($request, false);

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($request->filled('q')) {
        $search = mb_strtolower(
            trim((string) $request->input('q'))
        );

        $employees = $employees->filter(function ($employee) use ($search) {
            return str_contains(
                mb_strtolower((string) ($employee->name ?? '')),
                $search
            )
                || str_contains(
                    mb_strtolower((string) ($employee->email ?? '')),
                    $search
                )
                || str_contains(
                    mb_strtolower((string) ($employee->phone ?? '')),
                    $search
                );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Status filter
    |--------------------------------------------------------------------------
    */

    $selectedStatus = $request->filled('status')
        ? (string) $request->input('status')
        : '1';

    $employees = $employees->filter(
        fn ($employee) =>
            (string) ($employee->status ?? '') === $selectedStatus
    );

    /*
    |--------------------------------------------------------------------------
    | Department filter
    |--------------------------------------------------------------------------
    */

    if ($request->filled('department_id')) {
        $departmentId = (int) $request->input('department_id');

        $employees = $employees->filter(
            fn ($employee) =>
                (int) ($employee->department_id ?? 0) === $departmentId
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Rebuild hierarchy after filters
    |--------------------------------------------------------------------------
    */

    $forcedRootId = $request->user()?->hasRole('team_leader')
        ? (int) $request->user()->id
        : null;

    $employees = $this->sortEmployeesHierarchically(
        $employees->values(),
        $forcedRootId
    );

    /*
    |--------------------------------------------------------------------------
    | Lightweight manual pagination
    |--------------------------------------------------------------------------
    */

    $perPage = 25;
    $currentPage = max(1, (int) Paginator::resolveCurrentPage());

    $paginatedEmployees = new LengthAwarePaginator(
        $employees
            ->slice(($currentPage - 1) * $perPage, $perPage)
            ->values(),
        $employees->count(),
        $perPage,
        $currentPage,
        [
            'path' => Paginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]
    );

    $departments = Department::query()
        ->orderBy('name')
        ->get();

    $offices = Office::query()
        ->when(
            !empty($allowedOfficeIds),
            fn ($query) => $query->whereIn('id', $allowedOfficeIds),
            fn ($query) => $query->whereRaw('1 = 0')
        )
        ->orderBy('name')
        ->get();

    $unassignedCount = $request->user()?->hasRole('super_admin')
        ? User::query()->whereNull('office_id')->count()
        : 0;

    return view('dashboard.employee.index', [
        'employees' => $paginatedEmployees,
        'departments' => $departments,
        'offices' => $offices,
        'unassignedCount' => $unassignedCount,
    ]);
}


    public function create(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('super_admin')) {
            $offices = Office::orderBy('name')->get();

            $teamLeaders = User::whereHas('roles', function ($q) {
                    $q->where('name', 'team_leader');
                })
                ->with('office')
                ->get();
        } elseif ($user->hasRole('owner')) {
            // owner ke saare offices lao
            $offices = Office::where('owner_id', $user->id)
                ->orderBy('name')
                ->get();

            if ($offices->isEmpty()) {
                return back()->with('error', 'No office found for this owner.');
            }

            // owner ke total employees count sab offices ka
            $officeIds = $offices->pluck('id');

            $plan = Plan::where('user_id', $user->id)->latest()->first();
            $employeeCount = User::whereIn('office_id', $officeIds)->count();

            if ($plan && $employeeCount >= $plan->number_of_employees) {
                return back()->with('error', 'Your employee creation limit exceeded!');
            }

            $teamLeaders = User::whereIn('office_id', $officeIds)
                ->role('team_leader')
                ->with('office')
                ->get();
        } else {
            $activeOfficeId = $user->activeOfficeId();

            if (!$activeOfficeId) {
                return back()->with('error', 'Please select an office first.');
            }

            $owner = optional($user->office)->owner;

            if ($owner) {
                $plan = Plan::where('user_id', $owner->id)->latest()->first();

                $employeeCount = User::where('office_id', $activeOfficeId)->count();

                if ($plan && $employeeCount >= $plan->number_of_employees) {
                    return back()->with('error', 'Your employee creation limit exceeded!');
                }
            }

            $offices = Office::where('id', $activeOfficeId)->orderBy('name')->get();

            $teamLeaders = User::where('office_id', $activeOfficeId)
                ->role('team_leader')
                ->with('office')
                ->get();
        }

        $departments = Department::all();

        return view('dashboard.employee.create', compact('offices', 'teamLeaders', 'departments'));
    }




    public function store(EmployeeRequest $request)
    {
        $user = $request->user();

        // =========================
        // OFFICE RESOLUTION
        // =========================
        if ($user->hasRole('super_admin')) {

            $targetOfficeId = $request->office_id;

            if (!$targetOfficeId) {
                return back()->with('error', 'Please select an office first.')->withInput();
            }

        } elseif ($user->hasRole('owner')) {

            // owner ke saare offices
            $ownerOfficeIds = Office::where('owner_id', $user->id)->pluck('id');

            if ($ownerOfficeIds->isEmpty()) {
                return back()->with('error', 'No office found for this owner.')->withInput();
            }

            // agar dropdown se office select ho raha hai to use lo, warna active
            $targetOfficeId = $request->office_id ?: $user->activeOfficeId();

            if (!$targetOfficeId || !$ownerOfficeIds->contains($targetOfficeId)) {
                return back()->with('error', 'Invalid office selected.')->withInput();
            }

            // ✅ PLAN CHECK (OWNER TOTAL EMPLOYEES)
            $plan = Plan::where('user_id', $user->id)->latest()->first();

            $employeeCount = User::whereIn('office_id', $ownerOfficeIds)->count();

            if ($plan && $employeeCount >= $plan->number_of_employees) {
                return back()->with('error', 'Your employee creation limit exceeded!')->withInput();
            }

        } else {

            $targetOfficeId = $user->activeOfficeId();

            if (!$targetOfficeId) {
                return back()->with('error', 'Please select an office first.')->withInput();
            }

            // owner निकालो
            $owner = optional($user->office)->owner;

            if ($owner) {
                $plan = Plan::where('user_id', $owner->id)->latest()->first();

                $employeeCount = User::where('office_id', $targetOfficeId)->count();

                if ($plan && $employeeCount >= $plan->number_of_employees) {
                    return back()->with('error', 'Your employee creation limit exceeded!')->withInput();
                }
            }
        }

        // =========================
        // EMAIL CHECK
        // =========================
        $existingEmployee = User::where('email', $request->email)->first();
        if ($existingEmployee) {
            return back()->withErrors(['email' => 'Email already exists.'])->withInput();
        }

        // =========================
        // TIME CALCULATION
        // =========================
        $checkInTime = Carbon::parse($request->check_in_time);
        $checkOutTime = Carbon::parse($request->check_out_time);

        // =========================
        // CREATE EMPLOYEE
        // =========================
        $employee = User::create($request->except([
            'joining_date',
            'office_id',
            'photo',
            'aadhar_attachment',
            'pan_attachment',
            'other_attachment',
            'role',
            'basic_salary',
            'house_rent_allowance',
            'transport_allowance',
            'medical_allowance',
            'special_allowance',
            'dearness_allowance',
            'relieving_charge',
            'additional_allowance',
        ]) + [
            'office_id' => $targetOfficeId,
            'password' => Hash::make('password'),
        ]);

        // =========================
        // FILE UPLOADS
        // =========================
        if ($request->file('photo')) {
            $file = $request->file('photo')->store('public/photos');
            $employee->photo = str_replace('public/', '', $file);
        }

        if ($request->file('aadhar_attachment')) {
            $file = $request->file('aadhar_attachment')->store('public/aadhar_attachments');
            $employee->aadhar_attachment = str_replace('public/', '', $file);
        }

        if ($request->file('pan_attachment')) {
            $file = $request->file('pan_attachment')->store('public/pan_attachments');
            $employee->pan_attachment = str_replace('public/', '', $file);
        }

        if ($request->file('other_attachment')) {
            $file = $request->file('other_attachment')->store('public/other_attachments');
            $employee->other_attachment = str_replace('public/', '', $file);
        }

        // =========================
        // SAVE EXTRA DATA
        // =========================
        $employee->office_time = $checkInTime->diffInMinutes($checkOutTime);
        $employee->joining_date = $request->joining_date;
        $employee->save();

        // =========================
        // ROLE ASSIGN
        // =========================
        $employee->assignRole($request->role ?? 'employee');

        // =========================
        // SALARY
        // =========================
        $basic_salary = $request->basic_salary ?? 0;
        $house_rent_allowance = $request->house_rent_allowance ?? 0;
        $transport_allowance = $request->transport_allowance ?? 0;
        $medical_allowance = $request->medical_allowance ?? 0;
        $special_allowance = $request->special_allowance ?? 0;
        $dearness_allowance = $request->dearness_allowance ?? 0;
        $relieving_charge = $request->relieving_charge ?? 0;
        $additional_allowance = $request->additional_allowance ?? 0;

        $total_salary =
            $basic_salary +
            $house_rent_allowance +
            $transport_allowance +
            $medical_allowance +
            $special_allowance +
            $dearness_allowance +
            $relieving_charge +
            $additional_allowance;

        UserSalary::create([
            'user_id' => $employee->id,
            'basic_salary' => $basic_salary,
            'house_rent_allowance' => $house_rent_allowance,
            'transport_allowance' => $transport_allowance,
            'medical_allowance' => $medical_allowance,
            'special_allowance' => $special_allowance,
            'dearness_allowance' => $dearness_allowance,
            'relieving_charge' => $relieving_charge,
            'additional_allowance' => $additional_allowance,
            'total_salary' => $total_salary,
        ]);

        return redirect('employee')->with('success', 'Employee Registered successfully');
    }


    public function edit(Request $request, User $employee)
    {
        $user = $request->user();

        if ($user->hasRole('super_admin')) {
            $offices = Office::orderBy('name')->get();

            $teamLeaders = User::whereHas('roles', function ($q) {
                    $q->where('name', 'team_leader');
                })
                ->with('office')
                ->get();

            // super admin kisi bhi office ke employee ko edit kar sakta hai
        } elseif ($user->hasRole('owner')) {
            $ownerOfficeIds = Office::where('owner_id', $user->id)->pluck('id');

            if ($ownerOfficeIds->isEmpty()) {
                return back()->with('error', 'No office found for this owner.');
            }

            $activeOfficeId = $user->activeOfficeId();

            if (!$activeOfficeId) {
                $activeOfficeId = $ownerOfficeIds->first();
            }

            // security: owner sirf apne office ke employee ko edit kare
            if (!$ownerOfficeIds->contains($employee->office_id)) {
                abort(403, 'This employee does not belong to your office.');
            }

            $offices = Office::whereIn('id', $ownerOfficeIds)
                ->orderBy('name')
                ->get();

            $teamLeaders = User::where('office_id', $activeOfficeId)
                ->role('team_leader')
                ->with('office')
                ->get();
        } else {
            $activeOfficeId = $user->activeOfficeId();

            if (!$activeOfficeId) {
                return back()->with('error', 'Please select an office first.');
            }

            if ((int) $employee->office_id !== (int) $activeOfficeId) {
                abort(403, 'This employee does not belong to the selected office.');
            }

            $offices = Office::where('id', $activeOfficeId)
                ->orderBy('name')
                ->get();

            $teamLeaders = User::where('office_id', $activeOfficeId)
                ->role('team_leader')
                ->with('office')
                ->get();
        }

        $departments = Department::all();

        return view('dashboard.employee.edit', compact('employee', 'offices', 'teamLeaders', 'departments'));
    }

    public function update(Request $request, User $employee)
    {
        $user = $request->user();
        // =========================
        // TARGET OFFICE RESOLUTION
        // =========================
        if ($user->hasRole('super_admin')) {

            $targetOfficeId = $request->office_id ?: $employee->office_id;

            if (!$targetOfficeId) {
                return back()->with('error', 'Please select an office first.')->withInput();
            }

        } elseif ($user->hasRole('owner')) {

            $ownerOfficeIds = Office::where('owner_id', $user->id)->pluck('id');

            if ($ownerOfficeIds->isEmpty()) {
                return back()->with('error', 'No office found for this owner.')->withInput();
            }

            // security: employee owner ke kisi office ka hi hona chahiye
            if (!$ownerOfficeIds->contains($employee->office_id)) {
                abort(403, 'This employee does not belong to your office.');
            }

            // owner dropdown se office change kar sakta hai
            $targetOfficeId = $request->office_id ?: $employee->office_id;

            if (!$ownerOfficeIds->contains($targetOfficeId)) {
                return back()->with('error', 'Invalid office selected.')->withInput();
            }

        } else {

            $targetOfficeId = $user->activeOfficeId();

            if (!$targetOfficeId) {
                return back()->with('error', 'Please select an office first.')->withInput();
            }

            if ((int) $employee->office_id !== (int) $targetOfficeId) {
                abort(403, 'This employee does not belong to the selected office.');
            }
        }

        // =========================
        // OPTIONAL EMAIL UNIQUE CHECK
        // =========================
        if ($request->filled('email')) {
            $emailExists = User::where('email', $request->email)
                ->where('id', '!=', $employee->id)
                ->exists();

            if ($emailExists) {
                return back()->withErrors(['email' => 'Email already exists.'])->withInput();
            }
        }

        // =========================
        // UPDATE BASIC FIELDS
        // =========================
        $employee->fill($request->except([
            'password',
            'photo',
            'aadhar_attachment',
            'pan_attachment',
            'other_attachment',
            'joining_date',
            'office_id',
            'role',
            'basic_salary',
            'house_rent_allowance',
            'transport_allowance',
            'medical_allowance',
            'special_allowance',
            'dearness_allowance',
            'relieving_charge',
            'additional_allowance',
            'provident_fund',
            'employee_state_insurance_corporation',
        ]));

        if ($request->filled('password')) {
            $employee->password = Hash::make($request->password);
        }

        // =========================
        // FILE UPLOADS
        // =========================
        if ($request->file('photo')) {
            if ($employee->photo) {
                $oldFile = public_path('storage/' . $employee->photo);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $file = $request->file('photo')->store('public/photos');
            $employee->photo = str_replace('public/', '', $file);
        }

        if ($request->file('aadhar_attachment')) {
            if ($employee->aadhar_attachment) {
                $oldFile = public_path('storage/' . $employee->aadhar_attachment);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $file = $request->file('aadhar_attachment')->store('public/aadhar_attachments');
            $employee->aadhar_attachment = str_replace('public/', '', $file);
        }

        if ($request->file('pan_attachment')) {
            if ($employee->pan_attachment) {
                $oldFile = public_path('storage/' . $employee->pan_attachment);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $file = $request->file('pan_attachment')->store('public/pan_attachments');
            $employee->pan_attachment = str_replace('public/', '', $file);
        }

        if ($request->file('other_attachment')) {
            if ($employee->other_attachment) {
                $oldFile = public_path('storage/' . $employee->other_attachment);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $file = $request->file('other_attachment')->store('public/other_attachments');
            $employee->other_attachment = str_replace('public/', '', $file);
        }

        // =========================
        // EXTRA FIELDS
        // =========================
        $employee->joining_date = $request->joining_date;
        $employee->office_id = $targetOfficeId;

        if ($request->filled('check_in_time') && $request->filled('check_out_time')) {
            $checkInTime = Carbon::parse($request->check_in_time);
            $checkOutTime = Carbon::parse($request->check_out_time);
            $employee->office_time = $checkInTime->diffInMinutes($checkOutTime);
        }

        if ($request->role) {
            $employee->syncRoles([$request->role]);
        }

        $employee->save();

        // =========================
        // SALARY DATA
        // =========================
        $basicSalary = $request->basic_salary ?? 0;
        $houseRentAllowance = $request->house_rent_allowance ?? 0;
        $transportAllowance = $request->transport_allowance ?? 0;
        $medicalAllowance = $request->medical_allowance ?? 0;
        $specialAllowance = $request->special_allowance ?? 0;
        $dearnessAllowance = $request->dearness_allowance ?? 0;
        $relievingCharge = $request->relieving_charge ?? 0;
        $additionalAllowance = $request->additional_allowance ?? 0;
        $providentFund = $request->provident_fund ?? 0;
        $esic = $request->employee_state_insurance_corporation ?? 0;

        $totalSalary = $basicSalary
            + $houseRentAllowance
            + $transportAllowance
            + $medicalAllowance
            + $specialAllowance
            + $dearnessAllowance
            + $relievingCharge
            + $additionalAllowance;

        $userSalary = UserSalary::where('user_id', $employee->id)->first();

        if ($userSalary) {
            $userSalary->update([
                'basic_salary' => $basicSalary,
                'house_rent_allowance' => $houseRentAllowance,
                'transport_allowance' => $transportAllowance,
                'medical_allowance' => $medicalAllowance,
                'special_allowance' => $specialAllowance,
                'dearness_allowance' => $dearnessAllowance,
                'relieving_charge' => $relievingCharge,
                'additional_allowance' => $additionalAllowance,
                'provident_fund' => $providentFund,
                'employee_state_insurance_corporation' => $esic,
                'total_salary' => $totalSalary,
            ]);
        } else {
            UserSalary::create([
                'user_id' => $employee->id,
                'basic_salary' => $basicSalary,
                'house_rent_allowance' => $houseRentAllowance,
                'transport_allowance' => $transportAllowance,
                'medical_allowance' => $medicalAllowance,
                'special_allowance' => $specialAllowance,
                'dearness_allowance' => $dearnessAllowance,
                'relieving_charge' => $relievingCharge,
                'additional_allowance' => $additionalAllowance,
                'provident_fund' => $providentFund,
                'employee_state_insurance_corporation' => $esic,
                'total_salary' => $totalSalary,
            ]);
        }

        return redirect('employee')->with('success', 'Record Updated successfully');
    }


    public function delete(Request $request, User $employee)
    {
        $activeOfficeId = $request->user()->activeOfficeId();

        if (!$activeOfficeId || (int) $employee->office_id !== (int) $activeOfficeId) {
            abort(403, 'This employee does not belong to the selected office.');
        }

        if ($employee->photo) {
            $file = public_path('storage/' . $employee->photo);
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $employee->delete();

        return back()->with('success', 'Record Deleted Successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | Attendance employee list
    |--------------------------------------------------------------------------
    |
    | Sirf active employees:
    | employee    => sirf khud
    | team_leader => khud + nested team
    | management  => selected office ke sabhi active employees
    |
    */

    public function employeeAttendance(Request $request)
    {
        $employees = $this->visibleEmployees($request, true);

        return view(
            'dashboard.employee.list',
            compact('employees')
        );
    }



    public function status(Request $request, User $employee)
    {
        $activeOfficeId = $request->user()->activeOfficeId();

        if (!$activeOfficeId || (int) $employee->office_id !== (int) $activeOfficeId) {
            abort(403, 'This employee does not belong to the selected office.');
        }

        $employee->status = $employee->status == '1' ? '0' : '1';

        $response = $employee->save();

        if ($response) {
            $request->session()->flash('success', 'Status changed successfully');
        } else {
            $request->session()->flash('error', 'Error, Try again!');
        }

        return back();
    }


    public function permission(Request $request, User $user)
    {
        $activeOfficeId = $request->user()->activeOfficeId();

        if (!$activeOfficeId || (int) $user->office_id !== (int) $activeOfficeId) {
            abort(403, 'This employee does not belong to the selected office.');
        }

        $permissions = $user->permissions;

        return view('dashboard.employee.permission', compact('permissions', 'user'));
    }

    public function permissionRemove(Request $request, Permission $permission, User $user)
    {
        $activeOfficeId = $request->user()->activeOfficeId();

        if (!$activeOfficeId || (int) $user->office_id !== (int) $activeOfficeId) {
            abort(403, 'This employee does not belong to the selected office.');
        }

        if ($user->hasPermissionTo($permission)) {
            $user->revokePermissionTo($permission);
            return back()->with('success', 'Permission removed from the user successfully.');
        }

        return back()->with('error', 'User does not have this permission.');
    }


}