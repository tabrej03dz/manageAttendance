<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\LunchBreak;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LunchBreakController extends Controller
{

    public function start(Request $request){
        $user = auth()->user();
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->where('user_id', $user->id)->first();
        if ($record){
            LunchBreak::create([
                'attendance_record_id' => $record->id,
                'start_time' => Carbon::now()->format('h:i'),
                'start_latitude' => $request->latitude,
                'start_longitude' => $request->longitude,
            ]);
        }else{
            return back()->with('error', 'Error, You can\'t take break without check-in');
        }
        return back()->with('success', 'Break start successfully');
    }

    public function stop(Request $request, LunchBreak $break){
        $status = $break->update([
            'end_time' => Carbon::now()->format('h:i'),
            'end_latitude' => $request->latitude,
            'end_longitude' => $request->longitude,
        ]);
        if ($status){
            request()->session()->flash('success', 'Break end successfully');
        }else{
            request()->session()->flash('error', 'Failed, Try again!');
        }
        return back();
    }
}
