<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index(Request $request){
        if ($request->month) {
            // Extract year and month from the input (e.g., '2024-06')
            $year = Carbon::parse($request->month)->year;
            $month = Carbon::parse($request->month)->month;
        } else {
            // Default to the current year and month
            $year = Carbon::today()->year;
            $month = Carbon::today()->month;
        }

        $user = $request->user();
        $employees = HomeController::employeeList($user);


        foreach ($employees as $employee){
            $salary = Salary::where('user_id', $employee->id)->whereMonth('month', $month)->whereYear('month', $year)->first();
            if (!$salary){
                $attendanceRecord = AttendanceRecord::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('user_id', $employee->id)->get();
                $halfDays = $attendanceRecord->where('check_in', null)->orWhere('check_out', null)->count();
                $fullDays = $attendanceRecord->count() - $halfDays;
//                $sundayCount =
            }
            $employee->month_salary = $salary;
        }

        return response($employees);
    }
}
