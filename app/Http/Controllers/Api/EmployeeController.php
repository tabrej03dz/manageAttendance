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
    // public function index(Request $request)
    // {
    //     $user = $request->user(); // Get the authenticated user
    //     $employees = HomeController::employeeList($user); // Fetch the employee list

    //     foreach ($employees as $employee){
    //         $employee->role = $employee->roles->first();
    //     }
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Data retrieved successfully',
    //         'data' => [
    //             'user' => $user,
    //             'employees' => $employees,
    //         ]
    //     ], 200); // Return a 200 HTTP status code
    // }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'name' => 'required',
//             'email' => '',
//             'phone' => '',
//             'address' => '',
//             'photo' => '',
//             'joining_date' => '',
//             'designation' => '',
//             'responsibility' => '',
//             'salary' => '',
//             'check_in_time' => 'required',
//             'check_out_time' => 'required',
//             'basic_salary' => '',
//             'dearness_allowance' => '',
//             'relieving_charge' => '',
//             'additional_allowance' => '',
//             'provident_fund' => '',
//             'employee_state_insurance_corporation' => '',
//         ]);
//         try {
//             // Check if the email already exists
//             $existingEmployee = User::where('email', $request->email)->first();
//             if ($existingEmployee) {
//                 return response()->json([
//                     'status' => 'error',
//                     'message' => 'Email already exists.',
//                 ], 422);
//             }

//             // Parse check-in and check-out times
//             $checkInTime = Carbon::parse($request->check_in_time);
//             $checkOutTime = Carbon::parse($request->check_out_time);

//             // Create the employee record
//             $employee = User::create($request->except('joining_date') + [
//                     'office_id' => $request->office_id,
//                     'password' => Hash::make('password'),
//                 ]);

//             // Handle photo upload
//             if ($request->file('photo')) {
//                 $file = $request->file('photo')->store('public/photos');
//                 $employee->photo = str_replace('public/', '', $file);
//             }

//             if ($request->file('aadhar_attachment')) {
//                 $file = $request->file('aadhar_attachment')->store('public/photos');
//                 $employee->photo = str_replace('public/', '', $file);
//             }
//             if ($request->file('pan_attachment')) {
//                 $file = $request->file('pan_attachment')->store('public/photos');
//                 $employee->photo = str_replace('public/', '', $file);
//             }

//             if ($request->file('other_attachment')) {
//                 $file = $request->file('other_attachment')->store('public/photos');
//                 $employee->photo = str_replace('public/', '', $file);
//             }

//             // Calculate office time and save the joining date
//             $employee->office_time = $checkInTime->diffInMinutes($checkOutTime);
//             $employee->joining_date = $request->joining_date;
//             $employee->save();

//             // Assign role to employee
//             if ($request->role) {
//                 $employee->assignRole($request->role);
//             } else {
//                 $employee->assignRole('employee');
//             }

//             // Salary calculation
//             $basic_salary = $request->basic_salary ?? 0;
//             $house_rent_allowance = $request->house_rent_allowance ?? 0;
//             $transport_allowance = $request->transport_allowance ?? 0;
//             $medical_allowance = $request->medical_allowance ?? 0;
//             $special_allowance = $request->special_allowance ?? 0;
//             $dearness_allowance = $request->dearness_allowance ?? 0;
//             $relieving_charge = $request->relieving_charge ?? 0;
//             $additional_allowance = $request->additional_allowance ?? 0;

//             $total_salary = $basic_salary + $house_rent_allowance + $transport_allowance + $medical_allowance + $special_allowance + $dearness_allowance + $relieving_charge + $additional_allowance;

//             // Save the user's salary details
//             $userSalary = UserSalary::create([
//                 'user_id' => $employee->id,
//                 'basic_salary' => $basic_salary,
//                 'house_rent_allowance' => $house_rent_allowance,
//                 'transport_allowance' => $transport_allowance,
//                 'medical_allowance' => $medical_allowance,
//                 'special_allowance' => $special_allowance,
//                 'dearness_allowance' => $dearness_allowance,
//                 'relieving_charge' => $relieving_charge,
//                 'additional_allowance' => $additional_allowance,
//                 'total_salary' => $total_salary,
//             ]);

//             // Return a success response
//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'Employee registered successfully.',
//                 'employee' => $employee,
//                 'salary' => $userSalary,
//             ], 201);

//         } catch (\Exception $e) {
//             // Handle exceptions and return error response
//             return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
//         }
//     }

//     public function update(Request $request, $id)
//     {

//         $employee = User::find($id);

//         $request->validate([
//             'name' => 'sometimes|required',
//             'email' => 'sometimes|email',
//             'phone' => 'nullable',
//             'address' => 'nullable',
//             'photo' => 'nullable|image',
//             'joining_date' => 'nullable',
//             'designation' => 'nullable',
//             'responsibility' => 'nullable',
//             'salary' => 'nullable',
//             'check_in_time' => 'nullable',
//             'check_out_time' => 'nullable',
//             'basic_salary' => 'nullable',
//             'dearness_allowance' => 'nullable',
//             'relieving_charge' => 'nullable',
//             'additional_allowance' => 'nullable',
//             'provident_fund' => 'nullable',
//             'employee_state_insurance_corporation' => 'nullable',
//         ]);

//         try {
//             // Find the employee by ID
// //            $employee = User::where('id', 37)->first();
//             if (!$employee) {
//                 return response()->json(['error' => 'Employee not found.'], 404);
//             }

//             // Update employee details
//             $employee->update($request->except(['photo', 'joining_date', 'check_in_time', 'check_out_time']));

//             // Handle photo upload
//             if ($request->file('photo')) {
//                 $file = $request->file('photo')->store('public/photos');
//                 $employee->photo = str_replace('public/', '', $file);
//             }

//             if ($request->file('aadhar_attachment')) {
//                 $file = $request->file('aadhar_attachment')->store('public/photos');
//                 $employee->photo = str_replace('public/', '', $file);
//             }
//             if ($request->file('pan_attachment')) {
//                 $file = $request->file('pan_attachment')->store('public/photos');
//                 $employee->photo = str_replace('public/', '', $file);
//             }

//             if ($request->file('other_attachment')) {
//                 $file = $request->file('other_attachment')->store('public/photos');
//                 $employee->photo = str_replace('public/', '', $file);
//             }

//             // Update check-in and check-out times
//             if ($request->check_in_time && $request->check_out_time) {
//                 $checkInTime = Carbon::parse($request->check_in_time);
//                 $checkOutTime = Carbon::parse($request->check_out_time);
//                 $employee->office_time = $checkInTime->diffInMinutes($checkOutTime);
//             }

//             // Update joining date
//             if ($request->joining_date) {
//                 $employee->joining_date = $request->joining_date;
//             }

//             // Save the updated employee record
//             $employee->save();

//             // Update or calculate salary
//             $basic_salary = $request->basic_salary ?? $employee->userSalry?->basic_salary ?? 0;
//             $house_rent_allowance = $request->house_rent_allowance ?? $employee->userSalary?->house_rent_allowance ?? 0;
//             $transport_allowance = $request->transport_allowance ?? $employee->userSalary?->transport_allowance ?? 0;
//             $medical_allowance = $request->medical_allowance ?? $employee->userSalary?->medical_allowance ?? 0;
//             $special_allowance = $request->special_allowance ?? $employee->userSalary?->special_allowance ?? 0;
//             $dearness_allowance = $request->dearness_allowance ?? $employee->userSalary?->dearness_allowance ?? 0;
//             $relieving_charge = $request->relieving_charge ?? $employee->userSalary?->relieving_charge ?? 0;
//             $additional_allowance = $request->additional_allowance ?? $employee->userSalary?->additional_allowance ?? 0;

//             $total_salary = $basic_salary + $house_rent_allowance + $transport_allowance + $medical_allowance + $special_allowance + $dearness_allowance + $relieving_charge + $additional_allowance;

//             // Update the user's salary details
//             $userSalary = $employee->userSalary;
//             if ($userSalary){
//                 $userSalary->update([
//                     'basic_salary' => $basic_salary,
//                     'house_rent_allowance' => $house_rent_allowance,
//                     'transport_allowance' => $transport_allowance,
//                     'medical_allowance' => $medical_allowance,
//                     'special_allowance' => $special_allowance,
//                     'dearness_allowance' => $dearness_allowance,
//                     'relieving_charge' => $relieving_charge,
//                     'additional_allowance' => $additional_allowance,
//                     'total_salary' => $total_salary,
//                 ]);
//             }else{
//                 $userSalary->create([
//                     'user_id' => $employee->id,
//                     'basic_salary' => $basic_salary,
//                     'house_rent_allowance' => $house_rent_allowance,
//                     'transport_allowance' => $transport_allowance,
//                     'medical_allowance' => $medical_allowance,
//                     'special_allowance' => $special_allowance,
//                     'dearness_allowance' => $dearness_allowance,
//                     'relieving_charge' => $relieving_charge,
//                     'additional_allowance' => $additional_allowance,
//                     'total_salary' => $total_salary,
//                 ]);
//             }

//             // Return a success response
//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'Employee updated successfully.',
//                 'employee' => $employee,
//                 'salary' => $userSalary,
//             ], 200);

//         } catch (\Exception $e) {
//             // Handle exceptions and return error response
//             return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
//         }
//     }

//     public function delete(Request $request)
//     {
//         $employee = User::where('id', $request->employee_id)->first();
//         try {
//             // Find the employee by ID
//             if (!$employee) {
//                 return response()->json(['error' => 'Employee not found.'], 404);
//             }

//             // Delete the employee
//             $employee->delete();

//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'Employee deleted successfully.',
//             ], 200);

//         } catch (\Exception $e) {
//             // Handle exceptions
//             return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
//         }
//     }

//     public function teamLeaders(Request $request)
//     {
//         $user = $request->user();

//         if ($user->hasRole('admin')) {
//             $teamleaders = $user->office->users()->whereHas('roles', function ($query) {
//                 $query->where('name', 'team_leader'); // Ensure the role name is correct
//             })->get();
//         } else {
//             $teamleaders = null;
//         }

//         return response()->json([
//             'team_leaders' => $teamleaders
//         ], 200);
//     }







    // private function resolveOfficeId(Request $request, $user)
    // {
    //     if ($request->filled('office_id')) {
    //         return (int) $request->office_id;
    //     }

    //     if (method_exists($user, 'activeOfficeId')) {
    //         return (int) ($user->activeOfficeId() ?: 0);
    //     }

    //     return (int) ($user->office_id ?: 0);
    // }

    // /**
    //  * Role-based + office-based employee query
    //  */
    // private function employeeQuery(Request $request)
    // {
    //     $user = $request->user();
    //     $officeId = $this->resolveOfficeId($request, $user);

    //     $query = User::with(['roles', 'userSalary']);

    //     if ($officeId) {
    //         $query->where('office_id', $officeId);
    //     }

    //     // Role base filter jaise abhi ho raha hai
    //     if ($user->hasRole('super_admin')) {
    //         // super admin selected office ka sab dekh sakta hai
    //     } elseif ($user->hasRole('owner') || $user->hasRole('admin')) {
    //         // office ke andar ke employees
    //         if ($officeId) {
    //             $query->where('office_id', $officeId);
    //         }
    //     } elseif ($user->hasRole('team_leader')) {
    //         // apna + apni team ke records
    //         $allowedIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
    //         $allowedIds[] = $user->id;

    //         $query->whereIn('id', $allowedIds);

    //         if ($officeId) {
    //             $query->where('office_id', $officeId);
    //         }
    //     } else {
    //         // normal employee sirf apna hi record dekhe
    //         $query->where('id', $user->id);

    //         if ($officeId) {
    //             $query->where('office_id', $officeId);
    //         }
    //     }

    //     return $query;
    // }

    // public function index(Request $request)
    // {
    //     $user = $request->user();
    //     $officeId = $this->resolveOfficeId($request, $user);

    //     if (!$officeId && !$user->hasRole('super_admin')) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Office not found.',
    //         ], 422);
    //     }

    //     $employees = $this->employeeQuery($request)->latest()->get();

    //     foreach ($employees as $employee) {
    //         $employee->role = $employee->roles->first();
    //     }

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Data retrieved successfully',
    //         'data' => [
    //             'user' => $user,
    //             'office_id' => $officeId,
    //             'employees' => $employees,
    //         ]
    //     ], 200);
    // }


    private function resolveOfficeId(Request $request, $user): int
    {
        if ($request->filled('office_id')) {
            return (int) $request->office_id;
        }

        if (method_exists($user, 'activeOfficeId')) {
            return (int) ($user->activeOfficeId() ?: 0);
        }

        return (int) ($user->office_id ?: 0);
    }

    private function employeeQuery(Request $request)
    {
        $user = $request->user();
        $officeId = $this->resolveOfficeId($request, $user);

        $query = User::with(['roles', 'userSalary']);

        /**
         * Super Admin
         * - office_id selected hai to us office ke users
         * - office_id nahi hai to all users
         */
        if ($user->hasRole('super_admin')) {
            if ($officeId) {
                $query->where('office_id', $officeId);
            }

            return $query;
        }

        /**
         * Admin
         * - sirf apne office ke users
         */
        if ($user->hasRole('admin')) {
            $query->where('office_id', $officeId);

            return $query;
        }

        /**
         * Owner
         * - owner ke saare offices ke users
         * - agar office_id selected hai to sirf us office ka data
         */
        if ($user->hasRole('owner')) {
            $ownerOfficeIds = Office::where('owner_id', $user->id)->pluck('id')->toArray();

            if ($officeId && in_array($officeId, $ownerOfficeIds)) {
                $query->where('office_id', $officeId);
            } else {
                $query->whereIn('office_id', $ownerOfficeIds);
            }

            return $query;
        }

        /**
         * Team Leader
         * - apna record
         * - apni team ke members
         * - office wise filter bhi lagega
         */
        if ($user->hasRole('team_leader')) {
            $memberIds = User::where('parent_id', $user->id)->pluck('id')->toArray();
            $memberIds[] = $user->id;

            $query->whereIn('id', $memberIds);

            if ($officeId) {
                $query->where('office_id', $officeId);
            }

            return $query;
        }

        /**
         * Normal Employee
         * - sirf apna record
         */
        $query->where('id', $user->id);

        if ($officeId) {
            $query->where('office_id', $officeId);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $officeId = $this->resolveOfficeId($request, $user);

        if (!$officeId && !$user->hasRole(['super_admin', 'owner'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Office not found.',
            ], 422);
        }

        $employees = $this->employeeQuery($request)
            ->latest()
            ->get();

        $employees->transform(function ($employee) {
            $employee->role = $employee->roles->first();
            return $employee;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => [
                'user' => $user,
                'office_id' => $officeId,
                'employees' => $employees,
            ]
        ], 200);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'address' => 'nullable',
            'photo' => 'nullable|image',
            'joining_date' => 'nullable',
            'designation' => 'nullable',
            'responsibility' => 'nullable',
            'salary' => 'nullable',
            'check_in_time' => 'required',
            'check_out_time' => 'required',
            'basic_salary' => 'nullable',
            'dearness_allowance' => 'nullable',
            'relieving_charge' => 'nullable',
            'additional_allowance' => 'nullable',
            'provident_fund' => 'nullable',
            'employee_state_insurance_corporation' => 'nullable',
        ]);

        try {
            $user = $request->user();
            $officeId = $this->resolveOfficeId($request, $user);

            if (!$officeId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Office ID is required.',
                ], 422);
            }

            // non-super-admin dusri office me create na kar sake
            if (
                !$user->hasRole('super_admin') &&
                $officeId != (method_exists($user, 'activeOfficeId') ? $user->activeOfficeId() : $user->office_id)
            ) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not allowed to create employee in this office.',
                ], 403);
            }

            if ($request->filled('email')) {
                $existingEmployee = User::where('email', $request->email)->first();
                if ($existingEmployee) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Email already exists.',
                    ], 422);
                }
            }

            $checkInTime = Carbon::parse($request->check_in_time);
            $checkOutTime = Carbon::parse($request->check_out_time);

            $employee = User::create($request->except('joining_date', 'office_id') + [
                'office_id' => $officeId,
                'password' => Hash::make('password'),
            ]);

            if ($request->file('photo')) {
                $file = $request->file('photo')->store('public/photos');
                $employee->photo = str_replace('public/', '', $file);
            }

            if ($request->file('aadhar_attachment')) {
                $file = $request->file('aadhar_attachment')->store('public/photos');
                $employee->aadhar_attachment = str_replace('public/', '', $file);
            }

            if ($request->file('pan_attachment')) {
                $file = $request->file('pan_attachment')->store('public/photos');
                $employee->pan_attachment = str_replace('public/', '', $file);
            }

            if ($request->file('other_attachment')) {
                $file = $request->file('other_attachment')->store('public/photos');
                $employee->other_attachment = str_replace('public/', '', $file);
            }

            $employee->office_time = $checkInTime->diffInMinutes($checkOutTime);
            $employee->joining_date = $request->joining_date;
            $employee->save();

            if ($request->role) {
                $employee->assignRole($request->role);
            } else {
                $employee->assignRole('employee');
            }

            $basic_salary = $request->basic_salary ?? 0;
            $house_rent_allowance = $request->house_rent_allowance ?? 0;
            $transport_allowance = $request->transport_allowance ?? 0;
            $medical_allowance = $request->medical_allowance ?? 0;
            $special_allowance = $request->special_allowance ?? 0;
            $dearness_allowance = $request->dearness_allowance ?? 0;
            $relieving_charge = $request->relieving_charge ?? 0;
            $additional_allowance = $request->additional_allowance ?? 0;

            $total_salary = $basic_salary + $house_rent_allowance + $transport_allowance + $medical_allowance + $special_allowance + $dearness_allowance + $relieving_charge + $additional_allowance;

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

            return response()->json([
                'status' => 'success',
                'message' => 'Employee registered successfully.',
                'employee' => $employee,
                'salary' => $userSalary,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
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
            $user = $request->user();
            $officeId = $this->resolveOfficeId($request, $user);

            $employee = User::find($id);

            if (!$employee) {
                return response()->json(['error' => 'Employee not found.'], 404);
            }

            // super admin ko chhod kar sab office-based restrict honge
            if (
                !$user->hasRole('super_admin') &&
                (
                    !$officeId ||
                    (int) $employee->office_id !== (int) $officeId
                )
            ) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not allowed to update this employee.',
                ], 403);
            }

            $employee->update($request->except([
                'photo',
                'joining_date',
                'check_in_time',
                'check_out_time',
                'office_id'
            ]));

            // optionally office update only for super admin
            if ($user->hasRole('super_admin') && $request->filled('office_id')) {
                $employee->office_id = $request->office_id;
            }

            if ($request->file('photo')) {
                $file = $request->file('photo')->store('public/photos');
                $employee->photo = str_replace('public/', '', $file);
            }

            if ($request->file('aadhar_attachment')) {
                $file = $request->file('aadhar_attachment')->store('public/photos');
                $employee->aadhar_attachment = str_replace('public/', '', $file);
            }

            if ($request->file('pan_attachment')) {
                $file = $request->file('pan_attachment')->store('public/photos');
                $employee->pan_attachment = str_replace('public/', '', $file);
            }

            if ($request->file('other_attachment')) {
                $file = $request->file('other_attachment')->store('public/photos');
                $employee->other_attachment = str_replace('public/', '', $file);
            }

            if ($request->check_in_time && $request->check_out_time) {
                $checkInTime = Carbon::parse($request->check_in_time);
                $checkOutTime = Carbon::parse($request->check_out_time);
                $employee->office_time = $checkInTime->diffInMinutes($checkOutTime);
            }

            if ($request->joining_date) {
                $employee->joining_date = $request->joining_date;
            }

            $employee->save();

            $basic_salary = $request->basic_salary ?? $employee->userSalary?->basic_salary ?? 0;
            $house_rent_allowance = $request->house_rent_allowance ?? $employee->userSalary?->house_rent_allowance ?? 0;
            $transport_allowance = $request->transport_allowance ?? $employee->userSalary?->transport_allowance ?? 0;
            $medical_allowance = $request->medical_allowance ?? $employee->userSalary?->medical_allowance ?? 0;
            $special_allowance = $request->special_allowance ?? $employee->userSalary?->special_allowance ?? 0;
            $dearness_allowance = $request->dearness_allowance ?? $employee->userSalary?->dearness_allowance ?? 0;
            $relieving_charge = $request->relieving_charge ?? $employee->userSalary?->relieving_charge ?? 0;
            $additional_allowance = $request->additional_allowance ?? $employee->userSalary?->additional_allowance ?? 0;

            $total_salary = $basic_salary + $house_rent_allowance + $transport_allowance + $medical_allowance + $special_allowance + $dearness_allowance + $relieving_charge + $additional_allowance;

            $userSalary = $employee->userSalary;

            if ($userSalary) {
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
            } else {
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
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Employee updated successfully.',
                'employee' => $employee,
                'salary' => $userSalary,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try {
            $user = $request->user();
            $officeId = $this->resolveOfficeId($request, $user);

            $employee = User::where('id', $request->employee_id)->first();

            if (!$employee) {
                return response()->json(['error' => 'Employee not found.'], 404);
            }

            if (
                !$user->hasRole('super_admin') &&
                (
                    !$officeId ||
                    (int) $employee->office_id !== (int) $officeId
                )
            ) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not allowed to delete this employee.',
                ], 403);
            }

            $employee->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Employee deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function teamLeaders(Request $request)
    {
        $user = $request->user();
        $officeId = $this->resolveOfficeId($request, $user);

        if (!$officeId) {
            return response()->json([
                'team_leaders' => []
            ], 200);
        }

        if ($user->hasRole('admin') || $user->hasRole('owner') || $user->hasRole('super_admin')) {
            $teamleaders = User::with('roles')
                ->where('office_id', $officeId)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'team_leader');
                })
                ->get();
        } else {
            $teamleaders = collect();
        }

        return response()->json([
            'office_id' => $officeId,
            'team_leaders' => $teamleaders
        ], 200);
    }



}
