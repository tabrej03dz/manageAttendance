<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class FinalReportController extends Controller
{
    public function index(Request $request){

        if ($request->start){
            $startOfMonth = Carbon::parse($request->start);
        }else{
            $startOfMonth = Carbon::now()->startOfMonth();
        }
        $monthStart = $startOfMonth->toDateTimeLocalString();

        if ($request->end){
            $endOfMonth = Carbon::parse($request->end);
        }else{
            $endOfMonth = Carbon::now()->endOfMonth();
        }


        $dates = new Collection();
        $attendanceRecords = AttendanceRecord::query();
        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
            $dates->push((object)[
                'date' => $date->copy(),
            ]);
            $attendanceRecords->orWhereDate('created_at', $date);
        }
        $attendanceRecords = $attendanceRecords->get();
        if (auth()->user()->hasRole('super_admin')){
            $users = User::role(['admin', 'employee'])->get();
        }else{
            $office = auth()->user()->office;
//            dd($office->users);
            $users = $office->users;
        }
        return view('dashboard.finalReport.index', compact('dates', 'attendanceRecords', 'users', 'monthStart', 'endOfMonth'));
    }
}
