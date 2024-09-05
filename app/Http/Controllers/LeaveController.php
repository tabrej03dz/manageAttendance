<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeaveRequest;
use Illuminate\Http\Request;
use App\Models\Leave;

class LeaveController extends Controller
{
    public function index(){
        if (auth()->user()->hasRole('super_admin|admin')){
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

    public function store(LeaveRequest $request){
        $user = auth()->user();
        Leave::create($request->all() + ['user_id' => $user->id, 'office_id' => $user->office->id]);
        return redirect('userprofile')->with('success', 'Leave request taken successfully');
    }

    public function status(Leave $leave, $status){
        $leave->update(['status' => $status, 'responses_by' => auth()->user()->id]);
        return back()->with('success', 'Status updated successfully');
    }
}
