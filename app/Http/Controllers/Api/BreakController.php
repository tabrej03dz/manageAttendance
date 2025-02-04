<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\LunchBreak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BreakController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?? today();

        // Get the authenticated user
        $user = $request->user();

        // Retrieve the employee list
        $employees = HomeController::employeeList($user);

        // Attach break details to each employee
        foreach ($employees as $employee) {
            $employee->breaks = LunchBreak::whereDate('created_at', $date)
                ->where('user_id', $employee->id)
                ->get();
        }

        // Return the response as JSON
        return response()->json([
            'success' => true,
            'date' => $date,
            'employees' => $employees,
        ]);
    }


    public function start(Request $request, User $employee = null)
    {

        // Determine the employee (current user if not provided)
        if (!$employee) {
            $employee = $request->user();
        }

        // Check if today's attendance record exists
        $record = AttendanceRecord::whereDate('created_at', Carbon::today())
            ->where('user_id', $employee->id)
            ->first();

        if ($record) {
            // Create the lunch break record
            $break = LunchBreak::create([
                'attendance_record_id' => $record->id,
                'user_id' => $employee->id,
                'start_time' => Carbon::now()->format('h:i'),
                'start_latitude' => $request->latitude,
                'start_longitude' => $request->longitude,
                'reason' => $request->reason,
                'start_distance' => $request->distance,
            ]);

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $photo = $request->file('image')->store('public/images');
                $break->start_image = str_replace('public/', '', $photo);
                $break->save();
            }

            $break = LunchBreak::find($break->id);
            // Return success response
            return response()->json([
                'message' => 'Break started successfully',
                'break' => $break,
            ], 200);
        } else {
            // Return error response
            return response()->json([
                'message' => 'Error: You can\'t take a break without checking in.',
            ], 400);
        }
    }

    public function stop(Request $request, User $employee = null)
    {
        $break = LunchBreak::find($request->break_id);
        try {
            $break->update([
                'end_time' => Carbon::now()->format('h:i'),
                'end_latitude' => $request->latitude,
                'end_longitude' => $request->longitude,
                'end_distance' => $request->distance,
            ]);

            if ($request->hasFile('image')) {
                $photo = $request->file('image')->store('public/images');
                $break->end_image = str_replace('public/', '', $photo);
                $break->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Break ended successfully',
                'data' => $break,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to end break. Please try again!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function latestBreak(Request $request, User $employee = null){
        if ($employee){
            $user = $employee;
        }else{
            $user = $request->user();
        }
        $break = LunchBreak::whereDate('created_at', Carbon::now()->format('Y-m-d'))->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();
        if ($break) {
            return response()->json([
                'success' => true,
                'data' => $break,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No lunch break record found.',
            ], 404); // 404 for "not found" or use 200 if it's not an error
        }
    }

    public function employeeBreak(Request $request)
    {
        // Validate request input
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id', // Ensure the employee exists
            'date' => 'nullable|date', // Ensure date is a valid format
        ]);



        // Get date (default: today)
        $date = isset($validated['date']) ? Carbon::parse($validated['date']) : today();



        // Fetch breaks for the specific employee
        $breaks = LunchBreak::where('user_id', $validated['employee_id'])
            ->whereDate('created_at', $date)
            ->get();

        return response($breaks);


        // Return response
        return response()->json([
            'success' => true,
            'date' => $date->toDateString(),
            'breaks' => $breaks
        ], 200);
    }


}
