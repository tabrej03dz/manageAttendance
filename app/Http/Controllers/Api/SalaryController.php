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
use App\Http\Controllers\HomeController;

use Illuminate\Support\Collection;

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



    public function salaryCalculate(Request $request)
    {
        try {
            if ($request->filled('month')) {
                $month = $request->month;
                $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
                $endOfMonth   = Carbon::parse($month . '-01')->endOfMonth();
            } else {
                $month = now()->format('Y-m');
                $startOfMonth = now()->copy()->startOfMonth();
                $endOfMonth   = now()->copy()->endOfMonth();
            }

            $applyLateDeduction = $request->boolean('apply_late');
            $applyEarlyExitDeduction = $request->boolean('apply_early_exit');

            $lateDayThreshold = (int) $request->input('late_day_threshold', 3);
            $lateDayThreshold = $lateDayThreshold > 0 ? $lateDayThreshold : 3;

            $dates = new Collection();
            $loopDate = $startOfMonth->copy();

            while ($loopDate->lte($endOfMonth)) {
                $dates->push((object) [
                    'date' => $loopDate->copy(),
                ]);
                $loopDate->addDay();
            }

            // API ke liye direct users query
            $usersQuery = User::query()->where('status', '1');

            // optional office filter
            if ($request->filled('office_id')) {
                $usersQuery->where('office_id', $request->office_id);
            }

            // optional single user filter
            if ($request->filled('user_id')) {
                $usersQuery->where('id', $request->user_id);
            }

            $users = $usersQuery->get();

            $userIds = $users->pluck('id')->toArray();

            $attendanceRecords = AttendanceRecord::whereIn('user_id', $userIds)
                ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->whereBetween('check_in', [
                        $startOfMonth->copy()->startOfDay(),
                        $endOfMonth->copy()->endOfDay(),
                    ])->orWhereBetween('check_out', [
                        $startOfMonth->copy()->startOfDay(),
                        $endOfMonth->copy()->endOfDay(),
                    ]);
                })
                ->get();

            $advancePayments = AdvancePayment::whereIn('user_id', $userIds)
                ->whereMonth('date', $startOfMonth->month)
                ->whereYear('date', $startOfMonth->year)
                ->get();

            $data = [];

            foreach ($users as $user) {
                $advancePayment = (float) $advancePayments->where('user_id', $user->id)->sum('amount');

                $officeDays = 0;
                $presentDays = 0;
                $halfDays = 0;
                $paidLeave = 0;
                $unpaidLeave = 0;
                $offDays = 0;
                $sundayCount = 0;

                $lateDays = 0;
                $lateMinutes = 0;

                $earlyExitDays = 0;
                $earlyExitMinutes = 0;

                $officeMinutesPerDay = (int) ($user->office_time ?? 0);

                if ($officeMinutesPerDay <= 0 && !empty($user->check_in_time) && !empty($user->check_out_time)) {
                    $officeMinutesPerDay = Carbon::parse($user->check_in_time)
                        ->diffInMinutes(Carbon::parse($user->check_out_time));
                }

                if ($officeMinutesPerDay <= 0) {
                    $officeMinutesPerDay = 540;
                }

                foreach ($dates as $dateObj) {
                    $d = Carbon::parse($dateObj->date);

                    if ($d->isSunday()) {
                        $sundayCount++;
                    } else {
                        $officeDays++;
                    }

                    $record = $attendanceRecords
                        ->where('user_id', $user->id)
                        ->first(function ($record) use ($d) {
                            if (!$record->check_in) {
                                return false;
                            }

                            return Carbon::parse($record->check_in)->format('Y-m-d') === $d->format('Y-m-d');
                        });

                    $leave = Leave::whereDate('start_date', '<=', $d)
                        ->whereDate('end_date', '>=', $d)
                        ->where('user_id', $user->id)
                        ->where('status', 'approved')
                        ->first();

                    $off = Off::whereDate('date', $d)
                        ->where('office_id', $user->office_id)
                        ->where('is_off', '1')
                        ->first();

                    if ($record) {
                        if ($record->check_in && $record->check_out) {
                            $presentDays++;
                        } else {
                            $halfDays++;
                        }

                        if ((int) ($record->late ?? 0) > 0) {
                            $lateDays++;
                            $lateMinutes += (int) $record->late;
                        }

                        if (!empty($record->check_out) && !empty($user->check_out_time)) {
                            $actualCheckOut = Carbon::parse($record->check_out);

                            $expectedCheckOut = Carbon::parse(
                                $d->format('Y-m-d') . ' ' . Carbon::parse($user->check_out_time)->format('H:i:s')
                            );

                            if ($actualCheckOut->lt($expectedCheckOut)) {
                                $earlyExitDays++;
                                $earlyExitMinutes += $actualCheckOut->diffInMinutes($expectedCheckOut);
                            }
                        }
                    }

                    if ($leave) {
                        if ($leave->approve_as === 'paid') {
                            $paidLeave++;
                        } else {
                            $unpaidLeave++;
                        }
                    }

                    if ($off) {
                        $offDays++;
                    }
                }

                $payableSunday = min((int) floor($presentDays / 6), $sundayCount);

                $absentDays = max(
                    0,
                    $officeDays - ($presentDays + $halfDays + $paidLeave + $unpaidLeave + $offDays)
                );

                $employeeSalary = (float) ($user->salary ?? 0);

                $payableDays = $presentDays + ($halfDays * 0.5) + $paidLeave + $offDays + $payableSunday;

                $perDaySalary = $employeeSalary > 0 ? round($employeeSalary / 30, 2) : 0;
                $grossSalary = round($perDaySalary * $payableDays, 2);

                $perMinuteSalary = $officeMinutesPerDay > 0
                    ? ($perDaySalary / $officeMinutesPerDay)
                    : 0;

                $lateSalaryDays = $lateDayThreshold > 0
                    ? floor($lateDays / $lateDayThreshold)
                    : 0;

                $lateDeduction = $applyLateDeduction
                    ? round($lateSalaryDays * $perDaySalary, 2)
                    : 0;

                $earlyExitDeduction = $applyEarlyExitDeduction
                    ? round($perMinuteSalary * $earlyExitMinutes, 2)
                    : 0;

                $totalDeduction = round($advancePayment + $lateDeduction + $earlyExitDeduction, 2);
                $netSalary = round($grossSalary - $totalDeduction, 2);

                $data[] = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'office_id' => $user->office_id,
                    'attendance_summary' => [
                        'office_days' => $officeDays,
                        'present_days' => $presentDays,
                        'half_days' => $halfDays,
                        'paid_leave_days' => $paidLeave,
                        'unpaid_leave_days' => $unpaidLeave,
                        'off_days' => $offDays,
                        'total_sundays' => $sundayCount,
                        'payable_sunday' => $payableSunday,
                        'absent_days' => $absentDays,
                        'payable_days' => round($payableDays, 2),
                    ],
                    'late_summary' => [
                        'late_days' => $lateDays,
                        'late_minutes' => $lateMinutes,
                        'late_salary_days' => $lateSalaryDays,
                        'late_time_hhmm' => $this->minutesToHHMM($lateMinutes),
                    ],
                    'early_exit_summary' => [
                        'early_exit_days' => $earlyExitDays,
                        'early_exit_minutes' => $earlyExitMinutes,
                        'early_exit_time_hhmm' => $this->minutesToHHMM($earlyExitMinutes),
                    ],
                    'salary_breakdown' => [
                        'monthly_salary' => round($employeeSalary, 2),
                        'advance' => round($advancePayment, 2),
                        'per_day_salary' => round($perDaySalary, 2),
                        'gross_salary' => round($grossSalary, 2),
                        'late_deduction' => round($lateDeduction, 2),
                        'early_exit_deduction' => round($earlyExitDeduction, 2),
                        'total_deduction' => round($totalDeduction, 2),
                        'net_salary' => round($netSalary, 2),
                    ],
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Salary calculator data fetched successfully.',
                'filters' => [
                    'month' => $month,
                    'start_date' => $startOfMonth->toDateString(),
                    'end_date' => $endOfMonth->toDateString(),
                    'apply_late_deduction' => $applyLateDeduction,
                    'apply_early_exit_deduction' => $applyEarlyExitDeduction,
                    'late_day_threshold' => $lateDayThreshold,
                    'office_id' => $request->office_id,
                    'user_id' => $request->user_id,
                ],
                'total_employees' => count($data),
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while calculating salary.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function minutesToHHMM(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $remainMinutes = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $remainMinutes);
    }
}
