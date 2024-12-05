<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\AttendanceRecord;
use App\Models\Office;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class EmployeeController extends Controller
{
    public function index(){
//        if (auth()->user()->hasRole('super_admin')){
//            $employees = User::role(['admin', 'employee', 'team_leader'])->get();
//        }else{
//            $office = auth()->user()->office;
//            $employees = $office->users;
//        }

        $employees = HomeController::employeeList();
        return view('dashboard.employee.index', compact('employees'));
    }

    public function create(){
        if (auth()->user()->hasRole('super_admin')){
            $offices = Office::all();
            $teamLeaders = User::role('team_leader')->get();
        }else{
            if (auth()->user()->hasRole('owner')){
                $user = auth()->user();
            }else{
                $user = auth()->user()->office->owner;
            }
            $plan = Plan::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
            $employeeCount = 0;
            foreach ($user->offices as $office){
                $employeeCount += $office->users->count();
            }
            if ($employeeCount >= $plan->number_of_employees){
                return back()->with('error', 'Your employee creation limit exceeded!');
            }
            $offices = Office::where('owner_id', auth()->user()->id)->get();
            $teamLeaders = User::where('office_id', auth()->user()->office_id)->role('team_leader')->get();
        }
        return view('dashboard.employee.create', compact('offices', 'teamLeaders'));
    }

    public function store(EmployeeRequest $request)
    {
        // Check if the email already exists
        $existingEmployee = User::where('email', $request->email)->first();
        if ($existingEmployee) {
            return back()->withErrors(['email' => 'Email already exists.'])->withInput();
        }

        // Parse check-in and check-out times
        $checkInTime = Carbon::parse($request->check_in_time);
        $checkOutTime = Carbon::parse($request->check_out_time);

        // Create the employee record
        $employee = User::create($request->except('joining_date') + [
                'office_id' => $request->office_id,
                'password' => Hash::make('password'),
            ]);

        // Handle photo upload
        if ($request->file('photo')) {
            $file = $request->file('photo')->store('public/photos');
            $employee->photo = str_replace('public/', '', $file);
        }

        // Calculate office time and save the joining date
        $employee->office_time = $checkInTime->diffInMinutes($checkOutTime);
        $employee->joining_date = $request->joining_date;
        $employee->save();


        // Assign role to employee
        if ($request->role) {
            $employee->assignRole($request->role);
        } else {
            $employee->assignRole('employee');
        }

        // Redirect with success message
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
        $user = auth()->user();
        if ($user->hasRole('super_admin')){
            $employees = User::all();
        }else{
            $employees = User::where('office_id', $user->office_id)->get();
        }
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

    public function permission(User $user){
        $permissions = $user->permissions;
        return view('dashboard.employee.permission', compact('permissions', 'user'));
    }

    public function permissionRemove(Permission $permission, User $user){
        if ($user->hasPermissionTo($permission)) { // Check if the user has the permission
            $user->revokePermissionTo($permission); // Remove the permission from the user
            return back()->with('success', 'Permission removed from the user successfully.');
        }
        return back()->with('error', 'User does not have this permission.');
    }
}
