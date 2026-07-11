<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\LunchBreak;
use App\Models\Off;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use App\Models\Leave;
use App\Models\Salary;


class DashboardController extends Controller
{



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

    // Super admin ko sab offices allowed
    if ($user->hasRole('super_admin')) {
        return Office::query()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    // Owner ko apni offices allowed
    if ($user->hasRole('owner')) {
        return Office::query()
            ->where('owner_id', $user->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    /*
        Normal user jiske paas switch office permission hai:
        user.office_id -> office.owner_id -> owner ki saari offices
    */
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

    // Normal user without switch permission
    return $user->office_id && (int) $user->office_id > 0
        ? [(int) $user->office_id]
        : [];
}

private function allowedOfficeIds(Request $request): array
{
    $switchableOfficeIds = $this->switchableOfficeIds($request);
    $activeOfficeId = $this->activeOfficeId($request);

    /*
        Agar office switch hua hai to sirf active office ka data dikhega.
    */
    if ($activeOfficeId) {
        return in_array((int) $activeOfficeId, $switchableOfficeIds, true)
            ? [(int) $activeOfficeId]
            : [];
    }

    /*
        Agar active office nahi hai to user ke allowed offices ka data.
    */
    return $switchableOfficeIds;
}


    // public static function currentMonth($startOfMont, $endOfMonth, $user){

    //     $data['sundays'] = 0;
    //     $data['days'] = 0;
    //     $data['offs'] = 0;
    //     $data['records'] = 0;

    //     for ($date = $startOfMont; $date->lte($endOfMonth); $date->addDay()) {
    //         if ($date->isSunday()) {
    //             $data['sundays'] += 1;
    //         }
    //         $data['days'] += 1;
    //         if (auth()->user()->hasRole('owner')){
    //                 $off = Off::whereDate('date', $date)->where('office_id', auth()->user()->offices->first()?->id)->first();
    //         }else{
    //             $off = Off::whereDate('date', $date)->where('office_id', auth()->user()->office->id)->first();
    //         }
    //         if ($off){
    //             $data['offs'] += 1;
    //         }
    //         $record = AttendanceRecord::whereDate('check_in', $date)->where('user_id', auth()->user()->id)->first();
    //         if($record){
    //             $data['records'] += 1;
    //         }

    //     }
    //     return $data;
    // }


    public static function currentMonth($startOfMont, $endOfMonth, $user, ?int $activeOfficeId = null)
{
    $data['sundays'] = 0;
    $data['days'] = 0;
    $data['offs'] = 0;
    $data['records'] = 0;

    for ($date = $startOfMont->copy(); $date->lte($endOfMonth); $date->addDay()) {
        if ($date->isSunday()) {
            $data['sundays'] += 1;
        }

        $data['days'] += 1;

        if ($activeOfficeId) {
            $off = Off::whereDate('date', $date)
                ->where('office_id', $activeOfficeId)
                ->first();

            if ($off) {
                $data['offs'] += 1;
            }
        }

        $record = AttendanceRecord::whereDate('check_in', $date)
            ->where('user_id', $user->id)
            ->first();

        if ($record) {
            $data['records'] += 1;
        }
    }

    return $data;
}


    // public function dashboard(Request $request)
    // {
    //     $user = auth()->user();

    //     $allowedOfficeIds = $this->allowedOfficeIds($request);
    //     $activeOfficeId = $this->activeOfficeId($request);

    //     /*
    //         Employees active office ke according.
    //         Office switch hua hai to sirf switched office ke employees.
    //     */
    //     $employees = User::query()
    //         ->when(!empty($allowedOfficeIds), function ($q) use ($allowedOfficeIds) {
    //             $q->whereIn('office_id', $allowedOfficeIds);
    //         }, function ($q) {
    //             $q->whereRaw('1 = 0');
    //         })
    //         ->get();

    //     $employees = HomeController::sortEmployeesHierarchically($employees);

    //     /*
    //         Offices count/list bhi active office ke according.
    //         Agar switch hai to count 1 aayega.
    //         Agar switch nahi hai to allowed offices count aayega.
    //     */
    //     $offices = Office::query()
    //         ->when(!empty($allowedOfficeIds), function ($q) use ($allowedOfficeIds) {
    //             $q->whereIn('id', $allowedOfficeIds);
    //         }, function ($q) {
    //             $q->whereRaw('1 = 0');
    //         })
    //         ->orderBy('name')
    //         ->get();

    //     $employeeIds = $employees->pluck('id');

    //     $todayAttendanceRecord = AttendanceRecord::where('user_id', $user->id)
    //         ->whereDate('created_at', Carbon::today())
    //         ->first();

    //     if ($todayAttendanceRecord) {
    //         $break = LunchBreak::where('attendance_record_id', $todayAttendanceRecord->id)
    //             ->orderBy('created_at', 'desc')
    //             ->first();
    //     } else {
    //         $break = null;
    //     }

    //     $todayCheckIn = AttendanceRecord::whereIn('user_id', $employeeIds)
    //         ->whereDate('check_in', today())
    //         ->get();

    //     $leaves = \App\Models\Leave::whereDate('start_date', '<=', today())
    //         ->whereDate('end_date', '>=', today())
    //         ->whereIn('user_id', $employeeIds)
    //         ->where('status', 'approved')
    //         ->get();

    //     $lastMonthPayouts = \App\Models\Salary::whereIn('user_id', $employeeIds)
    //         ->whereMonth('month', Carbon::now()->subMonth()->month)
    //         ->whereYear('month', Carbon::now()->subMonth()->year)
    //         ->get();

    //     $data = $this->currentMonth(
    //         Carbon::now()->startOfMonth(),
    //         Carbon::now()->endOfMonth(),
    //         $user,
    //         $activeOfficeId
    //     );

    //     return view('dashboard.dashboard', compact(
    //         'offices',
    //         'data',
    //         'todayAttendanceRecord',
    //         'break',
    //         'employees',
    //         'todayCheckIn',
    //         'leaves',
    //         'lastMonthPayouts',
    //         'allowedOfficeIds',
    //         'activeOfficeId'
    //     ));
    // }


    public function dashboard(Request $request)
    {
        $user = auth()->user();

        $allowedOfficeIds = collect($this->allowedOfficeIds($request))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $activeOfficeId = $this->activeOfficeId($request);

        /*
        |--------------------------------------------------------------------------
        | Employees
        |--------------------------------------------------------------------------
        | Office switched hai to active office ke employees.
        | Otherwise user ke allowed offices ke employees.
        */
        $employees = User::query()
            ->with([
                'office:id,name',
            ])
            ->when(
                !empty($allowedOfficeIds),
                fn ($query) => $query->whereIn('office_id', $allowedOfficeIds),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->where('status', 1)
            ->get();

        $employees = HomeController::sortEmployeesHierarchically($employees);

        /*
        |--------------------------------------------------------------------------
        | Offices
        |--------------------------------------------------------------------------
        */
        $offices = Office::query()
            ->when(
                !empty($allowedOfficeIds),
                fn ($query) => $query->whereIn('id', $allowedOfficeIds),
                fn ($query) => $query->whereRaw('1 = 0')
            )
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $employeeIds = $employees->pluck('id')->filter()->values();

        /*
        |--------------------------------------------------------------------------
        | Logged-in user attendance and break
        |--------------------------------------------------------------------------
        */
        $todayAttendanceRecord = AttendanceRecord::query()
            ->where('user_id', $user->id)
            ->whereDate('check_in', today())
            ->latest('check_in')
            ->first();

        $break = $todayAttendanceRecord
            ? LunchBreak::query()
                ->where('attendance_record_id', $todayAttendanceRecord->id)
                ->latest()
                ->first()
            : null;

        /*
        |--------------------------------------------------------------------------
        | Today's employee attendance
        |--------------------------------------------------------------------------
        */
        $todayCheckIn = AttendanceRecord::query()
            ->with('user:id,name,email,phone,office_id')
            ->whereIn('user_id', $employeeIds)
            ->whereDate('check_in', today())
            ->latest('check_in')
            ->get()
            ->unique('user_id')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Today's approved leaves
        |--------------------------------------------------------------------------
        */
        $leaves = Leave::query()
            ->with('user:id,name,email,phone,office_id')
            ->whereIn('user_id', $employeeIds)
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->where('status', 'approved')
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Last month payouts
        |--------------------------------------------------------------------------
        */
        $lastMonth = now()->subMonth();

        $lastMonthPayouts = Salary::query()
            ->with('user:id,name,email,phone,office_id')
            ->whereIn('user_id', $employeeIds)
            ->whereYear('month', $lastMonth->year)
            ->whereMonth('month', $lastMonth->month)
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Current month data
        |--------------------------------------------------------------------------
        */
        $data = $this->currentMonth(
            now()->startOfMonth(),
            now()->endOfMonth(),
            $user,
            $activeOfficeId
        );

        /*
        |--------------------------------------------------------------------------
        | Dashboard statistics
        |--------------------------------------------------------------------------
        */
        $totalEmployees = $employees->count();
        $presentEmployees = $todayCheckIn->count();
        $onLeaveEmployees = $leaves->pluck('user_id')->unique()->count();

        $absentEmployees = max(
            0,
            $totalEmployees - $presentEmployees - $onLeaveEmployees
        );

        $attendancePercentage = $totalEmployees > 0
            ? round(($presentEmployees / $totalEmployees) * 100)
            : 0;

        $checkedOutEmployees = $todayCheckIn
            ->filter(fn ($attendance) => !empty($attendance->check_out))
            ->count();

        $currentlyWorkingEmployees = $todayCheckIn
            ->filter(fn ($attendance) => empty($attendance->check_out))
            ->count();

        $lateEmployees = $todayCheckIn
            ->filter(function ($attendance) {
                return (bool) (
                    $attendance->is_late
                    ?? $attendance->late
                    ?? false
                );
            })
            ->count();

        $totalLastMonthPayout = $lastMonthPayouts->sum(function ($salary) {
            return (float) (
                $salary->net_salary
                ?? $salary->paid_amount
                ?? $salary->amount
                ?? 0
            );
        });

        return view('dashboard.dashboard', compact(
            'offices',
            'data',
            'todayAttendanceRecord',
            'break',
            'employees',
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
            'totalLastMonthPayout'
        ));
    }

}
