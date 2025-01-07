<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Determine the month and calculate start and end dates
        if ($request->month) {
            $month = $request->month;
            $startOfMonth = Carbon::parse($request->month . '-01');
            $endOfMonth = Carbon::parse($request->month . '-01')->endOfMonth();
        } else {
            $month = Carbon::now()->format('Y-m');
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
        }

        // Retrieve the employee list
        $users = HomeController::employeeList($user);
        foreach ($users as $user){
            $records = AttendanceRecord::where('user_id', $user->id)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->get();
            $user->records = $records;
        }

        // Generate date range and retrieve attendance records
        $dates = new Collection();
//        $attendanceRecordsQuery = AttendanceRecord::query();
        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
            $dates->push([
                'date' => $date->copy()->toDateString(),
            ]);
//            $attendanceRecordsQuery->orWhereDate('created_at', $date);
        }
//        $attendanceRecords = $attendanceRecordsQuery->get();

        // Return the data as JSON
        return response()->json([
            'dates' => $dates,
            'users' => $users,
            'month' => $month,
        ]);
    }
}
