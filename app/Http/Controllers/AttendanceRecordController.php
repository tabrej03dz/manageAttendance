<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Leave;
use App\Models\Office;
use App\Models\User;
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
        $monthStart = $startOfMonth->toDateTimeLocalString();

        if ($request->end){
            $endOfMonth = Carbon::parse($request->end);
        }else{
            $endOfMonth = Carbon::now()->endOfMonth();
        }
        if($request->employee){
            $user = User::find($request->employee);
        }else{
            $user = null;
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
//        if (auth()->user()->hasRole('super_admin')){
//
//            $users = User::role(['admin', 'employee'])->get();
//        }else{
//            $office = auth()->user()->office;
////            dd($office->users);
//            $users = $office->users;
//        }
        $users = HomeController::employeeList();
        return view('dashboard.attendance.index', compact('dates', 'attendanceRecords', 'users', 'user', 'monthStart', 'endOfMonth'));
    }

//    public function checkIn(Request $request){
//        $request->validate([
//            'image' => 'required',
//            'latitude' => 'required',
//            'longitude' => 'required',
//        ]);
//        $user = auth()->user();
//        $latitude = $request->latitude;
//        $longitude = $request->longitude;
//
//        // Office coordinates from the database
//        $office = $user->office;
//        $officeLatitude = $office->latitude;
//        $officeLongitude = $office->longitude;
//        $radius = $office->radius ?? '100';
//
//        // Calculate the distance
//        $distance = $this->haversineDistance($latitude, $longitude, $officeLatitude, $officeLongitude);
//
//        // Check if the distance is within 100 meters
//        if (!($distance <= $radius)) {
//            return back()->with('error', 'you are '. $distance. ' of distance from office');
//        }
//
//
//        $previousRecords = AttendanceRecord::where('user_id', $user->id)
//            ->whereMonth('created_at', today()->month)
//            ->get();
//        $count = 0;
//        foreach ($previousRecords as $previous){
//            if ($previous?->check_in?->format('H:i') > $user->check_in_time?->addMinute(10)?->format('H:i')){
//                $count++;
//            }
//        }
//        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->where('user_id', $user->id)->first();
//        if ($record == null){
//            $attendanceRecord = AttendanceRecord::create(['user_id' => $user->id, 'check_in' => Carbon::now(), 'duration' => $user->office_time/2, 'check_in_distance' => $distance, 'day_type' => '__']);
//
////            dd($request->check_in?->format('H:i'));
//            if (now()->format('H:i') > $user->check_in_time->addMinute(10)->format('H:i')){
//                $attendanceRecord->late = Carbon::now()->diffInMinutes(Carbon::parse($user->check_in_time));
//                if ($count >= 2){
//                    $attendanceRecord->day_type = 'half day';
//                    $attendanceRecord->duration = $user->office_time;
//                }
//            }
//            if ($request->file('image')){
//                $file = $request->file('image')->store('public/images');
//                $attendanceRecord->check_in_image = str_replace('public/', '', $file);
//            }
//            $attendanceRecord->save();
//        }
//        return redirect('attendance/index')->with('success', 'checked in successfully');
//    }


    public function checkIn(Request $request, User $user = null) {
        $request->validate([
            'image' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'distance' => 'required',
        ]);

        // Get the authenticated user
        if ($user == null){
            $user = auth()->user();
        }

        // Capture latitude and longitude from the request
//        $latitude = $request->latitude;
//        $longitude = $request->longitude;

        // Fetch the office coordinates and radius from the database
        $office = $user->office;
        $officeLatitude = $office->latitude;
        $officeLongitude = $office->longitude;
        $radius = $office->radius ?? 100; // Default to 100 meters if no radius is set

        // Calculate the distance using Haversine formula
//        if ($latitude == '' || $longitude == ''){
//            $distance = '';
//        }else{
//            $distance = $this->haversineDistance($latitude, $longitude, $officeLatitude, $officeLongitude);
//        }



        // Check if the distance is within the allowed radius
//        if ($distance > $radius) {
//            return back()->with('error', 'You are ' . round($distance, 2) . ' meters away from the office.');
//        }

        // Fetch previous attendance records for the current month
        $previousRecords = AttendanceRecord::where('user_id', $user->id)
            ->whereMonth('created_at', today()->month)
            ->get();

        // Initialize late count
        $count = 0;

        // Check for late entries in the previous records
        foreach ($previousRecords as $previous) {
//            if ($previous?->check_in?->format('H:i') > $user->check_in_time?->addMinutes(10)?->format('H:i')) {
//                $count++;
//            }
            if ($previous?->late) {
                $count++;
            }
            if ($previous?->day_type == 'half day'){
                $count = 0;
            }
        }

        // Fetch today's attendance record
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())
            ->where('user_id', $user->id)
            ->first();

        if ($record === null) {
            // Create a new attendance record
            $attendanceRecord = AttendanceRecord::create([
                'user_id' => $user->id,
                'check_in' => Carbon::now(),
                'duration' => $user->office_time / 2, // Set initial duration
                'check_in_distance' => $request->distance,
                'day_type' => '__', // Initialize day type
                'check_in_note' => $request->note ?? null,
                'check_in_latitude' => $request->latitude,
                'check_in_longitude' => $request->longitude,
            ]);

            // Check if the user is late
            if (now()->format('H:i') > $user->check_in_time->addMinutes(5)->format('H:i')) {
                $attendanceRecord->late = Carbon::now()->diffInMinutes(Carbon::parse($user->check_in_time));

                // If late more than twice, mark as half-day
                if ($count >= 2) {
                    $attendanceRecord->day_type = 'half day';
                    $attendanceRecord->duration = $user->office_time;
                }
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                try {
                    $file = $request->file('image')->store('public/images');
                    $attendanceRecord->check_in_image = str_replace('public/', '', $file);
                    $attendanceRecord->save();
                } catch (\Exception $e) {
                    Log::error('Image upload failed: ' . $e->getMessage());
                    return back()->with('error', 'Failed to upload image. Please try again.');
                }
            }

            // Save the attendance record
            $attendanceRecord->save();
            $message = 'Checked in successfully';

            if ($attendanceRecord->late){
                $type = 'check_in_note';
                $time = HomeController::getTime($attendanceRecord->late);
                $message = 'You are '.$time.' late, Write here why you have been late..';
                return redirect()->route('attendance.reason.form', ['type' => $type, 'message' => $message, 'record' => $attendanceRecord]);
            }

        }else{
            $message = 'Today you has checked in already';
        }
        // Redirect to attendance index with success message
        return redirect('attendance/day-wise')->with('success', $message);
    }


    public function checkOut(Request $request, User $user = null){
        $request->validate([
            'image' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'distance' => 'required',
        ]);
        if ($user == null){
            $user = auth()->user();
        }
        // Office coordinates from the database
        $office = $user->office;
        $radius = $office->radius ?? '100';
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->where('user_id', $user->id)->first();
        if ($record){
            $duration = Carbon::now()->diffInMinutes($record->check_in);
            $record->update(['check_out' => Carbon::now(), 'duration' => $duration, 'check_out_distance' => $request->distance, 'day_type' => '__', 'check_out_latitude' => $request->latitude, 'check_out_longitude' => $request->logitude]);
        }else{
            $duration = $user->office_time / 2;
            $record = AttendanceRecord::create(['user_id' => $user->id, 'check_out' => Carbon::now(), 'duration' => $duration , 'check_out_distance' => $request->distance, 'check_out_latitude' => $request->latitude, 'check_out_longitude' => $request->logitude]);
        }
        if ($request->file('image')){
            $file = $request->file('image')->store('public/images');
            $record->check_out_image = str_replace('public/', '', $file);
            $record->save();
        }
        if (Carbon::parse($record->check_out)->format('H:i:s') < Carbon::parse($user->check_out_time)->format('H:i:s')){
            $checkOutTime = Carbon::parse($record->check_out)->format('H:i:s');
            $userCheckOutTime = Carbon::parse($user->check_out_time);
            $time = Carbon::createFromFormat('H:i:s', $checkOutTime)->diffInMinutes($userCheckOutTime);
            $before = HomeController::getTime($time);
            $message = 'You are checking out before '.$before.' write here the reasons';
            $type = 'check_out_note';

//            return view('dashboard.attendance.noteForm', compact('type', 'message', 'attendanceRecord'));
            return redirect()->route('attendance.reason.form', ['type' => $type, 'message' => $message, 'record' => $record]);
        }
        return redirect('attendance/day-wise')->with('success', 'checked out successfully');
    }

    public function form($formType, User $user = null){
        if (auth()->user()->is_accepted == '0'){
            return back()->with('error', 'You don\'t have accepted policy, Please read policies!');
        }
        return view('dashboard.attendance.form', compact('formType', 'user'));
    }


//    private function haversineDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
//    {
//        $earthRadius = 6371000; // Earth radius in meters
//        // Convert latitude and longitude from degrees to radians
//        $latFrom = deg2rad($latitudeFrom);
//        $lonFrom = deg2rad($longitudeFrom);
//        $latTo = deg2rad($latitudeTo);
//        $lonTo = deg2rad($longitudeTo);
//        // Haversine formula
//        $latDelta = $latTo - $latFrom;
//        $lonDelta = $lonTo - $lonFrom;
//        $a = sin($latDelta / 2) * sin($latDelta / 2) +
//            cos($latFrom) * cos($latTo)
//            * sin($lonDelta / 2) * sin($lonDelta / 2);
//        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
//        // Distance in meters
//        $distance = $earthRadius * $c;
//        return $distance;
//    }

    public function dayWise(Request $request){
        if ($request->date){
            $date = $request->date;
        }else{
            $date = today();
        }
//        if (auth()->user()->hasRole('super_admin|admin')){
//            if (auth()->user()->hasRole('super_admin')){
//                $employees = User::all();
//            }else{
//                $office = auth()->user()->office;
//                $employees = $office->users;
//            }
//        }else{
//            $employees = User::where('id', auth()->user()->id)->get();
//        }
        $employees = HomeController::employeeList();
//        dd($employees);
        return view('dashboard.attendance.dayWise', compact('employees', 'date'));
    }

    public function addNote(Request $request, AttendanceRecord $record){
        $request->validate([
            'note' => 'required',
        ]);
        $record->note = $request->note;
        $record->noted_by = auth()->user()->id;
        $record->save();
        return back()->with('success', 'Note Added successfully');
    }

    public function userNote(Request $request,AttendanceRecord $record, $type){
        $request->validate([
            'note' => 'required',
        ]);
        if ($type == 'check_in_note'){
            $record->check_in_note = $request->note;
        }else{
            $record->check_out_note = $request->note;
        }
        $record->save();

        return redirect('attendance/day-wise');
    }

    public function userNoteResponse(AttendanceRecord $record, $type, $status){
        if ($type == 'check_in_note'){
            $record->check_in_note_status = $status;
            $record->check_in_note_response_by = auth()->user()->id;
        }else{
            $record->check_out_note_status = $status;
            $record->check_out_note_response_by = auth()->user()->id;
        }
        $record->save();
        return back();
    }

    public function reasonFormLoad($type, $message, AttendanceRecord $record){
        return view('dashboard.attendance.noteForm', compact('type', 'message', 'record'));
    }
}
