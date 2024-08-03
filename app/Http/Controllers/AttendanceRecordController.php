<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceRecordController extends Controller
{
    public function index(Request $request){

        // Get the start and end of the current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

// Initialize an empty collection
        $dates = new Collection();
        $recordDates = [];

        $attendanceRecords = AttendanceRecord::query();
// Iterate from the start to the end of the month
        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
            // Add each date as an object with a 'date' property to the collection
//            array_push($recordDates, $date->format('Y-m-d'));
            $dates->push((object)[
                'date' => $date->copy(), // Copy to ensure each date is distinct
            ]);
            $attendanceRecords->orWhereDate('created_at', $date);
        }
        $attendanceRecords = $attendanceRecords->get();


//        dd($attendanceRecords);
        return view('dashboard.attendance.index', compact('dates', 'attendanceRecords'));
    }

    public function checkIn(Request $request){
//        dd($request->all());
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->first();
        if ($record == null){
            $attendanceRecord = AttendanceRecord::create(['user_id' => auth()->user()->id, 'check_in' => Carbon::now(), 'duration' => 4.0]);
            if ($request->file('image')){
                $file = $request->file('image')->store('public/images');
                $attendanceRecord->check_in_image = str_replace('public/', '', $file);
                $attendanceRecord->save();
            }
        }
        return redirect()->back()->with('success', 'checked in successfully');
    }

    public function checkOut(Request $request){
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->first();
        if ($record){
            $duration = Carbon::now()->diff($record->check_in);
//            dd($duration->format('%h:%i'));
            $record->update(['check_out' => Carbon::now(), 'duration' => $duration->format('%H:%I:%S')]);
        }else{
            $record = AttendanceRecord::create(['user_id' => auth()->user()->id, 'check_out' => Carbon::now(), 'duration' => 4.0]);
        }
        if ($request->file('image')){
            $file = $request->file('image')->store('public/images');
            $record->check_out_image = str_replace('public/', '', $file);
            $record->save();
        }
        return redirect()->back()->with('success', 'checked in successfully');
    }

    public function form($formType){
        return view('dashboard.attendance.form', compact('formType'));
    }
}
