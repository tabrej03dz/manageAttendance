<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;


class AttendanceRecordController extends Controller
{

    public function index(Request $request){

        if ($request->month){
            $month = $request->month;
            $startOfMonth = Carbon::parse($request->month . '-01');
            $endOfMonth = Carbon::parse($request->month . '-01')->endOfMonth();
        }else{
            $month = Carbon::now()->format('Y-m');
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
        }

//        if ($request->start){
//            $startOfMonth = Carbon::parse($request->start);
//        }else{
//            $startOfMonth = Carbon::now()->startOfMonth();
//        }
//        $monthStart = $startOfMonth->toDateTimeLocalString();
//
//        if ($request->end){
//            $endOfMonth = Carbon::parse($request->end);
//        }else{
//            $endOfMonth = Carbon::now()->endOfMonth();
//        }
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
            $attendanceRecords->orWhereDate('check_in', $date)->where('user_id', $user ? $user->id : auth()->user()->id);
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
        return view('dashboard.attendance.index', compact('dates', 'attendanceRecords', 'users', 'user',  'month'));
    }


    public function checkIn(Request $request, User $user = null) {
        $request->validate([
            'image' => '',
            'latitude' => '',
            'longitude' => '',
            'distance' => '',
        ]);

        if ($user == null){
            $user = auth()->user();
        }
        if ($user->status == '0' || $user->office->status == 'inactive'){
            return back()->with('error', 'You are inactive user');
        }
        if ($user->location_required === 'yes' && (!$request->latitude || !$request->longitude)) {
            return view('dashboard.settingInstruction');
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
                'check_in_distance' => $request->distance ?? null,
                'day_type' => '__',
                'check_in_note' => $request->note ?? null,
                'check_in_latitude' => $request->latitude ?? null,
                'check_in_longitude' => $request->longitude ?? null,
                'check_in_by' => auth()->user()->id,
            ]);
            // Check if the user is late
            if (now()->format('H:i') > $user->check_in_time->addMinutes(5)->format('H:i')) {
                $attendanceRecord->late = Carbon::now()->diffInMinutes(Carbon::parse($user->check_in_time));
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
            $messageType = 'success';


            if ($attendanceRecord->late){
                $type = 'check_in_note';
                $time = HomeController::getTime($attendanceRecord->late);
                $message = 'You are '.$time.' late, Write here why you have been late..';
                return redirect()->route('attendance.reason.form', ['type' => $type, 'message' => $message, 'record' => $attendanceRecord]);
            }



        }else{
            $message = 'Today you has checked in already';
            $messageType = 'error';
        }
        // Redirect to attendance index with success message
        $message1 = '';
        if ($user->office->under_radius_required == '1') {
            if ($request->distance > $user->office->radius) {
                if ($request->distance >= 1000) {
                    $distance = round($request->distance / 1000, 2) . ' km';
                } else {
                    $distance = round($request->distance) . ' m';
                }

                $required = $user->office->radius >= 1000
                    ? ($user->office->radius / 1000) . ' km'
                    : $user->office->radius . ' m';

                $message1 = "You are {$distance} away from the office, You should be under {$required}";
                // return back()->with('error', $message1);
            }
        }
        return redirect('attendance/day-wise')->with([$messageType => $message , 'error' => $message1]);
    }

    public function checkOut(Request $request, User $user = null){
        $request->validate([
            'image' => '',
            'latitude' => '',
            'longitude' => '',
            'distance' => '',
        ]);
        if ($user == null){
            $user = auth()->user();
        }
        if ($user->status == '0' || $user->office->status == 'inactive'){
            return back()->with('error', 'You are inactive user');
        }
        if ($user->location_required === 'yes' && (!$request->latitude || !$request->longitude)) {
            return view('dashboard.settingInstruction');
        }
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->where('user_id', $user->id)->first();
        if ($record){
            $duration = Carbon::now()->diffInMinutes($record->check_in);
            $record->update(['check_out' => Carbon::now(), 'duration' => $duration, 'check_out_distance' => $request->distance, 'day_type' => '__', 'check_out_latitude' => $request->latitude, 'check_out_longitude' => $request->longitude, 'check_out_by' => auth()->user()->id]);
        }else{
//            $duration = $user->office_time / 2;
//            $record = AttendanceRecord::create(['user_id' => $user->id, 'check_out' => Carbon::now(), 'duration' => $duration , 'check_out_distance' => $request->distance, 'check_out_latitude' => $request->latitude, 'check_out_longitude' => $request->logitude, 'check_out_by' => auth()->user()->id]);
            return back()->with('error', 'Error, You can\'t check-out without check-in');
        }
        if ($request->file('image')){
            $file = $request->file('image')->store('public/images');
            $record->check_out_image = str_replace('public/', '', $file);
            $record->save();
        }
//        if (!$request->latitude || !$request->longitude){
//            return view('dashboard.settingInstruction');
//        }

        if (Carbon::parse($record->check_out)->format('H:i:s') < Carbon::parse($user->check_out_time)->format('H:i:s') ){
            $checkOutTime = Carbon::parse($record->check_out)->format('H:i:s');
            $userCheckOutTime = Carbon::parse($user->check_out_time);
            $time = Carbon::createFromFormat('H:i:s', $checkOutTime)->diffInMinutes($userCheckOutTime);
            $before = HomeController::getTime($time);
            $message = 'You are checking out before '.$before.' write here the reasons';
            $type = 'check_out_note';
            return redirect()->route('attendance.reason.form', ['type' => $type, 'message' => $message, 'record' => $record]);
        }



        $diffMinutes = Carbon::parse($record->check_in)->diffInMinutes($record->check_out);

        // Required working minutes: based on user’s defined shift timing
        $requiredMinutes = Carbon::parse($user->check_in_time)
            ->diffInMinutes(Carbon::parse($user->check_out_time));

        // Remaining minutes (to complete required time)
        $remainingMinutes = max(0, $requiredMinutes - $diffMinutes);

        // Format check-in time for display
        $checkInTime = Carbon::parse($record->check_in)->format('h:i A');

        // If user is checking out before completing required working time
        if ($diffMinutes < $requiredMinutes) {

            // Convert required minutes to hours/min (for message)
            $reqHrs  = floor($requiredMinutes / 60);
            $reqMins = $requiredMinutes % 60;

//            $message = "You are checking out before completing {$reqHrs} hrs {$reqMins} mins.\n"
//                . "Check-in Time: {$checkInTime}\n"
//                . "Completed: " . floor($diffMinutes / 60) . " hrs " . ($diffMinutes % 60) . " mins\n"
//                . "Remaining: " . floor($remainingMinutes / 60) . " hrs " . ($remainingMinutes % 60) . " mins\n"
//                . "Please provide the reason for early check-out.";

            $message = "You are checking out early. Reason for early check-out?";

            $type = 'check_out_note';

            return redirect()->route('attendance.reason.form', [
                'type' => $type,
                'message' => $message,
                'record' => $record
            ]);
        }




        $message1 = '';
        if ($user->office->under_radius_required == '1') {
            if ($request->distance > $user->office->radius) {
                if ($request->distance >= 1000) {
                    $distance = round($request->distance / 1000, 2) . ' km';
                } else {
                    $distance = round($request->distance) . ' m';
                }

                $required = $user->office->radius >= 1000
                    ? ($user->office->radius / 1000) . ' km'
                    : $user->office->radius . ' m';

                $message1 = "You are {$distance} away from the office, You should be under {$required}";
                // return back()->with('error', $message1);
            }
        }

        return redirect('attendance/day-wise')->with(['success' => 'checked out successfully', 'error' => $message1]);
    }

    public function form($formType, User $user = null){

//        if (auth()->user()->is_accepted == '0'){
//            return back()->with('error', 'You don\'t have accepted policy, Please read policies!');
//        }
        return view('dashboard.attendance.form', compact('formType', 'user'));
    }

//    public function dayWise(Request $request){
//        if ($request->date){
//            $date = $request->date;
//        }else{
//            $date = today();
//        }
//        $employees = HomeController::employeeList();
//
//        $perPage = 20;
//        $currentPage = Paginator::resolveCurrentPage();
//        $employees = new LengthAwarePaginator(
//            $employees->forPage($currentPage, $perPage), // Get items for current page
//            $employees->count(),
//            $perPage,
//            $currentPage,
//            ['path' => Paginator::resolveCurrentPath()]
//        );
//
//        return view('dashboard.attendance.dayWise', compact('employees', 'date'));
//    }



    public function dayWise(Request $request)
    {
        $date = $request->date ?? today();

        // Get all employees
        $employees = HomeController::employeeList();

        // Convert to Laravel Collection if not already
        $employees = collect($employees);

        // Load attendance for the selected date
        $attendances = AttendanceRecord::whereDate('created_at', $date)->get()->keyBy('user_id');

        // Attach attendance status to each employee
        $employees = $employees->map(function ($employee) use ($attendances) {
            $employee->has_attendance = $attendances->has($employee->id); // or $employee->user_id
            return $employee;
        });

        // Sort: those with attendance first


        $employees = isset($request->status) ? $employees->where('status', $request->status)->sortByDesc('has_attendance')->values() : $employees->sortByDesc('has_attendance')->values();

        // Paginate
        $perPage = 20;
        $currentPage = Paginator::resolveCurrentPage();
        $paginatedEmployees = new LengthAwarePaginator(
            $employees->forPage($currentPage, $perPage),
            $employees->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath()]
        );

        return view('dashboard.attendance.dayWise', [
            'employees' => $paginatedEmployees,
            'date' => $date,
        ]);
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
            'note' => 'required|min:6',
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

    public function manualEntryForm(){
        $employees = HomeController::employeeList();
        return view('dashboard.attendance.manualEntryForm', compact('employees'));
    }

    public function store(Request $request){
        $request->validate([
            'employee_id' => 'required',
            'date' => 'required',
            'check_in' => 'required',
            'check_out' => 'required',
        ]);


        AttendanceRecord::create([
            'user_id' => $request->employee_id,
            'check_in' => $request->date.' '.$request->check_in,
            'check_out' => $request->date.' '.$request->check_out,
        ]);

        return back()->with('success', 'Attendance record created successfully');
    }
}
