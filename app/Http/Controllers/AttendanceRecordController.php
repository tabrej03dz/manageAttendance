<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;


class AttendanceRecordController extends Controller
{
    public function index(Request $request, User $user = null){
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
            $attendanceRecords->orWhereDate('created_at', $date)->where('user_id', $user ? $user->id : auth()->user()->id);
        }
        $attendanceRecords = $attendanceRecords->get();
        $users = User::whereDoesntHave('roles', function($query) {
            $query->where('name', 'super_admin');
        })->get();
        return view('dashboard.attendance.index', compact('dates', 'attendanceRecords', 'users', 'user'));
    }

    public function checkIn(Request $request){
        $user = auth()->user();
        $previousRecords = AttendanceRecord::where('user_id', $user->id)->where('created_at', '!=', today())->orderBy('created_at', 'desc')->take(3)->get();
        $count = 0;
        foreach ($previousRecords as $previous){
            if ($previous->check_in?->format('H:i') > $user->check_in_time && $previous->day_type == 'half day'){
                $count++;
            }
        }
        $yesterday = AttendanceRecord::whereDate('created_at', Carbon::yesterday())->where('user_id', $user->id)->first();
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->where('user_id', $user->id)->first();
        if ($record == null){
            $attendanceRecord = AttendanceRecord::create(['user_id' => $user->id, 'check_in' => Carbon::now(), 'duration' => $user->office_time/2]);
            if ($request->file('image')){
                $file = $request->file('image')->store('public/images');
                $attendanceRecord->check_in_image = str_replace('public/', '', $file);
            }
            if ($count == 3){
                $attendanceRecord->day_type = 'half day';
                $attendanceRecord->duration = $user->office_time / 2;
            }
            $attendanceRecord->save();

        }
        if ($yesterday == null){
            AttendanceRecord::create(['user_id' => $user->id, 'day_type' => 'leave', 'created_at' => Carbon::yesterday()]);
        }
        return redirect('attendance/index')->with('success', 'checked in successfully');
    }

    public function checkOut(Request $request){
        $user = auth()->user();
        $yesterday = AttendanceRecord::whereDate('created_at', Carbon::yesterday())->where('user_id', $user->id)->first();
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->where('user_id', auth()->user()->id)->first();
        if ($record){
            $duration = Carbon::now()->diffInMinutes($record->check_in)/60;
            $record->update(['check_out' => Carbon::now(), 'duration' => $record->day_type == 'half day' ? ($user->office_time/2) : $duration]);
        }else{
            $record = AttendanceRecord::create(['user_id' => auth()->user()->id, 'check_out' => Carbon::now(), 'duration' => 4.0]);
        }
        if ($request->file('image')){
            $file = $request->file('image')->store('public/images');
            $record->check_out_image = str_replace('public/', '', $file);
            $record->save();
        }
        if ($yesterday == null){
            AttendanceRecord::create(['user_id' => $user->id, 'day_type' => 'leave', 'created_at' => Carbon::yesterday()]);
        }
        return redirect('attendance/index')->with('success', 'checked in successfully');
    }

    public function form($formType){
        return view('dashboard.attendance.form', compact('formType'));
    }
}
