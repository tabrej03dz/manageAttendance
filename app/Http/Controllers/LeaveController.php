<?php

namespace App\Http\Controllers;


use App\Mail\LeaveRequest;
use App\Mail\LeaveResponse;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Leave;
use Illuminate\Support\Facades\Mail;


class LeaveController extends Controller
{
    public function index(Request $request){
        $user = auth()->user();
        $query = Leave::query();



        if ($user->hasRole('super_admin')) {
            $query->where(function ($q) {
                $q->whereDate('start_date', '>=', today())
                    ->orWhereDate('end_date', '>=', today());
            });
        }

        elseif ($user->hasRole('admin')) {
            $userIds = $user->office->users->pluck('id');
            $query->whereIn('user_id', $userIds)
                ->where(function ($q) {
                    $q->whereDate('start_date', '>=', today())
                        ->orWhereDate('end_date', '>=', today());
                });
        }

        elseif ($user->hasRole('team_leader')) {
            $userIds = $user->members->pluck('id');
            $query->whereIn('user_id', $userIds)
                ->where(function ($q) {
                    $q->whereDate('start_date', '>=', today())
                        ->orWhereDate('end_date', '>=', today());
                });
        }else{

            $query->where('user_id', $user->id)
                ->where(function ($q) {
                    $q->whereDate('start_date', '>=', today())
                        ->orWhereDate('end_date', '>=', today());
                });
        }
        if ($request->status){
            $query->where('status', $request->status);
        }
        $leaves = $query->get();
        return view('dashboard.leave.index', compact('leaves'));
    }


    public function create(){
        return view('dashboard.leave.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $leave = Leave::create($request->all() + ['user_id' => $user->id, 'office_id' => $user->office->id]);

        $admin = User::where('office_id', $user->office->id)
            ->whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })
            ->first();
        $superAdmin = User::role('super_admin')->first();
        Mail::to($superAdmin->email)->send(new LeaveRequest($leave));
        if($admin){
            Mail::to($admin->email)->send(new LeaveRequest($leave));
        }

        return redirect('userprofile/' . $user->id)->with('success', 'Leave request taken successfully and notification sent to admin.');
    }

    public function status(Leave $leave, $status){
        $leave->update(['status' => $status, 'responses_by' => auth()->user()->id]);
        Mail::to($leave->user->email1 ?? $leave->user->email)->send(new LeaveResponse($leave));
        return back()->with('success', 'Status updated successfully');
    }

    public function show($id){
        $leave = Leave::find($id);
        return view('dashboard.leave.show', compact('leave'));
    }
}
