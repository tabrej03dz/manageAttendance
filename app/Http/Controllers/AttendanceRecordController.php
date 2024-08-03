<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceRecordController extends Controller
{
    public function index(Request $request){
        if ($request->start){
            $startOfMonth = Carbon::parse($request->start);
        }else{
            $startOfMonth = Carbon::now()->startOfMonth();
        }
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
        return view('dashboard.attendance.index', compact('dates', 'attendanceRecords'));
    }

    public function checkIn(Request $request){
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->first();
        if ($record == null){
            $attendanceRecord = AttendanceRecord::create(['user_id' => auth()->user()->id, 'check_in' => Carbon::now(), 'duration' => 4.0]);
            if ($request->file('image')){
                $file = $request->file('image')->store('public/images');
                $attendanceRecord->check_in_image = str_replace('public/', '', $file);
                $attendanceRecord->save();
            }
        }
        return redirect('attendance')->with('success', 'checked in successfully');
    }

    public function checkOut(Request $request){
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->first();
        if ($record){
            $duration = Carbon::now()->diff($record->check_in);
            $record->update(['check_out' => Carbon::now(), 'duration' => $duration->format('%H:%I:%S')]);
        }else{
            $record = AttendanceRecord::create(['user_id' => auth()->user()->id, 'check_out' => Carbon::now(), 'duration' => 4.0]);
        }
        if ($request->file('image')){
            $file = $request->file('image')->store('public/images');
            $record->check_out_image = str_replace('public/', '', $file);
            $record->save();
        }
        return redirect('attendance')->with('success', 'checked in successfully');
    }

    public function form($formType){
        return view('dashboard.attendance.form', compact('formType'));
    }
}
