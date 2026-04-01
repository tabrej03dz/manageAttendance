<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Office;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserSalary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeController extends Controller
{


private function activeOfficeId(Request $request): ?int
{
    return $request->user()?->activeOfficeId();
}

private function allowedOfficeIds(Request $request): array
{
    $user = $request->user();

    if (!$user) {
        return [];
    }

    if ($user->hasRole('super_admin')) {
        $officeId = $user->activeOfficeId();
        return $officeId ? [$officeId] : [];
    }

    if ($user->hasRole('owner')) {
        $officeId = $user->activeOfficeId();
        return $officeId ? [$officeId] : [];
    }

    if ($user->hasRole('admin')) {
        return $user->office_id ? [$user->office_id] : [];
    }

    if ($user->office_id) {
        return [$user->office_id];
    }

    return [];
}

private function officeEmployeesQuery(Request $request)
{
    $officeIds = $this->allowedOfficeIds($request);

    return User::query()->when(!empty($officeIds), function ($q) use ($officeIds) {
        $q->whereIn('office_id', $officeIds);
    }, function ($q) {
        $q->whereRaw('1 = 0');
    });
}

private function sortEmployeesHierarchically($employees)
{
    $employees = collect($employees);

    $grouped = $employees->groupBy('team_leader_id');
    $sorted = collect();

    $appendChildren = function ($leaderId) use (&$appendChildren, $grouped, &$sorted) {
        if (!isset($grouped[$leaderId])) {
            return;
        }

        foreach ($grouped[$leaderId]->sortBy('name') as $employee) {
            $sorted->push($employee);
            $appendChildren($employee->id);
        }
    };

    if (isset($grouped[null])) {
        foreach ($grouped[null]->sortBy('name') as $employee) {
            $sorted->push($employee);
            $appendChildren($employee->id);
        }
    }

    $remaining = $employees->whereNotIn('id', $sorted->pluck('id'));

    foreach ($remaining->sortBy('name') as $employee) {
        $sorted->push($employee);
        $appendChildren($employee->id);
    }

    return $sorted->unique('id')->values();
}


    // public function index(Request $request)
    // {
    //     // $employees = HomeController::employeeList(); // must return query, not collection

    //     $employees = HomeController::employeeList()->toQuery();
    //     // 🔍 SEARCH
    //     if ($request->filled('q')) {
    //         $q = trim($request->q);
    //         $employees->where(function ($qq) use ($q) {
    //             $qq->where('name', 'like', "%{$q}%")
    //             ->orWhere('email', 'like', "%{$q}%")
    //             ->orWhere('phone', 'like', "%{$q}%");
    //         });
    //     }

    //     // status filter
    //     if ($request->filled('status')) {
    //         $employees->where('status', $request->status);
    //     } else {
    //         $employees->where('status', '1');
    //     }

    //     // department filter
    //     if ($request->filled('department_id')) {
    //         $employees->where('department_id', $request->department_id);
    //     }

    //     // office not assigned
    //     if ($request->filled('office_unassigned') && $request->office_unassigned == '1') {
    //         $employees->whereNull('office_id');
    //     }

    //     $departments = Department::all();

    //     $unassignedCount = HomeController::employeeList()
    //         ->whereNull('office_id')
    //         ->count();

    //     // ✅ PAGINATION (10 per page)
    //     $employees = $employees

    //         ->paginate(25)
    //         ->withQueryString(); // keep filters in pagination links

    //     return view('dashboard.employee.index', compact(
    //         'employees', 'departments', 'unassignedCount'
    //     ));
    // }


    // public function index(Request $request)
    // {
    //     $employees = HomeController::employeeList()->toQuery();

    //     // 🔍 SEARCH
    //     if ($request->filled('q')) {
    //         $q = trim($request->q);
    //         $employees->where(function ($qq) use ($q) {
    //             $qq->where('name', 'like', "%{$q}%")
    //             ->orWhere('email', 'like', "%{$q}%")
    //             ->orWhere('phone', 'like', "%{$q}%");
    //         });
    //     }

    //     // status filter
    //     if ($request->filled('status')) {
    //         $employees->where('status', $request->status);
    //     } else {
    //         $employees->where('status', '1');
    //     }

    //     // department filter
    //     if ($request->filled('department_id')) {
    //         $employees->where('department_id', $request->department_id);
    //     }

    //     // ✅ office filter (NEW)
    //     if ($request->filled('office_id')) {
    //         $employees->where('office_id', $request->office_id);
    //     }

    //     // office not assigned
    //     if ($request->filled('office_unassigned') && $request->office_unassigned == '1') {
    //         $employees->whereNull('office_id');
    //     }

    //     $departments = Department::all();
    //     $offices     = Office::orderBy('name')->get(); // ✅ NEW

    //     $unassignedCount = HomeController::employeeList()
    //         ->whereNull('office_id')
    //         ->count();

    //     $employees = $employees->paginate(25)->withQueryString();

    //     return view('dashboard.employee.index', compact(
    //         'employees', 'departments', 'offices', 'unassignedCount'
    //     ));
    // }


public function index(Request $request)
{
    $query = $this->officeEmployeesQuery($request);

    if ($request->filled('q')) {
        $q = trim($request->q);
        $query->where(function ($qq) use ($q) {
            $qq->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    } else {
        $query->where('status', '1');
    }

    if ($request->filled('department_id')) {
        $query->where('department_id', $request->department_id);
    }

    $allowedOfficeIds = $this->allowedOfficeIds($request);

    if ($request->filled('office_id')) {
        $requestedOfficeId = (int) $request->office_id;

        if (in_array($requestedOfficeId, $allowedOfficeIds)) {
            $query->where('office_id', $requestedOfficeId);
        } else {
            $query->whereRaw('1 = 0');
        }
    }

    if ($request->filled('office_unassigned') && $request->office_unassigned == '1') {
        $query->whereNull('office_id');
    }

    $employees = $query->get();

    // hierarchy order preserve
    $employees = $this->sortEmployeesHierarchically($employees);

    $departments = Department::all();

    $offices = Office::when(!empty($allowedOfficeIds), function ($q) use ($allowedOfficeIds) {
            $q->whereIn('id', $allowedOfficeIds);
        })
        ->orderBy('name')
        ->get();

    $unassignedCount = (clone $this->officeEmployeesQuery($request))
        ->whereNull('office_id')
        ->count();

    $perPage = 25;
    $currentPage = Paginator::resolveCurrentPage();

    $paginatedEmployees = new LengthAwarePaginator(
        $employees->forPage($currentPage, $perPage)->values(),
        $employees->count(),
        $perPage,
        $currentPage,
        [
            'path' => Paginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]
    );

    return view('dashboard.employee.index', [
        'employees' => $paginatedEmployees,
        'departments' => $departments,
        'offices' => $offices,
        'unassignedCount' => $unassignedCount,
    ]);
}




//     public function create()
//     {
//         if (auth()->user()->hasRole('super_admin')) {
//             $offices = Office::all();
//             $teamLeaders = User::role('team_leader')->get();
//         } else {
//             if (auth()->user()->hasRole('owner')) {
//                 $user = auth()->user();
//             } else {
//                 $user = auth()->user()->office->owner;
//             }
//             $plan = Plan::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();
//             $employeeCount = 0;
//             foreach ($user->offices as $office) {
//                 $employeeCount += $office->users->count();
//             }
//             if ($employeeCount >= $plan->number_of_employees) {
//                 return back()->with('error', 'Your employee creation limit exceeded!');
//             }
//             $offices = Office::where('owner_id', auth()->user()->id)->get();
// //            $teamLeaders = User::where('office_id', auth()->user()->office_id)->role('team_leader')->get();
//             $teamLeaders = HomeController::employeeList();
//         }
//         $departments = Department::all();
//         return view('dashboard.employee.create', compact('offices', 'teamLeaders', 'departments'));
//     }



    // public function create(Request $request)
    // {
    //     $user = $request->user();
    //     $activeOfficeId = $user->activeOfficeId();

    //     if (!$activeOfficeId) {
    //         return back()->with('error', 'Please select an office first.');
    //     }

    //     if ($user->hasRole('super_admin')) {
    //         $offices = Office::where('id', $activeOfficeId)->get();
    //         $teamLeaders = User::where('office_id', $activeOfficeId)
    //             ->role('team_leader')
    //             ->get();
    //     } else {
    //         $owner = $user->hasRole('owner') ? $user : optional($user->office)->owner;

    //         if ($owner) {
    //             $plan = Plan::where('user_id', $owner->id)->latest()->first();

    //             $employeeCount = User::where('office_id', $activeOfficeId)->count();

    //             if ($plan && $employeeCount >= $plan->number_of_employees) {
    //                 return back()->with('error', 'Your employee creation limit exceeded!');
    //             }
    //         }

    //         $offices = Office::where('id', $activeOfficeId)->get();

    //         $teamLeaders = User::where('office_id', $activeOfficeId)
    //             ->role('team_leader')
    //             ->get();
    //     }

    //     $departments = Department::all();

    //     return view('dashboard.employee.create', compact('offices', 'teamLeaders', 'departments'));
    // }


    public function create(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('super_admin')) {
            $offices = Office::orderBy('name')->get();

            $teamLeaders = User::whereHas('roles', function ($q) {
                    $q->where('name', 'team_leader');
                })
                ->with('office')
                ->get();
        } else {
            $activeOfficeId = $user->activeOfficeId();

            if (!$activeOfficeId) {
                return back()->with('error', 'Please select an office first.');
            }

            $owner = $user->hasRole('owner') ? $user : optional($user->office)->owner;

            if ($owner) {
                $plan = Plan::where('user_id', $owner->id)->latest()->first();

                $employeeCount = User::where('office_id', $activeOfficeId)->count();

                if ($plan && $employeeCount >= $plan->number_of_employees) {
                    return back()->with('error', 'Your employee creation limit exceeded!');
                }
            }

            $offices = Office::where('id', $activeOfficeId)->get();

            $teamLeaders = User::where('office_id', $activeOfficeId)
                ->role('team_leader')
                ->get();
        }

        $departments = Department::all();

        return view('dashboard.employee.create', compact('offices', 'teamLeaders', 'departments'));
    }

    // public function store(EmployeeRequest $request)
    // {
    //     // Check if the email already exists
    //     $existingEmployee = User::where('email', $request->email)->first();
    //     if ($existingEmployee) {
    //         return back()->withErrors(['email' => 'Email already exists.'])->withInput();
    //     }

    //     // Parse check-in and check-out times
    //     $checkInTime = Carbon::parse($request->check_in_time);
    //     $checkOutTime = Carbon::parse($request->check_out_time);

    //     // Create the employee record
    //     $employee = User::create($request->except('joining_date') + [
    //         'office_id' => $request->office_id,
    //         'password' => Hash::make('password'),
    //     ]);

    //     // Handle photo upload
    //     if ($request->file('photo')) {
    //         $file = $request->file('photo')->store('public/photos');
    //         $employee->photo = str_replace('public/', '', $file);
    //     }
    //     if ($request->file('aadhar_attachment')) {
    //         $file = $request->file('aadhar_attachment')->store('public/aadhar_attachments');
    //         $employee->aadhar_attachment = str_replace('public/', '', $file);
    //     }
    //     if ($request->file('pan_attachment')) {
    //         $file = $request->file('pan_attachment')->store('public/pan_attachments');
    //         $employee->pan_attachment = str_replace('public/', '', $file);
    //     }
    //     if ($request->file('other_attachment')) {
    //         $file = $request->file('other_attachment')->store('public/other_attachments');
    //         $employee->other_attachment = str_replace('public/', '', $file);
    //     }
    //     // Calculate office time and save the joining date
    //     $employee->office_time = $checkInTime->diffInMinutes($checkOutTime);
    //     $employee->joining_date = $request->joining_date;
    //     $employee->save();

    //     // Assign role to employee
    //     if ($request->role) {
    //         $employee->assignRole($request->role);
    //     } else {
    //         $employee->assignRole('employee');
    //     }

    //     if ($employee) {
    //         $basic_salary = $request->basic_salary ?? 0;
    //         $house_rent_allowance = $request->house_rent_allowance ?? 0;
    //         $transport_allowance = $request->transport_allowance ?? 0;
    //         $medical_allowance = $request->medical_allowance ?? 0;
    //         $special_allowance = $request->special_allowance ?? 0;
    //         $dearness_allowance = $request->dearness_allowance ?? 0;
    //         $relieving_charge = $request->relieving_charge ?? 0;
    //         $additional_allowance = $request->additional_allowance ?? 0;

    //         $total_salary = $basic_salary + $house_rent_allowance + $transport_allowance + $medical_allowance + $special_allowance + $dearness_allowance + $relieving_charge + $additional_allowance;

    //         $userSalary = UserSalary::create([
    //             'user_id' => $employee->id,
    //             'basic_salary' => $basic_salary,
    //             'house_rent_allowance' => $house_rent_allowance,
    //             'transport_allowance' => $transport_allowance,
    //             'medical_allowance' => $medical_allowance,
    //             'special_allowance' => $special_allowance,
    //             'dearness_allowance' => $dearness_allowance,
    //             'relieving_charge' => $relieving_charge,
    //             'additional_allowance' => $additional_allowance,
    //             'total_salary' => $total_salary,
    //         ]);
    //     }


    //     // Redirect with success message
    //     return redirect('employee')->with('success', 'Employee Registered successfully');
    // }


    public function store(EmployeeRequest $request)
    {
        $user = $request->user();
        $activeOfficeId = $user->activeOfficeId();

        if (!$activeOfficeId) {
            return back()->with('error', 'Please select an office first.')->withInput();
        }

        $existingEmployee = User::where('email', $request->email)->first();
        if ($existingEmployee) {
            return back()->withErrors(['email' => 'Email already exists.'])->withInput();
        }

        $checkInTime = Carbon::parse($request->check_in_time);
        $checkOutTime = Carbon::parse($request->check_out_time);

        // force selected office
        $employee = User::create($request->except('joining_date', 'office_id') + [
            'office_id' => $activeOfficeId,
            'password' => Hash::make('password'),
        ]);

        if ($request->file('photo')) {
            $file = $request->file('photo')->store('public/photos');
            $employee->photo = str_replace('public/', '', $file);
        }

        if ($request->file('aadhar_attachment')) {
            $file = $request->file('aadhar_attachment')->store('public/aadhar_attachments');
            $employee->aadhar_attachment = str_replace('public/', '', $file);
        }

        if ($request->file('pan_attachment')) {
            $file = $request->file('pan_attachment')->store('public/pan_attachments');
            $employee->pan_attachment = str_replace('public/', '', $file);
        }

        if ($request->file('other_attachment')) {
            $file = $request->file('other_attachment')->store('public/other_attachments');
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

        UserSalary::create([
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

        return redirect('employee')->with('success', 'Employee Registered successfully');
    }

    // public function edit(User $employee){
    //     if (auth()->user()->hasRole('super_admin')){
    //         $offices = Office::all();
    //         $teamLeaders = User::role('team_leader')->get();
    //     }else{
    //         $offices = Office::where('id', auth()->user()->office_id)->get();
    //         $teamLeaders = User::where('office_id', auth()->user()->office_id)->role('team_leader','owner')->get();
    //     }
    //     return view('dashboard.employee.edit', compact('employee', 'offices', 'teamLeaders'));
    // }

    // public function edit(User $employee)
    // {
    //     if (auth()->user()->hasRole('super_admin')) {
    //         // Super admin sees all offices and team leaders
    //         $offices = Office::all();
    //         //            $teamLeaders = User::role(['team_leader', 'owner'])->get();
    //         $teamLeaders = HomeController::employeeList();
    //     } elseif (auth()->user()->hasRole('owner')) {
    //         // Owner sees only their associated office(s) and users in those offices
    //         $officeIds = auth()->user()->offices()->pluck('id'); // Get associated office IDs
    //         $offices = Office::whereIn('id', $officeIds)->get();
    //         //            $teamLeaders = User::whereIn('office_id', $officeIds)
    //         //                ->role(['team_leader', 'owner'])
    //         //                ->get();
    //         $teamLeaders = HomeController::employeeList();
    //     } else {
    //         // For other users, restrict by their single office ID
    //         $offices = Office::where('id', auth()->user()->office_id)->get();
    //         //            $teamLeaders = User::where('office_id', auth()->user()->office_id)
    //         //                ->role(['team_leader', 'owner'])
    //         //                ->get();
    //         $teamLeaders = HomeController::employeeList();
    //     }

    //     $departments = Department::all();

    //     return view('dashboard.employee.edit', compact('employee', 'offices', 'teamLeaders', 'departments'));
    // }

    // public function edit(Request $request, User $employee)
    // {
    //     $activeOfficeId = $request->user()->activeOfficeId();

    //     if (!$activeOfficeId) {
    //         return back()->with('error', 'Please select an office first.');
    //     }

    //     if ((int) $employee->office_id !== (int) $activeOfficeId) {
    //         abort(403, 'This employee does not belong to the selected office.');
    //     }

    //     $offices = Office::where('id', $activeOfficeId)->get();

    //     $teamLeaders = User::where('office_id', $activeOfficeId)
    //         ->role('team_leader')
    //         ->get();

    //     $departments = Department::all();

    //     return view('dashboard.employee.edit', compact('employee', 'offices', 'teamLeaders', 'departments'));
    // }


    public function edit(Request $request, User $employee)
    {
        $user = $request->user();

        if ($user->hasRole('super_admin')) {
            $offices = Office::orderBy('name')->get();

            $teamLeaders = User::whereHas('roles', function ($q) {
                    $q->where('name', 'team_leader');
                })
                ->with('office')
                ->get();
        } else {
            $activeOfficeId = $user->activeOfficeId();

            if (!$activeOfficeId) {
                return back()->with('error', 'Please select an office first.');
            }

            if ((int) $employee->office_id !== (int) $activeOfficeId) {
                abort(403, 'This employee does not belong to the selected office.');
            }

            $offices = Office::where('id', $activeOfficeId)->get();

            $teamLeaders = User::where('office_id', $activeOfficeId)
                ->role('team_leader')
                ->get();
        }

        $departments = Department::all();

        return view('dashboard.employee.edit', compact('employee', 'offices', 'teamLeaders', 'departments'));
    }


    // public function update(Request $request, User $employee){
    //     $employee->update($request->except(['password', 'photo', 'joining_date', 'office_id' => $request->office_id, 'team_leader_id' => $request->team_leader_id]));
    //     if ($request->filled('password')){
    //         $employee->password = Hash::make($request->password);
    //     }
    //     if ($request->file('photo')){
    //         if ($employee->photo){
    //             $file = public_path('storage/'. $employee->photo);
    //             if (file_exists($file)){
    //                 unlink($file);
    //             }
    //         }
    //         $file = $request->file('photo')->store('public/photos');
    //         $employee->photo = str_replace('public/', '', $file);
    //     }
    //     $employee->joining_date =  $request->joining_date;
    //     if ($request->role) {
    //         // Remove old roles and assign the new role
    //         $employee->syncRoles($request->role);
    //     }
    //     $employee->save();

    //     $basicSalary = $request->basic_salary ?? 0;
    //     $dearnessAllowance = $request->dearness_allowance ?? 0;
    //     $relievingCharge = $request->relieving_charge ?? 0;
    //     $additionalAllowance = $request->additional_allowance ?? 0;
    //     $providentFund = $request->provident_fund ?? 0;
    //     $esic = $request->employee_state_insurance_corporation ?? 0;

    //     $userSalary = UserSalary::where('user_id', $employee->id)->first();
    //     if ($userSalary){
    //         $userSalary = $userSalary->update([
    //             'user_id' => $employee->id,
    //             'basic_salary' => $basicSalary,
    //             'dearness_allowance' => $dearnessAllowance,
    //             'relieving_charge' => $relievingCharge,
    //             'additional_allowance' => $additionalAllowance,
    //             'provident_fund' => $providentFund,
    //             'employee_state_insurance_corporation' => $esic,
    //             'total_salary' => $basicSalary + $dearnessAllowance + $relievingCharge + $additionalAllowance,
    //         ]);
    //     }else{
    //         $userSalary = UserSalary::create([
    //             'user_id' => $employee->id,
    //             'basic_salary' => $basicSalary,
    //             'dearness_allowance' => $dearnessAllowance,
    //             'relieving_charge' => $relievingCharge,
    //             'additional_allowance' => $additionalAllowance,
    //             'provident_fund' => $providentFund,
    //             'employee_state_insurance_corporation' => $esic,
    //             'total_salary' => $basicSalary + $dearnessAllowance + $relievingCharge + $additionalAllowance,
    //         ]);
    //     }
    //     return redirect('employee')->with('success', 'Record Updated successfully');
    // }


    // public function update(Request $request, User $employee)
    // {
    //     // dd($request->all());
    //     // Update employee details except sensitive fields
    //     $employee->update($request->except([
    //         'password',
    //         'photo',
    //         'aadhar_attachment',
    //         'pan_attachment',
    //         'other_attachment',
    //         'joining_date',
    //     ]));

    //     // Update password if provided
    //     if ($request->filled('password')) {
    //         $employee->password = Hash::make($request->password);
    //     }

    //     // Update photo if a new one is uploaded
    //     if ($request->file('photo')) {
    //         if ($employee->photo) {
    //             $file = public_path('storage/' . $employee->photo);
    //             if (file_exists($file)) {
    //                 unlink($file);
    //             }
    //         }
    //         $file = $request->file('photo')->store('public/photos');
    //         $employee->photo = str_replace('public/', '', $file);
    //     }

    //     // Update aadhar_attachment if a new one is uploaded
    //     if ($request->file('aadhar_attachment')) {
    //         if ($employee->aadhar_attachment) {
    //             $file = public_path('storage/' . $employee->aadhar_attachment);
    //             if (file_exists($file)) {
    //                 unlink($file);
    //             }
    //         }
    //         $file = $request->file('aadhar_attachment')->store('public/aadhar_attachments');
    //         $employee->aadhar_attachment = str_replace('public/', '', $file);
    //     }

    //     // Update pan_attachment if a new one is uploaded
    //     if ($request->file('pan_attachment')) {
    //         if ($employee->pan_attachment) {
    //             $file = public_path('storage/' . $employee->pan_attachment);
    //             if (file_exists($file)) {
    //                 unlink($file);
    //             }
    //         }
    //         $file = $request->file('pan_attachment')->store('public/pan_attachments');
    //         $employee->pan_attachment = str_replace('public/', '', $file);
    //     }

    //     // Update other_attachment if a new one is uploaded
    //     if ($request->file('other_attachment')) {
    //         if ($employee->other_attachment) {
    //             $file = public_path('storage/' . $employee->other_attachment);
    //             if (file_exists($file)) {
    //                 unlink($file);
    //             }
    //         }
    //         $file = $request->file('other_attachment')->store('public/other_attachments');
    //         $employee->other_attachment = str_replace('public/', '', $file);
    //     }

    //     // Update joining date
    //     $employee->joining_date = $request->joining_date;

    //     // Update role if provided
    //     if ($request->role) {
    //         $employee->syncRoles($request->role);
    //     }
    //     $employee->office_id = $request->office_id;
    //     $employee->save();

    //     // Update salary details
    //     $basicSalary = $request->basic_salary ?? 0;
    //     $dearnessAllowance = $request->dearness_allowance ?? 0;
    //     $relievingCharge = $request->relieving_charge ?? 0;
    //     $additionalAllowance = $request->additional_allowance ?? 0;
    //     $providentFund = $request->provident_fund ?? 0;
    //     $esic = $request->employee_state_insurance_corporation ?? 0;

    //     $userSalary = UserSalary::where('user_id', $employee->id)->first();
    //     if ($userSalary) {
    //         $userSalary->update([
    //             'basic_salary' => $basicSalary,
    //             'dearness_allowance' => $dearnessAllowance,
    //             'relieving_charge' => $relievingCharge,
    //             'additional_allowance' => $additionalAllowance,
    //             'provident_fund' => $providentFund,
    //             'employee_state_insurance_corporation' => $esic,
    //             'total_salary' => $basicSalary + $dearnessAllowance + $relievingCharge + $additionalAllowance,
    //         ]);
    //     } else {
    //         UserSalary::create([
    //             'user_id' => $employee->id,
    //             'basic_salary' => $basicSalary,
    //             'dearness_allowance' => $dearnessAllowance,
    //             'relieving_charge' => $relievingCharge,
    //             'additional_allowance' => $additionalAllowance,
    //             'provident_fund' => $providentFund,
    //             'employee_state_insurance_corporation' => $esic,
    //             'total_salary' => $basicSalary + $dearnessAllowance + $relievingCharge + $additionalAllowance,
    //         ]);
    //     }

    //     // Redirect with success message
    //     return redirect('employee')->with('success', 'Record Updated successfully');
    // }


    // public function update(Request $request, User $employee)
    // {
    //     $activeOfficeId = $request->user()->activeOfficeId();

    //     if (!$activeOfficeId) {
    //         return back()->with('error', 'Please select an office first.');
    //     }

    //     if ((int) $employee->office_id !== (int) $activeOfficeId) {
    //         abort(403, 'This employee does not belong to the selected office.');
    //     }

    //     $employee->update($request->except([
    //         'password',
    //         'photo',
    //         'aadhar_attachment',
    //         'pan_attachment',
    //         'other_attachment',
    //         'joining_date',
    //         'office_id',
    //     ]));

    //     if ($request->filled('password')) {
    //         $employee->password = Hash::make($request->password);
    //     }

    //     if ($request->file('photo')) {
    //         if ($employee->photo) {
    //             $file = public_path('storage/' . $employee->photo);
    //             if (file_exists($file)) {
    //                 unlink($file);
    //             }
    //         }
    //         $file = $request->file('photo')->store('public/photos');
    //         $employee->photo = str_replace('public/', '', $file);
    //     }

    //     if ($request->file('aadhar_attachment')) {
    //         if ($employee->aadhar_attachment) {
    //             $file = public_path('storage/' . $employee->aadhar_attachment);
    //             if (file_exists($file)) {
    //                 unlink($file);
    //             }
    //         }
    //         $file = $request->file('aadhar_attachment')->store('public/aadhar_attachments');
    //         $employee->aadhar_attachment = str_replace('public/', '', $file);
    //     }

    //     if ($request->file('pan_attachment')) {
    //         if ($employee->pan_attachment) {
    //             $file = public_path('storage/' . $employee->pan_attachment);
    //             if (file_exists($file)) {
    //                 unlink($file);
    //             }
    //         }
    //         $file = $request->file('pan_attachment')->store('public/pan_attachments');
    //         $employee->pan_attachment = str_replace('public/', '', $file);
    //     }

    //     if ($request->file('other_attachment')) {
    //         if ($employee->other_attachment) {
    //             $file = public_path('storage/' . $employee->other_attachment);
    //             if (file_exists($file)) {
    //                 unlink($file);
    //             }
    //         }
    //         $file = $request->file('other_attachment')->store('public/other_attachments');
    //         $employee->other_attachment = str_replace('public/', '', $file);
    //     }

    //     $employee->joining_date = $request->joining_date;
    //     $employee->office_id = $activeOfficeId;

    //     if ($request->role) {
    //         $employee->syncRoles($request->role);
    //     }

    //     $employee->save();

    //     $basicSalary = $request->basic_salary ?? 0;
    //     $dearnessAllowance = $request->dearness_allowance ?? 0;
    //     $relievingCharge = $request->relieving_charge ?? 0;
    //     $additionalAllowance = $request->additional_allowance ?? 0;
    //     $providentFund = $request->provident_fund ?? 0;
    //     $esic = $request->employee_state_insurance_corporation ?? 0;

    //     $userSalary = UserSalary::where('user_id', $employee->id)->first();

    //     if ($userSalary) {
    //         $userSalary->update([
    //             'basic_salary' => $basicSalary,
    //             'dearness_allowance' => $dearnessAllowance,
    //             'relieving_charge' => $relievingCharge,
    //             'additional_allowance' => $additionalAllowance,
    //             'provident_fund' => $providentFund,
    //             'employee_state_insurance_corporation' => $esic,
    //             'total_salary' => $basicSalary + $dearnessAllowance + $relievingCharge + $additionalAllowance,
    //         ]);
    //     } else {
    //         UserSalary::create([
    //             'user_id' => $employee->id,
    //             'basic_salary' => $basicSalary,
    //             'dearness_allowance' => $dearnessAllowance,
    //             'relieving_charge' => $relievingCharge,
    //             'additional_allowance' => $additionalAllowance,
    //             'provident_fund' => $providentFund,
    //             'employee_state_insurance_corporation' => $esic,
    //             'total_salary' => $basicSalary + $dearnessAllowance + $relievingCharge + $additionalAllowance,
    //         ]);
    //     }

    //     return redirect('employee')->with('success', 'Record Updated successfully');
    // }


    public function update(Request $request, User $employee)
{
    $user = $request->user();

    if ($user->hasRole('super_admin')) {
        $targetOfficeId = $request->office_id;
    } else {
        $targetOfficeId = $user->activeOfficeId();

        if (!$targetOfficeId) {
            return back()->with('error', 'Please select an office first.');
        }

        if ((int) $employee->office_id !== (int) $targetOfficeId) {
            abort(403, 'This employee does not belong to the selected office.');
        }
    }

    $employee->update($request->except([
        'password',
        'photo',
        'aadhar_attachment',
        'pan_attachment',
        'other_attachment',
        'joining_date',
        'office_id',
    ]));

    if ($request->filled('password')) {
        $employee->password = Hash::make($request->password);
    }

    if ($request->file('photo')) {
        if ($employee->photo) {
            $file = public_path('storage/' . $employee->photo);
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $file = $request->file('photo')->store('public/photos');
        $employee->photo = str_replace('public/', '', $file);
    }

    if ($request->file('aadhar_attachment')) {
        if ($employee->aadhar_attachment) {
            $file = public_path('storage/' . $employee->aadhar_attachment);
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $file = $request->file('aadhar_attachment')->store('public/aadhar_attachments');
        $employee->aadhar_attachment = str_replace('public/', '', $file);
    }

    if ($request->file('pan_attachment')) {
        if ($employee->pan_attachment) {
            $file = public_path('storage/' . $employee->pan_attachment);
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $file = $request->file('pan_attachment')->store('public/pan_attachments');
        $employee->pan_attachment = str_replace('public/', '', $file);
    }

    if ($request->file('other_attachment')) {
        if ($employee->other_attachment) {
            $file = public_path('storage/' . $employee->other_attachment);
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $file = $request->file('other_attachment')->store('public/other_attachments');
        $employee->other_attachment = str_replace('public/', '', $file);
    }

    $employee->joining_date = $request->joining_date;
    $employee->office_id = $targetOfficeId;

    if ($request->role) {
        $employee->syncRoles($request->role);
    }

    $employee->save();

    $basicSalary = $request->basic_salary ?? 0;
    $dearnessAllowance = $request->dearness_allowance ?? 0;
    $relievingCharge = $request->relieving_charge ?? 0;
    $additionalAllowance = $request->additional_allowance ?? 0;
    $providentFund = $request->provident_fund ?? 0;
    $esic = $request->employee_state_insurance_corporation ?? 0;

    $userSalary = UserSalary::where('user_id', $employee->id)->first();

    if ($userSalary) {
        $userSalary->update([
            'basic_salary' => $basicSalary,
            'dearness_allowance' => $dearnessAllowance,
            'relieving_charge' => $relievingCharge,
            'additional_allowance' => $additionalAllowance,
            'provident_fund' => $providentFund,
            'employee_state_insurance_corporation' => $esic,
            'total_salary' => $basicSalary + $dearnessAllowance + $relievingCharge + $additionalAllowance,
        ]);
    } else {
        UserSalary::create([
            'user_id' => $employee->id,
            'basic_salary' => $basicSalary,
            'dearness_allowance' => $dearnessAllowance,
            'relieving_charge' => $relievingCharge,
            'additional_allowance' => $additionalAllowance,
            'provident_fund' => $providentFund,
            'employee_state_insurance_corporation' => $esic,
            'total_salary' => $basicSalary + $dearnessAllowance + $relievingCharge + $additionalAllowance,
        ]);
    }

    return redirect('employee')->with('success', 'Record Updated successfully');
}


    // public function delete(User $employee)
    // {
    //     if ($employee->photo) {
    //         $file = public_path('storage/' . $employee->photo);
    //         if (file_exists($file)) {
    //             unlink($file);
    //         }
    //     }
    //     $employee->delete();
    //     return back()->with('success', 'Record Deleted Successfully');
    // }

    public function delete(Request $request, User $employee)
    {
        $activeOfficeId = $request->user()->activeOfficeId();

        if (!$activeOfficeId || (int) $employee->office_id !== (int) $activeOfficeId) {
            abort(403, 'This employee does not belong to the selected office.');
        }

        if ($employee->photo) {
            $file = public_path('storage/' . $employee->photo);
            if (file_exists($file)) {
                unlink($file);
            }
        }

        $employee->delete();

        return back()->with('success', 'Record Deleted Successfully');
    }

    // public function employeeAttendance()
    // {
    //     $user = auth()->user();
    //     if ($user->hasRole('super_admin')) {
    //         $employees = User::all();
    //     } elseif($user->hasRole('owner')) {
    //         $officeIds = $user->offices->pluck('id');
    //         $employees = User::whereIn('office_id', $officeIds)->get();
    //     }else{
    //         $employees = User::where('office_id', $user->office_id)->get();
    //     }
    //     return view('dashboard.employee.list', compact('employees'));
    // }

    public function employeeAttendance(Request $request)
    {
        $activeOfficeId = $request->user()->activeOfficeId();

        if (!$activeOfficeId) {
            $employees = collect();
        } else {
            $employees = User::where('office_id', $activeOfficeId)
                ->orderBy('name')
                ->get();
        }

        return view('dashboard.employee.list', compact('employees'));
    }

    // public function status(User $employee)
    // {
    //     if ($employee->status == '1') {
    //         $employee->status = '0';
    //     } else {
    //         $employee->status = '1';
    //     }
    //     $response = $employee->save();
    //     if ($response) {
    //         request()->session()->flash('success', 'Status changed successfully');
    //     } else {
    //         \request()->session()->flash('error', 'Error, Try again!');
    //     }
    //     return back();
    // }

    public function status(Request $request, User $employee)
    {
        $activeOfficeId = $request->user()->activeOfficeId();

        if (!$activeOfficeId || (int) $employee->office_id !== (int) $activeOfficeId) {
            abort(403, 'This employee does not belong to the selected office.');
        }

        $employee->status = $employee->status == '1' ? '0' : '1';

        $response = $employee->save();

        if ($response) {
            $request->session()->flash('success', 'Status changed successfully');
        } else {
            $request->session()->flash('error', 'Error, Try again!');
        }

        return back();
    }

    // public function permission(User $user)
    // {
    //     $permissions = $user->permissions;
    //     return view('dashboard.employee.permission', compact('permissions', 'user'));
    // }


    public function permission(Request $request, User $user)
{
    $activeOfficeId = $request->user()->activeOfficeId();

    if (!$activeOfficeId || (int) $user->office_id !== (int) $activeOfficeId) {
        abort(403, 'This employee does not belong to the selected office.');
    }

    $permissions = $user->permissions;

    return view('dashboard.employee.permission', compact('permissions', 'user'));
}

public function permissionRemove(Request $request, Permission $permission, User $user)
{
    $activeOfficeId = $request->user()->activeOfficeId();

    if (!$activeOfficeId || (int) $user->office_id !== (int) $activeOfficeId) {
        abort(403, 'This employee does not belong to the selected office.');
    }

    if ($user->hasPermissionTo($permission)) {
        $user->revokePermissionTo($permission);
        return back()->with('success', 'Permission removed from the user successfully.');
    }

    return back()->with('error', 'User does not have this permission.');
}

    // public function permissionRemove(Permission $permission, User $user)
    // {
    //     if ($user->hasPermissionTo($permission)) { // Check if the user has the permission
    //         $user->revokePermissionTo($permission); // Remove the permission from the user
    //         return back()->with('success', 'Permission removed from the user successfully.');
    //     }
    //     return back()->with('error', 'User does not have this permission.');
    // }
}
