<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\HomeController;
use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AttendanceRecordController extends Controller
{
    public function checkIn(Request $request, User $user = null)
    {
        $validatedData = $request->validate([
            'image' => '',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'distance' => 'nullable|numeric',
        ]);
        if ($user === null) {
            $user = $request->user();
        }
        if ($user->office->under_radius_required === '1') {
            if ($request->distance > $user->office->radius) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are ' . round($request->distance) . 'm of distance from the office. You should be under ' . $user->office->radius . 'm.',
                ], 403);
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
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to upload image. Please try again.',
                    ], 500);
                }
            }

            $attendanceRecord->save();

            if ($attendanceRecord->late) {
                $type = 'check_in_note';
                $time = HomeController::getTime($attendanceRecord->late);
                return response()->json([
                    'status' => 'warning',
                    'message' => 'You are ' . $time . ' late. Please provide a reason for being late.',
                    'data' => [
                        'type' => $type,
                        'attendance_record_id' => $attendanceRecord->id,
                    ],
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Checked in successfully.',
                'record' => $attendanceRecord,
            ], 200);
        } else {
            return response()->json([
                'status' => 'info',
                'message' => 'You have already checked in today.',
            ], 200);
        }
    }

    public function checkOut(Request $request, User $user = null){
        $request->validate([
            'image' => '',
            'latitude' => '',
            'longitude' => '',
            'distance' => '',
        ]);
        if ($user == null){
            $user = $request->user();
        }
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())->where('user_id', $user->id)->first();
        if ($record){
            $duration = Carbon::now()->diffInMinutes($record->check_in);
            $record->update(['check_out' => Carbon::now(), 'duration' => $duration, 'check_out_distance' => $request->distance, 'day_type' => '__', 'check_out_latitude' => $request->latitude, 'check_out_longitude' => $request->logitude, 'check_out_by' => auth()->user()->id]);
        }else{
//            $duration = $user->office_time / 2;
//            $record = AttendanceRecord::create(['user_id' => $user->id, 'check_out' => Carbon::now(), 'duration' => $duration , 'check_out_distance' => $request->distance, 'check_out_latitude' => $request->latitude, 'check_out_longitude' => $request->logitude, 'check_out_by' => auth()->user()->id]);
            return response()->json([
                'error' => 'Error, you cannot check-out without check-in'
            ], 400);
        }
        if ($request->file('image')){
            $file = $request->file('image')->store('public/images');
            $record->check_out_image = str_replace('public/', '', $file);
            $record->save();
        }
//        if (!$request->latitude || !$request->longitude){
//            return view('dashboard.settingInstruction');
//        }
        if (Carbon::parse($record->check_out)->format('H:i:s') < Carbon::parse($user->check_out_time)->format('H:i:s')){
            $checkOutTime = Carbon::parse($record->check_out)->format('H:i:s');
            $userCheckOutTime = Carbon::parse($user->check_out_time);
            $time = Carbon::createFromFormat('H:i:s', $checkOutTime)->diffInMinutes($userCheckOutTime);
            $before = HomeController::getTime($time);
//            return redirect()->route('attendance.reason.form', ['type' => $type, 'message' => $message, 'record' => $record]);
            return response()->json([
                'message' => 'You are checking out before ' . $before.' write here the reasons',
                'type' => 'check_out_note',
                'record' => $record,
            ], 200);
        }
        return response()->json([
            'message' => 'Checked out successfully',
            'record' => $record,
        ], 200);
    }


    public function dayWise(Request $request){
        $user = $request->user();
//        $user = User::find(1);
        if ($request->date){
            $date = Carbon::parse($request->date);
        }else{
            $date = Carbon::today();
        }
        $employees = HomeController::employeeList($user);
        foreach ($employees as $employee){
            $latest_attendance = AttendanceRecord::whereDate('created_at', $date)->where('user_id', $employee->id)->first();
            $employee->latest_attendance = $latest_attendance;
        }
        return response($employees);
    }

    public function monthlyRecord(Request $request)
    {
        // Validate the input
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'month' => 'nullable|date_format:Y-m',
        ]);

        // Determine the user
        $user = $request->user_id
            ? User::find($request->user_id)
            : $request->user();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }
        if ($request->user()->hasRole('super_admin|admin|owner|team_leader')){
            $employees = HomeController::employeeList($request->user());
        }else{
            $employees = null;
        }

        // Determine the month and calculate date range
        $month = $request->month ?: Carbon::now()->format('Y-m');
        $startOfMonth = Carbon::parse($month . '-01')->startOfMonth();
        $endOfMonth = Carbon::parse($month . '-01')->endOfMonth();

        // Get attendance records for the date range
        $attendanceRecords = AttendanceRecord::where('user_id', $user->id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function ($record) {
                return $record->created_at->format('Y-m-d');
            });

        // Generate the dates and associate records
        $dates = collect();
        for ($date = $startOfMonth; $date->lte($endOfMonth); $date->addDay()) {
            $dates->push([
                'date' => $date->toDateString(),
                'record' => $attendanceRecords->has($date->toDateString())
                    ? $attendanceRecords[$date->toDateString()]->first()
                    : null,
            ]);
        }

        return response()->json([
            'employees' => $employees,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
            'month' => $month,
            'attendance' => $dates,
        ]);
    }


}
