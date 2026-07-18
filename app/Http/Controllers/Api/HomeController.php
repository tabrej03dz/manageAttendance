<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use App\Models\AttendanceRecord;
use App\Models\LunchBreak;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class HomeController extends Controller
{
    public static function employeeList($user)
    {

        if ($user->hasRole('super_admin|admin')) {
            if ($user->hasRole('super_admin')) {
                $employees = User::all();
            } else {
                $office = $user->office;
                $employees = $office->users;
            }
        } elseif ($user->hasRole('owner')) {
            $officeIds = Office::where('owner_id', $user->id)->pluck('id');
            $employees = User::whereIn('office_id', $officeIds)->get();
        } else {
            if ($user->hasRole('team_leader')) {
                $employees = $user->members;
                $record = User::find($user->id);
                $employees->push($record);
            } else {
                $employees = User::where('id', $user->id)->get();
            }
        }

        return $employees;
    }



    // public static function getTime($totalMinutes){
    //     $hours = (int)($totalMinutes/60);
    //     $minutes = $totalMinutes % 60;

    //     return "$hours h, $minutes m";
    // }

    public static function getTime($totalMinutes): string
{
    $totalMinutes = max(0, (int) $totalMinutes);

    $hours = intdiv($totalMinutes, 60);
    $minutes = $totalMinutes % 60;

    return "{$hours} h, {$minutes} m";
}

    // public function dashboard(Request $request)
    // {
    // //        $user = $request->user();
    //     $user = User::find(1);
    //     // Fetch employees
    //     $employees = User::all();

    //     // Determine offices based on user role
    //     if ($user->hasRole('owner')) {
    //         $offices = $user->offices;
    //     } else {
    //         $offices = Office::all();
    //     }

    //     // Check today's attendance record
    //     $todayAttendanceRecord = AttendanceRecord::where('user_id', $user->id)
    //         ->whereDate('created_at', Carbon::today())
    //         ->first();
    //     // Get the latest lunch break for today's attendance record
    //     if ($todayAttendanceRecord) {
    //         $break = LunchBreak::where('attendance_record_id', $todayAttendanceRecord->id)
    //             ->orderBy('created_at', 'desc')
    //             ->first();
    //     } else {
    //         $break = null;
    //     }
    //     // Fetch current month's data
    //     $data = DashboardController::currentMonth(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth(), $user);

    //     // Return response as JSON
    //     return response()->json([
    //         'offices' => $offices->count(),
    //         'employees' => $employees->count(),
    //         'todayAttendanceRecord' => $todayAttendanceRecord,
    //         'break' => $break,
    //         'data' => $data,
    //     ]);
    // }


public function dashboard(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'status'  => false,
            'message' => 'Unauthenticated user.',
        ], 401);
    }

    try {
        /*
        |--------------------------------------------------------------------------
        | Employee list
        |--------------------------------------------------------------------------
        */

        $employees = self::employeeList($user);

        /*
        |--------------------------------------------------------------------------
        | Office list according to role
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('super_admin')) {
            $offices = Office::query()->get();
        } elseif ($user->hasRole('owner')) {
            $offices = Office::query()
                ->where('owner_id', $user->id)
                ->get();
        } elseif ($user->office_id) {
            $offices = Office::query()
                ->where('id', $user->office_id)
                ->get();
        } else {
            $offices = collect();
        }

        /*
        |--------------------------------------------------------------------------
        | Today's attendance
        |--------------------------------------------------------------------------
        */

        $todayAttendanceRecord = AttendanceRecord::query()
            ->where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->latest('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Latest break of today's attendance
        |--------------------------------------------------------------------------
        */

        $break = null;

        if ($todayAttendanceRecord) {
            $break = LunchBreak::query()
                ->where(
                    'attendance_record_id',
                    $todayAttendanceRecord->id
                )
                ->latest('id')
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Current month dashboard data
        |--------------------------------------------------------------------------
        */

        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $dashboardController = app(DashboardController::class);

        $data = $dashboardController->currentMonth(
            $startDate,
            $endDate,
            $user
        );

        /*
        |--------------------------------------------------------------------------
        | If currentMonth returns JsonResponse
        |--------------------------------------------------------------------------
        */

        if ($data instanceof \Illuminate\Http\JsonResponse) {
            $data = $data->getData(true);
        }

        return response()->json([
            'status'                => true,
            'message'               => 'Dashboard data fetched successfully.',
            'offices'               => $offices->count(),
            'employees'             => $employees->count(),
            'todayAttendanceRecord' => $todayAttendanceRecord,
            'break'                 => $break,
            'data'                  => $data,
        ], 200);
    } catch (\Throwable $exception) {
        \Log::error('API dashboard error', [
            'user_id' => $user->id,
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
            'trace'   => $exception->getTraceAsString(),
        ]);

        return response()->json([
            'status'  => false,
            'message' => 'Dashboard data could not be loaded.',
            'error'   => config('app.debug')
                ? $exception->getMessage()
                : null,
            'line'    => config('app.debug')
                ? $exception->getLine()
                : null,
        ], 500);
    }
}


}
