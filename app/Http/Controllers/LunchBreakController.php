<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\LunchBreak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LunchBreakController extends Controller
{

    public function start(Request $request, User $employee = null){

        if (!$employee){
            $employee = auth()->user();
        }
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->where('user_id', $employee->id)->first();
        if ($record){
            $break = LunchBreak::create([
                'attendance_record_id' => $record->id,
                'user_id' => $employee->id,
                'start_time' => Carbon::now()->format('h:i'),
                'start_latitude' => $request->latitude,
                'start_longitude' => $request->longitude,
                'reason' => $request->reason,
                'start_distance' => $request->distance,
            ]);
            if ($request->hasFile('image')){
                $photo = $request->file('image')->store('public/images');
                $break->start_image = str_replace('public/', '', $photo);
                $break->save();
            }
        }else{
            return back()->with('error', 'Error, You can\'t take break without check-in');
        }
        return redirect('home')->with('success', 'Break start successfully');
    }

    public function stop(Request $request, LunchBreak $break){
        $status = $break->update([
            'end_time' => Carbon::now()->format('h:i'),
            'end_latitude' => $request->latitude,
            'end_longitude' => $request->longitude,
            'end_distance' => $request->distance,
        ]);
        if ($request->hasFile('image')){
            $photo = $request->file('image')->store('public/images');
            $break->end_image = str_replace('public/', '', $photo);
            $break->save();
        }
        if ($status){
            request()->session()->flash('success', 'Break end successfully');
        }else{
            request()->session()->flash('error', 'Failed, Try again!');
        }
        return redirect('home');
    }

    public function index(Request $request){
        if ($request->date){
            $date = $request->date;
        }else{
            $date = Carbon::today();
        }
        $users = HomeController::employeeList();
        return view('dashboard.break.index', compact('users', 'date'));
    }

    public function form( User $employee = null, LunchBreak $break = null   ){
        return view('dashboard.break.form', compact('break', 'employee'));
    }
}
