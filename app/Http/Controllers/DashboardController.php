<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(){
        $halfDayRecords = AttendanceRecord::where('check_in', null)->orWhere('check_out', null)->get();
        foreach ($halfDayRecords as $record){
            $record->update(['day_type' => 'half day', 'duration' => $record->user->office_time / 2]);
        }
        return view('dashboard.dashboard');
    }
}
