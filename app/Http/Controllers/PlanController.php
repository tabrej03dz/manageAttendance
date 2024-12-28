<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    public function ownerPlan(User $owner){
        $plans = Plan::where('user_id', $owner->id)->latest()->get();

        return view('dashboard.plan.index', compact('plans'));
    }

    public function edit(Plan $plan){
        return view('dashboard.plan.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan){
        $request->validate([
            'number_of_offices' => 'required',
            'number_of_employees' => 'required',
            'duration' => 'required',
            'price' => 'required',
            'start_date' => '',
        ]);

//        dd($request->start_date ? Carbon::createFromFormat('Y-m-d', $request->input('start_date'))->addDays($request->duration)->toDateString() : Carbon::today()->addDays($plan->duration)->toDateString());

        $status = $plan->update([
            'number_of_offices' => $request->number_of_offices,
            'number_of_employees' => $request->number_of_employees,
            'duration' => $request->number_of_employees,
            'start_date' => $request->start_date ?? Carbon::today(),
            'end_date' => $request->start_date
                ? Carbon::createFromFormat('Y-m-d', $request->input('start_date'))->addDays($request->duration)->toDateString()
                : Carbon::today()->addDays($plan->duration)->toDateString(),
            'price' => $request->price,
        ]);
        if ($status){
            request()->session()->flash('success', 'Plan updated successfully');
        }
        else{
            request()->session()->flash('error', 'Failed, Try again!');
        }
        return redirect()->route('owner.plan', ['owner' => $plan->user_id]);
    }

    public function status(Plan $plan){
        if ($plan->status == '1'){
            $plan->update(['status' => '0']);
        }else{
            $plan->update(['status' => '1']);
        }
        return back()->with('success', 'Status updated successfully');
    }
}
