<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(){
        if (auth()->user()->hasRole('super_admin')){
            $employees = User::role(['admin', 'employee', 'team_leader'])->get();
        }else{
            $office = auth()->user()->office;
            $employees = $office->users;
        }
        return view('dashboard.employee.index', compact('employees'));
    }

    public function create(){
        if (auth()->user()->hasRole('super_admin')){
            $offices = Office::all();
            $teamLeaders = User::role('team_leader')->get();
        }else{
            $offices = Office::where('id', auth()->user()->office_id)->get();
            $teamLeaders = User::where('office_id', auth()->user()->office_id)->role('team_leader')->get();
        }
        return view('dashboard.employee.create', compact('offices', 'teamLeaders'));
    }

    public function store(EmployeeRequest $request){
        $checkInTime = Carbon::parse($request->check_in_time);
        $checkOutTime = Carbon::parse($request->check_out_time);
        $employee = User::create($request->except('joining_date') + ['office_id' => $request->office_id, 'password' => Hash::make('password'), ]);
        if ($request->file('photo')){
            $file = $request->file('photo')->store('public/photos');
            $employee->photo = str_replace('public/', '', $file);
        }
        $employee->office_time = $checkInTime->diffInMinutes($checkOutTime);
        //$employee->joining_date = Carbon::createFromFormat('d-M-Y', $request->joining_date)->format('Y-m-d');
        $employee->joining_date = $request->joining_date;
        $employee->save();
        if ($request->role){
            $employee->assignRole($request->role);
        }else{
            $employee->assignRole('employee');
        }
        return redirect('employee')->with('success', 'Employee Registered successfully');
    }

    public function edit(User $employee){
        if (auth()->user()->hasRole('super_admin')){
            $offices = Office::all();
            $teamLeaders = User::role('team_leader')->get();
        }else{
            $offices = Office::where('id', auth()->user()->office_id)->get();
            $teamLeaders = User::where('office_id', auth()->user()->office_id)->role('team_leader')->get();
        }
        return view('dashboard.employee.edit', compact('employee', 'offices', 'teamLeaders'));
    }

    public function update(Request $request, User $employee){
        $employee->update($request->except(['password', 'photo', 'joining_date', 'office_id' => $request->office_id, 'team_leader_id' => $request->team_leader_id]));
        if ($request->filled('password')){
            $employee->password = Hash::make($request->password);
        }
        if ($request->file('photo')){
            if ($employee->photo){
                $file = public_path('storage/'. $employee->photo);
                if (file_exists($file)){
                    unlink($file);
                }
            }
            $file = $request->file('photo')->store('public/photos');
            $employee->photo = str_replace('public/', '', $file);
        }
        $employee->joining_date =  $request->joining_date;
        if ($request->role){
            $employee->assignRole($request->role);
        }
        $employee->save();
        return redirect('employee')->with('success', 'Record Updated successfully');
    }

    public function delete(User $employee){
        if ($employee->photo){
            $file = public_path('storage/'. $employee->photo);
            if (file_exists($file)){
                unlink($file);
            }
        }
        $employee->delete();
        return back()->with('success', 'Record Deleted Successfully');
    }

    public function employeeAttendance(){
        $employees = HomeController::employeeList();
        return view('dashboard.employee.list', compact('employees'));
    }

    public function status(User $employee){
        if ($employee->status == '1'){
            $employee->status = '0';
        }else{
            $employee->status = '1';
        }
        $response = $employee->save();
        if ($response){
            request()->session()->flash('success', 'Status changed successfully');
        }else{
            \request()->session()->flash('error', 'Error, Try again!');
        }
        return back();
    }
}
