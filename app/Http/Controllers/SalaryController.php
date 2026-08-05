<?php

// namespace App\Http\Controllers;

// use App\Models\AdvancePayment;
// use App\Models\AttendanceRecord;
// use App\Models\Salary;
// use App\Models\User;
// use App\Models\UserSalary;
// use Carbon\Carbon;
// use Illuminate\Http\Request;
// use Illuminate\Support\Collection;

// class SalaryController extends Controller
// {

// public function index(Request $request)
// {
//     if ($request->filled('month')) {
//         $month = $request->month;
//         $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
//         $endOfMonth   = Carbon::parse($month . '-01')->endOfMonth();
//     } else {
//         $month = now()->format('Y-m');
//         $startOfMonth = now()->copy()->startOfMonth();
//         $endOfMonth   = now()->copy()->endOfMonth();
//     }

//     $applyLateDeduction = $request->boolean('apply_late');
//     $applyEarlyExitDeduction = $request->boolean('apply_early_exit');

//     $lateDayThreshold = (int) $request->get('late_day_threshold', 3);
//     if ($lateDayThreshold <= 0) {
//         $lateDayThreshold = 3;
//     }

//     $dates = new Collection();
//     $loopDate = $startOfMonth->copy();

//     while ($loopDate->lte($endOfMonth)) {
//         $dates->push((object)[
//             'date' => $loopDate->copy(),
//         ]);
//         $loopDate->addDay();
//     }

//     $users = HomeController::employeeList()->where('status', '1');

//     $userIds = $users->pluck('id')->toArray();

//     $attendanceRecords = AttendanceRecord::whereIn('user_id', $userIds)
//         ->where(function ($query) use ($startOfMonth, $endOfMonth) {
//             $query->whereBetween('check_in', [
//                 $startOfMonth->copy()->startOfDay(),
//                 $endOfMonth->copy()->endOfDay()
//             ])->orWhereBetween('check_out', [
//                 $startOfMonth->copy()->startOfDay(),
//                 $endOfMonth->copy()->endOfDay()
//             ]);
//         })
//         ->get();

//     $advancePayments = AdvancePayment::whereIn('user_id', $userIds)
//         ->whereMonth('date', $startOfMonth->month)
//         ->whereYear('date', $startOfMonth->year)
//         ->get();

//     return view('dashboard.salary.calculator', compact(
//         'dates',
//         'attendanceRecords',
//         'users',
//         'month',
//         'advancePayments',
//         'startOfMonth',
//         'endOfMonth',
//         'applyLateDeduction',
//         'applyEarlyExitDeduction',
//         'lateDayThreshold'
//     ));
// }


//     public function status(Salary $salary){
//         $salary->update(['status' => 'paid']);
//         return back()->with('success', 'Mark as paid successfully');
//     }

//     public function paidAmount(Request $request, Salary $salary){
//         $request->validate(['paid_amount' => 'required|numeric']);
//         $salary->update(['paid_amount' => $request->paid_amount, 'status' => 'paid']);
//         return back()->with('success', 'Paid amount saved successfully');
//     }

//     public function salarySetupForm(User $employee){
//         $userSalary = UserSalary::where('user_id', $employee->id)->first();
//         return view('dashboard.salary.setupForm', compact('employee', 'userSalary'));
//     }

//     public function userSalaryInformationStore(Request $request, User $employee, UserSalary $userSalary = null){
//         $basicSalary = $request->basic_salary;
//         $dearnessAllowance = $request->dearness_allowance;
//         $relievingCharge = $request->relieving_charge;
//         $additionalAllowance = $request->additional_allowance;

//         $totalSalary = $basicSalary + $dearnessAllowance + $relievingCharge + $additionalAllowance;

//         if ($userSalary){
//             $userSalary->update($request->all() + ['total_salary' => $totalSalary]);
//         }else{
//             UserSalary::create(['user_id' => $employee->id, 'total_salary' => $totalSalary] + $request->all());
//         }
//         return redirect('employee')->with('success', 'Salary Details Saved Successfully');
//     }

//     public function salarySlip(Salary $salary){
//         $userSalary = UserSalary::where('user_id', $salary->user_id)->first();
//         return view('dashboard.salary.slip', compact('salary', 'userSalary'));
//     }

// }



<?php

namespace App\Http\Controllers;

use App\Models\AdvancePayment;
use App\Models\AttendanceRecord;
use App\Models\Office;
use App\Models\Salary;
use App\Models\User;
use App\Models\UserSalary;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class SalaryController extends Controller
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
    | Base employee query
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
    | Safe nested team hierarchy IDs
    |--------------------------------------------------------------------------
    |
    | Recursive Collection push/prepend का उपयोग नहीं किया गया है।
    | Circular hierarchy आने पर visited IDs loop रोक देंगे।
    |
    */

    private function nestedTeamIds(
        int $leaderId,
        ?int $officeId = null
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
             * Self-reference को root मानेंगे।
             */
            if ($leaderId === $employeeId) {
                $leaderId = 0;
            }

            /*
             * Current result में leader मौजूद नहीं है तो root।
             */
            if (
                $leaderId > 0
                && !isset($employeeById[$leaderId])
            ) {
                $leaderId = 0;
            }

            $childrenByLeader[$leaderId][] = $employee;
        }

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
                $employee->hierarchy_level = $level;
                $result[] = $employee;

                $children = $childrenByLeader[$employeeId] ?? [];

                /*
                 * Stack LIFO है, इसलिए reverse order में add करेंगे।
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

        if (
            $forcedRootId
            && isset($employeeById[$forcedRootId])
        ) {
            $processRoot(
                $employeeById[$forcedRootId],
                0
            );
        }

        foreach ($childrenByLeader[0] ?? [] as $rootEmployee) {
            $processRoot($rootEmployee, 0);
        }

        /*
         * Broken या circular hierarchy में बचे employees।
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
    | Employees visible to logged-in user
    |--------------------------------------------------------------------------
    */

    private function visibleEmployees(Request $request): Collection
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
         * Team leader खुद और पूरी nested team।
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
         * Super admin, owner और admin selected office के employees।
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
    | Visible employee IDs
    |--------------------------------------------------------------------------
    */

    private function visibleEmployeeIds(Request $request): array
    {
        return $this->visibleEmployees($request)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Scope validation
    |--------------------------------------------------------------------------
    */

    private function ensureEmployeeInScope(
        Request $request,
        int $employeeId
    ): void {
        if (
            !in_array(
                $employeeId,
                $this->visibleEmployeeIds($request),
                true
            )
        ) {
            abort(
                403,
                'This employee is outside your allowed hierarchy.'
            );
        }
    }

    private function ensureSalaryInScope(
        Request $request,
        Salary $salary
    ): void {
        $this->ensureEmployeeInScope(
            $request,
            (int) $salary->user_id
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Salary calculator
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
            $month = $request->filled('month')
                ? Carbon::createFromFormat(
                    'Y-m',
                    (string) $request->input('month')
                )->format('Y-m')
                : $currentMonth;
        } catch (Throwable $exception) {
            $month = $currentMonth;
        }

        $startOfMonth = Carbon::createFromFormat(
            'Y-m',
            $month
        )
            ->startOfMonth()
            ->startOfDay();

        $endOfMonth = Carbon::createFromFormat(
            'Y-m',
            $month
        )
            ->endOfMonth()
            ->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | Salary deduction settings
        |--------------------------------------------------------------------------
        */

        $applyLateDeduction = $request->boolean(
            'apply_late'
        );

        $applyEarlyExitDeduction = $request->boolean(
            'apply_early_exit'
        );

        $lateDayThreshold = (int) $request->input(
            'late_day_threshold',
            3
        );

        if ($lateDayThreshold <= 0) {
            $lateDayThreshold = 3;
        }

        if ($lateDayThreshold > 31) {
            $lateDayThreshold = 31;
        }

        /*
        |--------------------------------------------------------------------------
        | Month dates
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
        | Visible employees with hierarchy
        |--------------------------------------------------------------------------
        */

        $allUsers = $this->visibleEmployees($request);

        /*
         * Default active employees।
         */
        $users = $allUsers
            ->filter(function ($employee) {
                return (string) ($employee->status ?? '') === '1';
            })
            ->values();

        /*
         * Optional department filter।
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
         * Optional employee filter with hierarchy authorization।
         */
        if ($request->filled('employee_id')) {
            $employeeId = (int) $request->input(
                'employee_id'
            );

            $allowedEmployeeIds = $allUsers
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

        $userIds = $users
            ->pluck('id')
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

        if (empty($userIds)) {
            $attendanceQuery->whereRaw('1 = 0');
        } else {
            $attendanceQuery->whereIn(
                'user_id',
                $userIds
            );
        }

        $attendanceRecords = $attendanceQuery
            ->orderBy('user_id')
            ->orderBy('check_in')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Advance payments
        |--------------------------------------------------------------------------
        |
        | whereMonth/whereYear की जगह date range से index उपयोग हो सकता है।
        |
        */

        $advancePaymentQuery = AdvancePayment::query()
            ->whereBetween('date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString(),
            ]);

        if (empty($userIds)) {
            $advancePaymentQuery->whereRaw('1 = 0');
        } else {
            $advancePaymentQuery->whereIn(
                'user_id',
                $userIds
            );
        }

        $advancePayments = $advancePaymentQuery
            ->orderBy('user_id')
            ->orderBy('date')
            ->get();

        return view('dashboard.salary.calculator', [
            'dates' => $dates,
            'attendanceRecords' => $attendanceRecords,
            'users' => $users,
            'month' => $month,
            'advancePayments' => $advancePayments,
            'startOfMonth' => $startOfMonth,
            'endOfMonth' => $endOfMonth,
            'applyLateDeduction' => $applyLateDeduction,
            'applyEarlyExitDeduction' =>
                $applyEarlyExitDeduction,
            'lateDayThreshold' => $lateDayThreshold,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Mark salary as paid
    |--------------------------------------------------------------------------
    */

    public function status(
        Request $request,
        Salary $salary
    ) {
        $this->ensureSalaryInScope(
            $request,
            $salary
        );

        $salary->update([
            'status' => 'paid',
        ]);

        return back()->with(
            'success',
            'Marked as paid successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Save paid amount
    |--------------------------------------------------------------------------
    */

    public function paidAmount(
        Request $request,
        Salary $salary
    ) {
        $this->ensureSalaryInScope(
            $request,
            $salary
        );

        $validated = $request->validate([
            'paid_amount' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $salary->update([
            'paid_amount' => $validated['paid_amount'],
            'status' => 'paid',
        ]);

        return back()->with(
            'success',
            'Paid amount saved successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Salary setup form
    |--------------------------------------------------------------------------
    */

    public function salarySetupForm(
        Request $request,
        User $employee
    ) {
        $this->ensureEmployeeInScope(
            $request,
            (int) $employee->id
        );

        $userSalary = UserSalary::query()
            ->where('user_id', $employee->id)
            ->first();

        return view('dashboard.salary.setupForm', [
            'employee' => $employee,
            'userSalary' => $userSalary,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Store/update salary information
    |--------------------------------------------------------------------------
    */

    public function userSalaryInformationStore(
        Request $request,
        User $employee,
        UserSalary $userSalary = null
    ) {
        $this->ensureEmployeeInScope(
            $request,
            (int) $employee->id
        );

        $validated = $request->validate([
            'basic_salary' => [
                'required',
                'numeric',
                'min:0',
            ],
            'dearness_allowance' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'relieving_charge' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'additional_allowance' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ]);

        $basicSalary = (float) $validated['basic_salary'];

        $dearnessAllowance = (float) (
            $validated['dearness_allowance'] ?? 0
        );

        $relievingCharge = (float) (
            $validated['relieving_charge'] ?? 0
        );

        $additionalAllowance = (float) (
            $validated['additional_allowance'] ?? 0
        );

        $totalSalary = $basicSalary
            + $dearnessAllowance
            + $relievingCharge
            + $additionalAllowance;

        DB::transaction(function () use (
            $employee,
            $basicSalary,
            $dearnessAllowance,
            $relievingCharge,
            $additionalAllowance,
            $totalSalary
        ) {
            UserSalary::query()->updateOrCreate(
                [
                    'user_id' => $employee->id,
                ],
                [
                    'basic_salary' => $basicSalary,
                    'dearness_allowance' => $dearnessAllowance,
                    'relieving_charge' => $relievingCharge,
                    'additional_allowance' =>
                        $additionalAllowance,
                    'total_salary' => $totalSalary,
                ]
            );
        });

        return redirect('employee')->with(
            'success',
            'Salary details saved successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Salary slip
    |--------------------------------------------------------------------------
    */

    public function salarySlip(
        Request $request,
        Salary $salary
    ) {
        $this->ensureSalaryInScope(
            $request,
            $salary
        );

        $salary->loadMissing([
            'user',
        ]);

        $userSalary = UserSalary::query()
            ->where('user_id', $salary->user_id)
            ->first();

        return view('dashboard.salary.slip', [
            'salary' => $salary,
            'userSalary' => $userSalary,
        ]);
    }
}