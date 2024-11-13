<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Off;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{

    function currentMonth($startOfMont, $endOfMonth){

        $data['sundays'] = 0;
        $data['days'] = 0;
        $data['offs'] = 0;
        $data['records'] = 0;

        for ($date = $startOfMont; $date->lte($endOfMonth); $date->addDay()) {
            if ($date->isSunday()) {
                $data['sundays'] += 1;
            }
            $data['days'] += 1;
            $off = Off::whereDate('date', $date)->where('office_id', auth()->user()->office->id)->first();
            if ($off){
                $data['offs'] += 1;
            }
            $record = AttendanceRecord::whereDate('check_in', $date)->where('user_id', auth()->user()->id)->first();
            if($record){
                $data['records'] += 1;
            }

        }
        return $data;
    }

    public function dashboard(){
        Role::create(['name' => 'owner']);
        dd('Role Created successfully');
        $halfDayRecords = AttendanceRecord::where('check_in', null)->orWhere('check_out', null)->get();
        foreach ($halfDayRecords as $record){
            $record->update(['day_type' => 'half day', 'duration' => $record->user->office_time / 2]);
        }
        $employees = User::all();
        if (auth()->user()->hasRole('owner')){
            $offices = auth()->user()->offices;
        }else{
            $offices = Office::all();
        }

        $data = $this->currentMonth(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());

        return view('dashboard.dashboard', compact('offices', 'data'))->with('employees', $employees);
    }


}
