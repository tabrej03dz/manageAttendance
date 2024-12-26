<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
use App\Models\AttendanceRecord;
use App\Models\Leave;
use App\Models\Off;
use App\Models\Salary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public static function countSundaysInMonth($year, $month)
    {
        $startOfMonth = Carbon::createFromDate($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        $sundaysCount = 0;

        // Loop through each day of the month
        while ($startOfMonth <= $endOfMonth) {
            if ($startOfMonth->isSunday()) {
                $sundaysCount++;
            }
            $startOfMonth->addDay();
        }

        return $sundaysCount;
    }
    public function index(Request $request){
        $request->validate([
            'month' => '',
        ]);


        if ($request->month) {
            // Extract year and month from the input (e.g., '2024-06')
            $year = Carbon::parse($request->month)->year;
            $month = Carbon::parse($request->month)->month;
            $endOfMonth = Carbon::parse($request->month . '-01')->endOfMonth();
        } else {
            // Default to the current year and month
            $year = Carbon::today()->year;
            $month = Carbon::today()->month;
            $endOfMonth = Carbon::now()->endOfMonth();
        }


        $user = $request->user();
//        $user = User::find(1);
        $employees = HomeController::employeeList($user);
        $sundayCount = SalaryController::countSundaysInMonth($year, $month);


        foreach ($employees as $employee){
            $monthSalary = Salary::where('user_id', $employee->id)->whereMonth('month', $month)->whereYear('month', $year)->first();
            if (!$monthSalary && $endOfMonth <= today()){
                $attendanceRecord = AttendanceRecord::whereMonth('created_at', $month)->whereYear('created_at', $year)->where('user_id', $employee->id)->get();
                $workingDuration = $attendanceRecord->sum('duration');
                $halfDays = $attendanceRecord->filter(function ($record) {
                    return is_null($record->check_in) || is_null($record->check_out);
                })->count();

                $fullDays = $attendanceRecord->count() - $halfDays;
                $leaves = Leave::where(function ($query) use ($month, $year) {
                    $query->whereYear('start_date', $year)
                        ->whereMonth('start_date', '<=', $month)
                        ->whereYear('end_date', $year)
                        ->whereMonth('end_date', '>=', $month);
                })->get();

                $offDays = Off::whereMonth('date', $month)->whereYear('date', $year)->count();

                $leaveCount = $leaves->sum('day_count');
                $paidLeave = $leaves->where('approve_as', 'paid')->sum('day_count');

                $oneDaySalary = $employee->salary / 30;
                $oneHourSalary = $oneDaySalary / ($employee->office_time / 60)  ;
                $salary = ($oneDaySalary * ($fullDays + $offDays + $sundayCount)) + (($halfDays * $oneDaySalary)/2) + ($paidLeave * $oneDaySalary);
                $durationSalary = (($workingDuration / 60) * $oneHourSalary) + (($sundayCount + $offDays) * $oneDaySalary);
                $advancePayment = AdvancePayment::whereMonth('date', $month)->where('date', $year)->where('user_id', $employee->id)->sum('amount');

                $monthSalary = Salary::create([
                    'user_id' => $employee->id,
                    'month' => $endOfMonth,
                    'day_wise_salary' => $salary,
                    'hour_wise_salary' => $durationSalary,
                    'status' => 'unpaid',
                    'basic_salary' => $user->userSalary ? $user->userSalary->basic_salary : $user->salary,
                    'house_rent_allowance' => $user->userSalary ? $user->userSalary->house_rent_allowance : 0,
                    'transport_allowance' => $user->userSalary ? $user->userSalary->transport_allowance : 0,
                    'medical_allowance' => $user->userSalary ? $user->userSalary->medical_allowance : 0,
                    'special_allowance' => $user->userSalary ? $user->userSalary->special_allowance : 0,
                    'advance' => $advancePayment ?? 0,
                    'deduction' => ($leaveCount - $paidLeave) * $oneDaySalary,

                ]);


            }

            $employee->month_salary = $monthSalary;
        }
        return response($employees);
    }
}
