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

    public function stop(Request $request, LunchBreak $break)
    {
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


}
