<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FinalReportController extends Controller
{
//    public function index(Request $request){
//
//        if ($request->month){
//            $month = $request->month;
//            $startOfMonth = Carbon::parse($request->month . '-01');
//            $endOfMonth = Carbon::parse($request->month . '-01')->endOfMonth();
//        }else{
//            $month = Carbon::now()->format('Y-m');
//            $startOfMonth = Carbon::now()->startOfMonth();
//            $endOfMonth = Carbon::now()->endOfMonth();
//        }
//
//        $dates = new Collection();
//        $attendanceRecords = AttendanceRecord::query();
//        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
//            $dates->push((object)[
//                'date' => $date->copy(),
//            ]);
//            $attendanceRecords->orWhereDate('created_at', $date);
//        }
//        $attendanceRecords = $attendanceRecords->get();
//
//        $users = isset($request->status) ? HomeController::employeeList()->where('status', $request->status) : HomeController::employeeList()->where('status', '1');
//        $departments = Department::all();
//
//        return view('dashboard.finalReport.index', compact('dates', 'attendanceRecords', 'users', 'month', 'departments'));
//    }

    public function index(Request $request)
    {
        // ====== Month Filter ======
        if ($request->month) {
            $month = $request->month;
            $startOfMonth = Carbon::parse($request->month . '-01')->startOfDay();
            $endOfMonth   = Carbon::parse($request->month . '-01')->endOfMonth()->endOfDay();
        } else {
            $month = Carbon::now()->format('Y-m');
            $startOfMonth = Carbon::now()->startOfMonth()->startOfDay();
            $endOfMonth   = Carbon::now()->endOfMonth()->endOfDay();
        }

        // ====== Dates Collection (UI ke liye) ======
        $dates = new Collection();
        $loopDate = $startOfMonth->copy();

        while ($loopDate->lte($endOfMonth)) {
            $dates->push((object)[
                'date' => $loopDate->copy(),
            ]);
            $loopDate->addDay();
        }

        // ====== Employees List (status + department filter) ======
        $users = HomeController::employeeList(); // yeh already collection lag raha hai

        // Status filter (pehle se)
        if ($request->filled('status')) {
            $users = $users->where('status', $request->status);
        } else {
            $users = $users->where('status', '1');
        }

        // ✅ Naya Department Filter
        if ($request->filled('department_id')) {
            $users = $users->where('department_id', $request->department_id);
        }

        // ====== Attendance Records (month + department ke hisaab se) ======
        $attendanceQuery = AttendanceRecord::query()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        // Agar department select hai to sirf us department ke employees ka attendance lao
        if ($request->filled('department_id')) {
            $employeeIds = $users->pluck('id'); // HomeController::employeeList() se aaye ids
            if ($employeeIds->isNotEmpty()) {
                $attendanceQuery->whereIn('user_id', $employeeIds); // yaha 'user_id' ko 'employee_id' bhi kar sakte ho
            } else {
                // agar koi employee hi nahi mila to koi attendance bhi nahi
                $attendanceQuery->whereRaw('1 = 0');
            }
        }

        $attendanceRecords = $attendanceQuery->get();

        // Departments dropdown ke liye
        $departments = Department::all();

        return view('dashboard.finalReport.index', [
            'dates'             => $dates,
            'attendanceRecords' => $attendanceRecords,
            'users'             => $users,
            'month'             => $month,
            'departments'       => $departments,
        ]);
    }
}
