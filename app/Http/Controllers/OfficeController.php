<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OfficeController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('owner')) {
            $offices = auth()->user()->offices;
        } else {
            $offices = Office::all();
        }

        return view('dashboard.office.index', compact('offices'));
    }

    public function create()
    {
        if (auth()->user()->hasRole('super_admin')) {
            $owners = User::role('owner')->get();
        } else {
            $user = auth()->user();
            $plan = Plan::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();

            if ($plan && $plan->number_of_offices <= $user->offices->count()) {
                return back()->with('error', 'Your office creation limit is exceeded');
            }

            $owners = null;
        }

        return view('dashboard.office.create', compact('owners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                   => 'required|string|max:255',
            'latitude'               => 'required',
            'longitude'              => 'required',
            'radius'                 => 'nullable',
            'number_of_employees'    => 'nullable',
            'owner_id'               => 'nullable|exists:users,id',
            'address'                => 'nullable|string',
            'under_radius_required'  => 'nullable|boolean',
            'otp_enable'             => 'nullable|boolean',
            'logo'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('logo');

        $data['otp_enable'] = $request->boolean('otp_enable');
        $data['under_radius_required'] = $request->boolean('under_radius_required');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('office-logos', 'public');
        }

        Office::create($data);

        return redirect('office')->with('success', 'Office created successfully');
    }

    public function edit(Office $office)
    {
        if (auth()->user()->hasRole('super_admin')) {
            $owners = User::role('owner')->get();
        } else {
            $owners = null;
        }

        return view('dashboard.office.edit', compact('office', 'owners'));
    }

    public function update(Request $request, Office $office)
    {

        $request->validate([
            'name'                   => 'required|string|max:255',
            'latitude'               => 'required',
            'longitude'              => 'required',
            'radius'                 => 'nullable',
            'number_of_employees'    => 'nullable',
            'price_per_employee'     => 'nullable',
            'owner_id'               => 'nullable|exists:users,id',
            'address'                => 'nullable|string',
            'under_radius_required'  => 'nullable|boolean',
            'otp_enable'             => 'nullable|boolean',
            'logo'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('logo');

        $data['otp_enable'] = $request->boolean('otp_enable');
        $data['under_radius_required'] = $request->boolean('under_radius_required');

        if ($request->hasFile('logo')) {
            if ($office->logo && Storage::disk('public')->exists($office->logo)) {
                Storage::disk('public')->delete($office->logo);
            }

            $data['logo'] = $request->file('logo')->store('office-logos', 'public');
        }

        $office->update($data);

        return redirect('office')->with('success', 'Updated successfully');
    }

    public function delete(Office $office)
    {
        if ($office->logo && Storage::disk('public')->exists($office->logo)) {
            Storage::disk('public')->delete($office->logo);
        }

        $office->delete();

        return back()->with('success', 'Deleted successfully');
    }

    public function status(Office $office)
    {
        $office->status = $office->status == 'active' ? 'inactive' : 'active';

        $response = $office->save();

        if ($response) {
            request()->session()->flash('success', 'Status changed successfully');
        } else {
            request()->session()->flash('error', 'Error, Try again!');
        }

        return back();
    }

    public function detail(Office $office)
    {
        $payments = $office->payments;

        return view('dashboard.office.detail', compact('payments', 'office'));
    }
}