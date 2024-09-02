<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Office;
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
        $request->validate([
            'image' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);
        $user = auth()->user();
        $latitude = $request->latitude;
        $longitude = $request->longitude;

        // Office coordinates from the database
        $office = $user->office;
        $officeLatitude = $office->latitude;
        $officeLongitude = $office->longitude;
        $radius = $office->radius ?? '100';

        // Calculate the distance
        $distance = $this->haversineDistance($latitude, $longitude, $officeLatitude, $officeLongitude);

        // Check if the distance is within 100 meters
//        if (!($distance <= $radius)) {
//            return back()->with('error', 'you are '. $distance. ' of distance from office');
//        }


        $previousRecords = AttendanceRecord::where('user_id', $user->id)
            ->whereMonth('created_at', today()->month)
            ->get();
        $count = 0;
        foreach ($previousRecords as $previous){
            if ($previous?->check_in?->format('H:i') > $user->check_in_time?->addMinute(10)?->format('H:i')){
                $count++;
            }
        }
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->where('user_id', $user->id)->first();
        if ($record == null){
            $attendanceRecord = AttendanceRecord::create(['user_id' => $user->id, 'check_in' => Carbon::now(), 'duration' => $user->office_time/2, 'check_in_distance' => $distance, 'day_type' => '__']);

//            dd($request->check_in?->format('H:i'));
            if (now()->format('H:i') > $user->check_in_time->addMinute(10)->format('H:i')){
                $attendanceRecord->late = Carbon::now()->diffInMinutes(Carbon::parse($user->check_in_time));
                if ($count >= 2){
                    $attendanceRecord->day_type = 'half day';
                    $attendanceRecord->duration = $user->office_time;
                }
            }
            if ($request->file('image')){
                $file = $request->file('image')->store('public/images');
                $attendanceRecord->check_in_image = str_replace('public/', '', $file);
            }
            $attendanceRecord->save();
        }
        return redirect('attendance/index')->with('success', 'checked in successfully');
    }


    public function checkOut(Request $request){
        $user = auth()->user();

        $latitude = $request->latitude;
        $longitude = $request->longitude;

        // Office coordinates from the database
        $office = $user->office;
        $officeLatitude = $office->latitude;
        $officeLongitude = $office->longitude;
        $radius = $office->radius ?? '100';

        // Calculate the distance
        $distance = $this->haversineDistance($latitude, $longitude, $officeLatitude, $officeLongitude);

        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->where('user_id', auth()->user()->id)->first();
        if ($record){
            $duration = Carbon::now()->diffInMinutes($record->check_in);
//            dd($duration);
            $record->update(['check_out' => Carbon::now(), 'duration' => $record->day_type == 'half day' ? ($user->office_time)/2 : $duration, 'check_out_distance' => $distance, 'day_type' => '__']);
        }else{
            $record = AttendanceRecord::create(['user_id' => auth()->user()->id, 'check_out' => Carbon::now(), 'duration' => ($user->office_time)/2 , 'check_out_distance' => $distance]);
        }
        if ($request->file('image')){
            $file = $request->file('image')->store('public/images');
            $record->check_out_image = str_replace('public/', '', $file);
            $record->save();
        }
        return redirect('attendance/index')->with('success', 'checked in successfully');
    }

    public function form($formType){
        return view('dashboard.attendance.form', compact('formType'));
    }


    private function haversineDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $earthRadius = 6371000; // Earth radius in meters
        // Convert latitude and longitude from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
        // Haversine formula
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos($latFrom) * cos($latTo)
            * sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        // Distance in meters
        $distance = $earthRadius * $c;
        return $distance;
    }

    public function dayWise(Request $request){
        if ($request->date){
            $date = $request->date;
        }else{
            $date = today();
        }
        if (auth()->user()->hasRole('super_admin|admin')){
            $employees = User::all();
        }else{
            $employees = User::where('id', auth()->user()->id)->get();
        }
//        dd($employees);
        return view('dashboard.attendance.dayWise', compact('employees', 'date'));
    }
}
