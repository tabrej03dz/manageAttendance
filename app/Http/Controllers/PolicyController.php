<?php

namespace App\Http\Controllers;

use App\Mail\PolicyAcceptanceNotification;
use App\Models\Policy;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PolicyController extends Controller
{
    public function index(){
        $policies = Policy::where('office_id', auth()->user()->office_id)->get();
        return view('dashboard.policy.index', compact('policies'));
    }
    public function create(){
        if (auth()->user()->hasRole('super_admin')){
            $offices = Office::all();
        }else{
            $offices = null;
        }
        return view('dashboard.policy.create', compact('offices'));
    }

    public function store(Request $request){
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'office_id' => 'sometimes',
        ]);

        $policy = Policy::create($request->all() + ['office_id' => $request->office_id ?? auth()->user()->office_id]);

        if ($policy) {
            request()->session()->flash('success', 'Policy Created successfully');
        }else{
            request()->session()->flash('error', 'Failed, Try again!');
        }
        return redirect('policy');
    }

    public function edit(Policy $policy){
        if (auth()->user()->hasRole('super_admin')){
            $offices = Office::all();
        }else{
            $offices = null;
        }
        return view('dashboard.policy.edit', compact('offices', 'policy'));
    }

    public function update(Request $request, Policy $policy){
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'office_id' => 'sometimes',
        ]);
        $status = $policy->update($request->all());
        if ($status){
            request()->session()->flash('success', 'Updated successfully');
        }else{
            request()->session()->flash('error', 'Failed, Try again!');
        }
        return redirect('policy');
    }

    public function delete(Policy $policy){
        $status = $policy->delete();
        if ($status){
            \request()->session()->flash('success', 'Deleted successfully');
        }else{
            \request()->session()->flash('error', 'Failed, Try again!');
        }
        return back();
    }

    public function read(Policy $policy = null){
        if ($policy == null){
            $policy = auth()->user()->office->policy;
        }
        return view('dashboard.policy.read', compact('policy'));
    }

    public function accept(Policy $policy){
        $user = auth()->user();
        $user->is_accepted = '1';
        $status = $user->save();
        if ($status){
            Mail::to($user->email1 ?? $user->email)->send(new PolicyAcceptanceNotification($policy, $user));
            request()->session()->flash('success', 'Thanks for agreeing to our policy!');
        }else{
            \request()->session()->flash('error', 'Failed, Try again!');
        }
        return redirect('home');

    }
}
