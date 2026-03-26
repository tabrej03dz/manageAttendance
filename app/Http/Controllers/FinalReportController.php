<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Leave;
use App\Models\Off;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class FinalReportController extends Controller
{
    // public function index(Request $request)
    // {
    //     // ====== Month Filter ======
    //     if ($request->month) {
    //         $month = $request->month;
    //         $startOfMonth = Carbon::parse($request->month . '-01')->startOfDay();
    //         $endOfMonth   = Carbon::parse($request->month . '-01')->endOfMonth()->endOfDay();
    //     } else {
    //         $month = Carbon::now()->format('Y-m');
    //         $startOfMonth = Carbon::now()->startOfMonth()->startOfDay();
    //         $endOfMonth   = Carbon::now()->endOfMonth()->endOfDay();
    //     }

    //     // ====== Dates Collection (UI ke liye) ======
    //     $dates = new Collection();
    //     $loopDate = $startOfMonth->copy();

    //     while ($loopDate->lte($endOfMonth)) {
    //         $dates->push((object)[
    //             'date' => $loopDate->copy(),
    //         ]);
    //         $loopDate->addDay();
    //     }

    //     // ====== Employees List (status + department filter) ======
    //     $users = HomeController::employeeList(); // yeh already collection lag raha hai

    //     // Status filter (pehle se)
    //     if ($request->filled('status')) {
    //         $users = $users->where('status', $request->status);
    //     } else {
    //         $users = $users->where('status', '1');
    //     }

    //     // ✅ Naya Department Filter
    //     if ($request->filled('department_id')) {
    //         $users = $users->where('department_id', $request->department_id);
    //     }

    //     // ====== Attendance Records (month + department ke hisaab se) ======
    //     $attendanceQuery = AttendanceRecord::query()
    //         ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

    //     // Agar department select hai to sirf us department ke employees ka attendance lao
    //     if ($request->filled('department_id')) {
    //         $employeeIds = $users->pluck('id'); // HomeController::employeeList() se aaye ids
    //         if ($employeeIds->isNotEmpty()) {
    //             $attendanceQuery->whereIn('user_id', $employeeIds); // yaha 'user_id' ko 'employee_id' bhi kar sakte ho
    //         } else {
    //             // agar koi employee hi nahi mila to koi attendance bhi nahi
    //             $attendanceQuery->whereRaw('1 = 0');
    //         }
    //     }

    //     $attendanceRecords = $attendanceQuery->get();

    //     // Departments dropdown ke liye
    //     $departments = Department::all();

    //     return view('dashboard.finalReport.index', [
    //         'dates'             => $dates,
    //         'attendanceRecords' => $attendanceRecords,
    //         'users'             => $users,
    //         'month'             => $month,
    //         'departments'       => $departments,
    //     ]);
    // }


    // public function index(Request $request)
    // {
    //     // ====== Month Range Filter ======
    //     $fromMonth = $request->filled('from_month')
    //         ? $request->from_month
    //         : Carbon::now()->format('Y-m');

    //     $toMonth = $request->filled('to_month')
    //         ? $request->to_month
    //         : Carbon::now()->format('Y-m');

    //     $startOfMonth = Carbon::parse($fromMonth . '-01')->startOfMonth()->startOfDay();
    //     $endOfMonth   = Carbon::parse($toMonth . '-01')->endOfMonth()->endOfDay();

    //     // Safety: agar from > to ho gaya to swap kar do
    //     if ($startOfMonth->gt($endOfMonth)) {
    //         [$startOfMonth, $endOfMonth] = [$endOfMonth, $startOfMonth];
    //         [$fromMonth, $toMonth] = [$toMonth, $fromMonth];
    //     }

    //     // ====== Dates Collection (UI ke liye) ======
    //     $dates = new \Illuminate\Support\Collection();
    //     $loopDate = $startOfMonth->copy();

    //     while ($loopDate->lte($endOfMonth)) {
    //         $dates->push((object)[
    //             'date' => $loopDate->copy(),
    //         ]);
    //         $loopDate->addDay();
    //     }

    //     // ====== Employees List ======
    //     $users = HomeController::employeeList(); // collection

    //     // Status filter
    //     if ($request->filled('status')) {
    //         $users = $users->where('status', $request->status);
    //     } else {
    //         $users = $users->where('status', '1');
    //     }

    //     // Department filter
    //     if ($request->filled('department_id')) {
    //         $users = $users->where('department_id', $request->department_id);
    //     }

    //     // Employee filter
    //     if ($request->filled('employee_id')) {
    //         $users = $users->where('id', $request->employee_id);
    //     }

    //     // reindex collection
    //     $users = $users->values();

    //     $employeeIds = $users->pluck('id')->toArray();

    //     // ====== Attendance Records ======
    //     $attendanceQuery = AttendanceRecord::query()
    //         ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

    //     if (!empty($employeeIds)) {
    //         $attendanceQuery->whereIn('user_id', $employeeIds);
    //     } else {
    //         $attendanceQuery->whereRaw('1 = 0');
    //     }

    //     $attendanceRecords = $attendanceQuery->get();

    //     // Departments dropdown
    //     $departments = Department::all();
    //     $allEmployees = HomeController::employeeList()->values();

    //     return view('dashboard.finalReport.index1', [
    //         'dates'             => $dates,
    //         'attendanceRecords' => $attendanceRecords,
    //         'users'             => $users,
    //         'fromMonth'         => $fromMonth,
    //         'toMonth'           => $toMonth,
    //         'departments'       => $departments,
    //         'allEmployees'      => $allEmployees,
    //     ]);
    // }



    // public function index(Request $request)
    // {
    //     $fromMonth = $request->filled('from_month')
    //         ? $request->from_month
    //         : Carbon::now()->format('Y-m');

    //     $toMonth = $request->filled('to_month')
    //         ? $request->to_month
    //         : Carbon::now()->format('Y-m');

    //     $startOfMonth = Carbon::parse($fromMonth . '-01')->startOfMonth()->startOfDay();
    //     $endOfMonth   = Carbon::parse($toMonth . '-01')->endOfMonth()->endOfDay();

    //     if ($startOfMonth->gt($endOfMonth)) {
    //         [$startOfMonth, $endOfMonth] = [$endOfMonth, $startOfMonth];
    //         [$fromMonth, $toMonth] = [$toMonth, $fromMonth];
    //     }

    //     $dates = collect();
    //     $loopDate = $startOfMonth->copy();

    //     while ($loopDate->lte($endOfMonth)) {
    //         $dates->push((object)[
    //             'date' => $loopDate->copy(),
    //         ]);
    //         $loopDate->addDay();
    //     }

    //     // Month-wise grouping
    //     $monthGroups = $dates->groupBy(function ($item) {
    //         return $item->date->format('Y-m');
    //     })->map(function ($monthDates, $monthKey) {
    //         $officeDays = 0;
    //         $sundayCount = 0;

    //         foreach ($monthDates as $dateObj) {
    //             $d = Carbon::parse($dateObj->date);

    //             if ($d->isSunday()) {
    //                 $sundayCount++;
    //             } else {
    //                 $officeDays++;
    //             }
    //         }

    //         return (object)[
    //             'month_key'    => $monthKey,
    //             'month_label'  => Carbon::parse($monthKey . '-01')->format('M-Y'),
    //             'dates'        => $monthDates->values(),
    //             'officeDays'   => $officeDays,
    //             'sundayCount'  => $sundayCount,
    //         ];
    //     })->values();

    //     $allEmployees = HomeController::employeeList()->values();
    //     $users = $allEmployees;

    //     if ($request->filled('status')) {
    //         $users = $users->where('status', $request->status);
    //     } else {
    //         $users = $users->where('status', '1');
    //     }

    //     if ($request->filled('department_id')) {
    //         $users = $users->where('department_id', $request->department_id);
    //     }

    //     if ($request->filled('employee_id')) {
    //         $users = $users->where('id', $request->employee_id);
    //     }

    //     $users = $users->values();

    //     $employeeIds = $users->pluck('id')->filter()->values();
    //     $officeIds   = $users->pluck('office_id')->filter()->unique()->values();

    //     // Attendance preload
    //     $attendanceRecords = AttendanceRecord::query()
    //         ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
    //         ->when($employeeIds->isNotEmpty(), function ($q) use ($employeeIds) {
    //             $q->whereIn('user_id', $employeeIds);
    //         }, function ($q) {
    //             $q->whereRaw('1 = 0');
    //         })
    //         ->get();

    //     $attendanceMap = $attendanceRecords->keyBy(function ($record) {
    //         return $record->user_id . '_' . $record->created_at->format('Y-m-d');
    //     });

    //     // Leaves preload
    //     $leaves = Leave::query()
    //         ->when($employeeIds->isNotEmpty(), function ($q) use ($employeeIds) {
    //             $q->whereIn('user_id', $employeeIds);
    //         }, function ($q) {
    //             $q->whereRaw('1 = 0');
    //         })
    //         ->whereDate('start_date', '<=', $endOfMonth->toDateString())
    //         ->whereDate('end_date', '>=', $startOfMonth->toDateString())
    //         ->get();

    //     $leaveMap = collect();

    //     foreach ($leaves as $leave) {
    //         $leaveStart = Carbon::parse($leave->start_date)->startOfDay();
    //         $leaveEnd   = Carbon::parse($leave->end_date)->endOfDay();

    //         if ($leaveStart->lt($startOfMonth)) {
    //             $leaveStart = $startOfMonth->copy();
    //         }

    //         if ($leaveEnd->gt($endOfMonth)) {
    //             $leaveEnd = $endOfMonth->copy();
    //         }

    //         $cursor = $leaveStart->copy();

    //         while ($cursor->lte($leaveEnd)) {
    //             $leaveMap->put(
    //                 $leave->user_id . '_' . $cursor->format('Y-m-d'),
    //                 $leave
    //             );
    //             $cursor->addDay();
    //         }
    //     }

    //     // Off preload
    //     $offs = Off::query()
    //         ->when($officeIds->isNotEmpty(), function ($q) use ($officeIds) {
    //             $q->whereIn('office_id', $officeIds);
    //         }, function ($q) {
    //             $q->whereRaw('1 = 0');
    //         })
    //         ->where('is_off', '1')
    //         ->whereDate('date', '>=', $startOfMonth->toDateString())
    //         ->whereDate('date', '<=', $endOfMonth->toDateString())
    //         ->get();

    //     $offMap = $offs->keyBy(function ($off) {
    //         return $off->office_id . '_' . Carbon::parse($off->date)->format('Y-m-d');
    //     });

    //     $departments = Department::all();

    //     return view('dashboard.finalReport.index1', [
    //         'dates'             => $dates,
    //         'monthGroups'       => $monthGroups,
    //         'attendanceRecords' => $attendanceRecords,
    //         'attendanceMap'     => $attendanceMap,
    //         'leaveMap'          => $leaveMap,
    //         'offMap'            => $offMap,
    //         'users'             => $users,
    //         'allEmployees'      => $allEmployees,
    //         'fromMonth'         => $fromMonth,
    //         'toMonth'           => $toMonth,
    //         'departments'       => $departments,
    //     ]);
    // }


    public function index(Request $request)
    {
        $fromMonth = $request->filled('from_month')
            ? $request->from_month
            : Carbon::now()->format('Y-m');

        $toMonth = $request->filled('to_month')
            ? $request->to_month
            : Carbon::now()->format('Y-m');

        $startOfMonth = Carbon::parse($fromMonth . '-01')->startOfMonth()->startOfDay();
        $endOfMonth   = Carbon::parse($toMonth . '-01')->endOfMonth()->endOfDay();

        if ($startOfMonth->gt($endOfMonth)) {
            [$startOfMonth, $endOfMonth] = [$endOfMonth, $startOfMonth];
            [$fromMonth, $toMonth] = [$toMonth, $fromMonth];
        }

        $dates = collect();
        $loopDate = $startOfMonth->copy();

        while ($loopDate->lte($endOfMonth)) {
            $dates->push((object)[
                'date' => $loopDate->copy(),
            ]);
            $loopDate->addDay();
        }

        $monthGroups = $dates->groupBy(function ($item) {
            return $item->date->format('Y-m');
        })->map(function ($monthDates, $monthKey) {
            $officeDays = 0;
            $sundayCount = 0;

            foreach ($monthDates as $dateObj) {
                $d = Carbon::parse($dateObj->date);

                if ($d->isSunday()) {
                    $sundayCount++;
                } else {
                    $officeDays++;
                }
            }

            return (object)[
                'month_key'    => $monthKey,
                'month_label'  => Carbon::parse($monthKey . '-01')->format('M-Y'),
                'dates'        => $monthDates->values(),
                'officeDays'   => $officeDays,
                'sundayCount'  => $sundayCount,
            ];
        })->values();

        // office-wise + hierarchical employee list
        $allEmployees = collect(HomeController::employeeList())->values();

        $users = $allEmployees;

        if ($request->filled('status')) {
            $users = $users->where('status', $request->status);
        } else {
            $users = $users->where('status', '1');
        }

        if ($request->filled('department_id')) {
            $users = $users->where('department_id', $request->department_id);
        }

        if ($request->filled('employee_id')) {
            $users = $users->where('id', $request->employee_id);
        }

        $users = $users->values();

        $employeeIds = $users->pluck('id')->filter()->unique()->values();
        $officeIds   = $users->pluck('office_id')->filter()->unique()->values();

        // Attendance preload
        $attendanceRecords = AttendanceRecord::query()
            ->when($employeeIds->isNotEmpty(), function ($q) use ($employeeIds) {
                $q->whereIn('user_id', $employeeIds);
            }, function ($q) {
                $q->whereRaw('1 = 0');
            })
            ->whereBetween('check_in', [$startOfMonth, $endOfMonth])
            ->get();

        $attendanceMap = $attendanceRecords->keyBy(function ($record) {
            return $record->user_id . '_' . Carbon::parse($record->check_in)->format('Y-m-d');
        });

        // Leaves preload
        $leaves = Leave::query()
            ->when($employeeIds->isNotEmpty(), function ($q) use ($employeeIds) {
                $q->whereIn('user_id', $employeeIds);
            }, function ($q) {
                $q->whereRaw('1 = 0');
            })
            ->whereDate('start_date', '<=', $endOfMonth->toDateString())
            ->whereDate('end_date', '>=', $startOfMonth->toDateString())
            ->get();

        $leaveMap = collect();

        foreach ($leaves as $leave) {
            $leaveStart = Carbon::parse($leave->start_date)->startOfDay();
            $leaveEnd   = Carbon::parse($leave->end_date)->endOfDay();

            if ($leaveStart->lt($startOfMonth)) {
                $leaveStart = $startOfMonth->copy();
            }

            if ($leaveEnd->gt($endOfMonth)) {
                $leaveEnd = $endOfMonth->copy();
            }

            $cursor = $leaveStart->copy();

            while ($cursor->lte($leaveEnd)) {
                $leaveMap->put(
                    $leave->user_id . '_' . $cursor->format('Y-m-d'),
                    $leave
                );
                $cursor->addDay();
            }
        }

        // Office offs preload
        $offs = Off::query()
            ->when($officeIds->isNotEmpty(), function ($q) use ($officeIds) {
                $q->whereIn('office_id', $officeIds);
            }, function ($q) {
                $q->whereRaw('1 = 0');
            })
            ->where('is_off', '1')
            ->whereDate('date', '>=', $startOfMonth->toDateString())
            ->whereDate('date', '<=', $endOfMonth->toDateString())
            ->get();

        $offMap = $offs->keyBy(function ($off) {
            return $off->office_id . '_' . Carbon::parse($off->date)->format('Y-m-d');
        });

        $departments = Department::all();

        return view('dashboard.finalReport.index1', [
            'dates'             => $dates,
            'monthGroups'       => $monthGroups,
            'attendanceRecords' => $attendanceRecords,
            'attendanceMap'     => $attendanceMap,
            'leaveMap'          => $leaveMap,
            'offMap'            => $offMap,
            'users'             => $users,
            'allEmployees'      => $allEmployees,
            'fromMonth'         => $fromMonth,
            'toMonth'           => $toMonth,
            'departments'       => $departments,
        ]);
    }
}
