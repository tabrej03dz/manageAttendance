<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSalary;
use Carbon\Carbon;
//use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user(); // Get the authenticated user
        $employees = HomeController::employeeList($user); // Fetch the employee list

        foreach ($employees as $employee){
            $employee->userSalary = $employee->userSalary;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => [
                'user' => $user,
                'employees' => $employees,
            ]
        ], 200); // Return a 200 HTTP status code
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => '',
            'phone' => '',
            'address' => '',
            'photo' => '',
            'joining_date' => '',
            'designation' => '',
            'responsibility' => '',
            'salary' => '',
            'check_in_time' => 'required',
            'check_out_time' => 'required',
            'basic_salary' => '',
            'dearness_allowance' => '',
            'relieving_charge' => '',
            'additional_allowance' => '',
            'provident_fund' => '',
            'employee_state_insurance_corporation' => '',
        ]);
        try {
            // Check if the email already exists
            $existingEmployee = User::where('email', $request->email)->first();
            if ($existingEmployee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email already exists.',
                ], 422);
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

            // Salary calculation
            $basic_salary = $request->basic_salary ?? 0;
            $house_rent_allowance = $request->house_rent_allowance ?? 0;
            $transport_allowance = $request->transport_allowance ?? 0;
            $medical_allowance = $request->medical_allowance ?? 0;
            $special_allowance = $request->special_allowance ?? 0;
            $dearness_allowance = $request->dearness_allowance ?? 0;
            $relieving_charge = $request->relieving_charge ?? 0;
            $additional_allowance = $request->additional_allowance ?? 0;

            $total_salary = $basic_salary + $house_rent_allowance + $transport_allowance + $medical_allowance + $special_allowance + $dearness_allowance + $relieving_charge + $additional_allowance;

            // Save the user's salary details
            $userSalary = UserSalary::create([
                'user_id' => $employee->id,
                'basic_salary' => $basic_salary,
                'house_rent_allowance' => $house_rent_allowance,
                'transport_allowance' => $transport_allowance,
                'medical_allowance' => $medical_allowance,
                'special_allowance' => $special_allowance,
                'dearness_allowance' => $dearness_allowance,
                'relieving_charge' => $relieving_charge,
                'additional_allowance' => $additional_allowance,
                'total_salary' => $total_salary,
            ]);

            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Employee registered successfully.',
                'employee' => $employee,
                'salary' => $userSalary,
            ], 201);

        } catch (\Exception $e) {
            // Handle exceptions and return error response
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {

        $employee = User::find($id);

        $request->validate([
            'name' => 'sometimes|required',
            'email' => 'sometimes|email',
            'phone' => 'nullable',
            'address' => 'nullable',
            'photo' => 'nullable|image',
            'joining_date' => 'nullable',
            'designation' => 'nullable',
            'responsibility' => 'nullable',
            'salary' => 'nullable',
            'check_in_time' => 'nullable',
            'check_out_time' => 'nullable',
            'basic_salary' => 'nullable',
            'dearness_allowance' => 'nullable',
            'relieving_charge' => 'nullable',
            'additional_allowance' => 'nullable',
            'provident_fund' => 'nullable',
            'employee_state_insurance_corporation' => 'nullable',
        ]);

        try {
            // Find the employee by ID
//            $employee = User::where('id', 37)->first();
            if (!$employee) {
                return response()->json(['error' => 'Employee not found.'], 404);
            }

            // Update employee details
            $employee->update($request->except(['photo', 'joining_date', 'check_in_time', 'check_out_time']));

            // Handle photo upload
            if ($request->file('photo')) {
                $file = $request->file('photo')->store('public/photos');
                $employee->photo = str_replace('public/', '', $file);
            }

            // Update check-in and check-out times
            if ($request->check_in_time && $request->check_out_time) {
                $checkInTime = Carbon::parse($request->check_in_time);
                $checkOutTime = Carbon::parse($request->check_out_time);
                $employee->office_time = $checkInTime->diffInMinutes($checkOutTime);
            }

            // Update joining date
            if ($request->joining_date) {
                $employee->joining_date = $request->joining_date;
            }

            // Save the updated employee record
            $employee->save();

            // Update or calculate salary
            $basic_salary = $request->basic_salary ?? $employee->userSalry?->basic_salary ?? 0;
            $house_rent_allowance = $request->house_rent_allowance ?? $employee->userSalary?->house_rent_allowance ?? 0;
            $transport_allowance = $request->transport_allowance ?? $employee->userSalary?->transport_allowance ?? 0;
            $medical_allowance = $request->medical_allowance ?? $employee->userSalary?->medical_allowance ?? 0;
            $special_allowance = $request->special_allowance ?? $employee->userSalary?->special_allowance ?? 0;
            $dearness_allowance = $request->dearness_allowance ?? $employee->userSalary?->dearness_allowance ?? 0;
            $relieving_charge = $request->relieving_charge ?? $employee->userSalary?->relieving_charge ?? 0;
            $additional_allowance = $request->additional_allowance ?? $employee->userSalary?->additional_allowance ?? 0;

            $total_salary = $basic_salary + $house_rent_allowance + $transport_allowance + $medical_allowance + $special_allowance + $dearness_allowance + $relieving_charge + $additional_allowance;

            // Update the user's salary details
            $userSalary = $employee->userSalary;
            $userSalary->update([
                'basic_salary' => $basic_salary,
                'house_rent_allowance' => $house_rent_allowance,
                'transport_allowance' => $transport_allowance,
                'medical_allowance' => $medical_allowance,
                'special_allowance' => $special_allowance,
                'dearness_allowance' => $dearness_allowance,
                'relieving_charge' => $relieving_charge,
                'additional_allowance' => $additional_allowance,
                'total_salary' => $total_salary,
            ]);

            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Employee updated successfully.',
                'employee' => $employee,
                'salary' => $userSalary,
            ], 200);

        } catch (\Exception $e) {
            // Handle exceptions and return error response
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        $employee = User::where('id', $request->employee_id)->first();
        try {
            // Find the employee by ID
            if (!$employee) {
                return response()->json(['error' => 'Employee not found.'], 404);
            }

            // Delete the employee
            $employee->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Employee deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function teamLeaders(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            $teamleaders = $user->office->users()->whereHas('roles', function ($query) {
                $query->where('name', 'team_leader'); // Ensure the role name is correct
            })->get();
        } else {
            $teamleaders = null;
        }

        return response()->json([
            'team_leaders' => $teamleaders
        ], 200);
    }




}
