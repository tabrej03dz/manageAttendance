<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\LeaveRequest;
use App\Mail\LeaveResponse;
use App\Models\Leave;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;




class LeaveController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Leave::query();

        if ($user->hasRole('super_admin')) {
            $query->where(function ($q) {
                $q->whereDate('start_date', '>=', today())
                    ->orWhereDate('end_date', '>=', today());
            });
        } elseif ($user->hasRole('admin')) {
            $userIds = $user->office->users->pluck('id');
            $query->whereIn('user_id', $userIds)
                ->where(function ($q) {
                    $q->whereDate('start_date', '>=', today())
                        ->orWhereDate('end_date', '>=', today());
                });
        } elseif ($user->hasRole('team_leader')) {
            $userIds = $user->members->pluck('id');
            $query->whereIn('user_id', $userIds)
                ->where(function ($q) {
                    $q->whereDate('start_date', '>=', today())
                        ->orWhereDate('end_date', '>=', today());
                });
        } else {
            $query->where('user_id', $user->id)
                ->where(function ($q) {
                    $q->whereDate('start_date', '>=', today())
                        ->orWhereDate('end_date', '>=', today());
                });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $leaves = $query->get();

        return response()->json([
            'success' => true,
            'data' => $leaves,
        ], 200);
    }


    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required',
            'is_paid' => 'required',
            'start_date'=> 'required|date',
            'end_date' => 'nullable|date',
            'subject' => 'nullable',
            'reason' => 'nullable|string',
        ]);

        $user = $request->user();

        // Calculate the difference in days if both start_date and end_date are provided
        if ($request->start_date && $request->end_date) {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $dayCount = $startDate->diffInDays($endDate);
        } else {
            $dayCount = 1; // Default to 1 day if no end_date is provided
        }

        try {
            // Create the leave request
            $leave = Leave::create($request->all() + [
                    'user_id' => $user->id,
                    'office_id' => $user->office->id,
                    'day_count' => $dayCount,
                ]);

            // Fetch admin and super admin for notifications
            $admin = User::where('office_id', $user->office->id)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'admin');
                })
                ->first();

            $superAdmin = User::role('super_admin')->first();

            // Send email notifications
            if ($superAdmin) {
                Mail::to($superAdmin->email)->send(new LeaveRequest($leave));
            }

            if ($admin) {
                Mail::to($admin->email)->send(new LeaveRequest($leave));
            }

            return response()->json([
                'success' => true,
                'message' => 'Leave request submitted successfully.',
                'data' => $leave,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the leave request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function status(Request $request, Leave $leave, $status, $type = null)
    {
        $user = $request->user();
        try {
            $leave->update([
                'status' => $status,
                'responses_by' => $user->id,
                'approve_as' => $type
            ]);

            Mail::to($leave->user->email1 ?? $leave->user->email)->send(new LeaveResponse($leave));

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
                'data' => $leave,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getLeaveByDate(Request $request,$date, User $user)
    {
        try {
            $leave = Leave::whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->where('user_id', $user->id)
                ->first();

            if ($leave) {
                return response()->json([
                    'success' => true,
                    'data' => $leave,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No leave found for the specified date.',
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve leave.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
