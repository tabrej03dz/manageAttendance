<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceRecordController extends Controller
{
    public function checkIn(Request $request, User $user = null)
    {
        $validatedData = $request->validate([
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'distance' => 'nullable|numeric',
        ]);

        if ($user === null) {
            $user = auth()->user();
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
            ], 200);
        } else {
            return response()->json([
                'status' => 'info',
                'message' => 'You have already checked in today.',
            ], 200);
        }
    }

}
