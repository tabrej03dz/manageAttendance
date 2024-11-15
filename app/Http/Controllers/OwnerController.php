<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;
use Spatie\Permission\Models\Role;

class OwnerController extends Controller
{
    public function index(){

        $owners = User::role('owner')->get();
        return view('dashboard.owner.index', compact('owners'));
    }

    public function create(){

        return view('dashboard.owner.create');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => '',
            'photo' => '',
            'number_of_offices' => 'required',
            'number_of_employees' => 'required',
            'duration' => 'required',
            'price' => 'required',
            'start_date' => '',
        ]);

        $owner = User::create($request->all() + ['password' => Hash::make('password')]);
        if ($owner){
            $owner->assignRole('owner');
            if ($request->hasFile('photo')){
                $photo = $request->file('photo')->store('public/photos');
                $owner->photo = str_replace('public/', '', $photo);
                $owner->save();
            }

            $plan = Plan::create([
                'number_of_offices' => $request->number_of_offices,
                'number_of_employees' => $request->number_of_employees,
                'duration' => $request->number_of_employees,
                'start_date' => $request->start_date ?? Carbon::today(),
                'price' => $request->price,
                'expiry_date' => $request->start_date ? Carbon::createFromFormat('Y-m-d', $request->input('start_date'))->addDays($request->duration)->toDateString() : Carbon::today()->addDays($package->duration)->toDateString(),
                'user_id' => $owner->id,
            ]);
            request()->session()->flash('success', 'Owner Creates successfully');
        }else{
            request()->session()->flash('error', 'Failed, Try again!');
        }
        return redirect('owner');
    }

    public function edit(User $owner){
        return view('dashboard.owner.edit', compact('owner'));
    }

    public function delete(User $owner){
        $owner->delete();
        return back()->with('success', 'Owner Deleted successfully');
    }
}
