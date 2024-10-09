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
    public function index(){
        if (auth()->user()->hasRole('super_admin|admin|team_leader')){
            $office = auth()->user()->office;
            $leaves = Leave::where('office_id', $office->id)->get();
        }else{
            return back()->with('error', 'You don\'t have permission to access this url');
        }
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
        Mail::to($admin->email)->send(new LeaveRequest($leave));

        return redirect('userprofile/' . $user->id)->with('success', 'Leave request taken successfully and notification sent to admin.');
    }

    public function status(Leave $leave, $status){
        $leave->update(['status' => $status, 'responses_by' => auth()->user()->id]);
        Mail::to($leave->user->email)->send(new LeaveResponse($leave));
        return back()->with('success', 'Status updated successfully');
    }

    public function show($id){
        $leave = Leave::find($id);
        return view('dashboard.leave.show', compact('leave'));
    }
}
