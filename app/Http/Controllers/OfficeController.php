<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function index(){
        if (auth()->user()->hasRole('owner')){
            $offices = auth()->user()->offices;
        }else{
            $offices = Office::all();
        }
        return view('dashboard.office.index', compact('offices'));
    }

    public function create(){
        if (auth()->user()->hasRole('super_admin')){
            $owners = User::role('owner')->get();
        }else{
            $user = auth()->user();
            $plan = Plan::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
            if ($plan->number_of_offices <= $user->offices->count()){
                return back()->with('error', 'You Office creation limit is exceeded');
            }
            $owners = null;
        }
        return view('dashboard.office.create', compact('owners'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => '',
            'number_of_employees' => '',
        ]);
        Office::create($request->all());
        return redirect('office')->with('success', 'Office Created successfully');
    }

    public function edit(Office $office){
        return view('dashboard.office.edit', compact('office'));
    }

    public function update(Request $request, Office $office){
//        dd($request->all());
        $request->validate([
            'name' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => '',
            'number_of_employees' => '',
            'price_per_employee' => '',
            'under_radius_required' => '',
        ]);

        $office->update($request->all());

        return redirect('office')->with('success', 'Updated successfully');
    }

    public function delete(Office $office){
        $office->delete();
        return back()->with('success', 'Deleted successfully');
    }

    public function status(Office $office){
        if ($office->status == 'active'){
            $office->status = 'inactive';
        }else{
            $office->status = 'active';
        }
        $response = $office->save();
        if ($response){
            request()->session()->flash('success', 'Status changed successfully');
        }else{
            \request()->session()->flash('error', 'Error, Try again!');
        }
        return back();
    }

    public function detail(Office $office){
        $payments = $office->payments;
        return view('dashboard.office.detail', compact('payments', 'office'));
    }
}
