<?php

namespace App\Http\Controllers;

use App\Models\AdvancePayment;
use App\Models\AttendanceRecord;
use App\Models\Salary;
use App\Models\User;
use App\Models\UserSalary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SalaryController extends Controller
{
    public function index(Request $request){
        if ($request->month){
            $month = $request->month;
            $startOfMonth = Carbon::parse($request->month . '-01');
            $endOfMonth = Carbon::parse($request->month . '-01')->endOfMonth();
        }else{
            $month = Carbon::now()->format('Y-m');
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
        }


        $dates = new Collection();
        $attendanceRecords = AttendanceRecord::query();
        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
            $dates->push((object)[
                'date' => $date->copy(),
            ]);
            $attendanceRecords->orWhereDate('check_in', $date)->orWhereDate('check_out', $date);
        }
        $attendanceRecords = $attendanceRecords->get();

        $users = HomeController::employeeList()->where('status', '1');

        $parseMonthYear = Carbon::parse($month);
        $advancePayments = AdvancePayment::whereMonth('date', $parseMonthYear->month)->whereYear('date',$parseMonthYear->year)->get();
//        dd($advancePayments);
        return view('dashboard.salary.index', compact('dates', 'attendanceRecords', 'users', 'month', 'advancePayments'));
    }

    public function status(Salary $salary){
        $salary->update(['status' => 'paid']);
        return back()->with('success', 'Mark as paid successfully');
    }

    public function paidAmount(Request $request, Salary $salary){
        $request->validate(['paid_amount' => 'required|numeric']);
        $salary->update(['paid_amount' => $request->paid_amount, 'status' => 'paid']);
        return back()->with('success', 'Paid amount saved successfully');
    }

    public function salarySetupForm(User $employee){
        $userSalary = UserSalary::where('user_id', $employee->id)->first();
        return view('dashboard.salary.setupForm', compact('employee', 'userSalary'));
    }

    public function userSalaryInformationStore(Request $request, User $employee, UserSalary $userSalary = null){
        $basicSalary = $request->basic_salary;
        $dearnessAllowance = $request->dearness_allowance;
        $relievingCharge = $request->relieving_charge;
        $additionalAllowance = $request->additional_allowance;

        $totalSalary = $basicSalary + $dearnessAllowance + $relievingCharge + $additionalAllowance;

        if ($userSalary){
            $userSalary->update($request->all() + ['total_salary' => $totalSalary]);
        }else{
            UserSalary::create(['user_id' => $employee->id, 'total_salary' => $totalSalary] + $request->all());
        }
        return redirect('employee')->with('success', 'Salary Details Saved Successfully');
    }

    public function salarySlip(Salary $salary){
        $userSalary = UserSalary::where('user_id', $salary->user_id)->first();
        return view('dashboard.salary.slip', compact('salary', 'userSalary'));
    }

}
