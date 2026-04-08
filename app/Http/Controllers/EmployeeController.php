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
        } elseif ($user->hasRole('owner')) {
            // owner ke saare offices lao
            $offices = Office::where('owner_id', $user->id)
                ->orderBy('name')
                ->get();

            if ($offices->isEmpty()) {
                return back()->with('error', 'No office found for this owner.');
            }

            // owner ke total employees count sab offices ka
            $officeIds = $offices->pluck('id');

            $plan = Plan::where('user_id', $user->id)->latest()->first();
            $employeeCount = User::whereIn('office_id', $officeIds)->count();

            if ($plan && $employeeCount >= $plan->number_of_employees) {
                return back()->with('error', 'Your employee creation limit exceeded!');
            }

            $teamLeaders = User::whereIn('office_id', $officeIds)
                ->role('team_leader')
                ->with('office')
                ->get();
        } else {
            $activeOfficeId = $user->activeOfficeId();

            if (!$activeOfficeId) {
                return back()->with('error', 'Please select an office first.');
            }

            $owner = optional($user->office)->owner;

            if ($owner) {
                $plan = Plan::where('user_id', $owner->id)->latest()->first();

                $employeeCount = User::where('office_id', $activeOfficeId)->count();

                if ($plan && $employeeCount >= $plan->number_of_employees) {
                    return back()->with('error', 'Your employee creation limit exceeded!');
                }
            }

            $offices = Office::where('id', $activeOfficeId)->orderBy('name')->get();

            $teamLeaders = User::where('office_id', $activeOfficeId)
                ->role('team_leader')
                ->with('office')
                ->get();
        }

        $departments = Department::all();

        return view('dashboard.employee.create', compact('offices', 'teamLeaders', 'departments'));
    }




    public function store(EmployeeRequest $request)
    {
        $user = $request->user();

        // =========================
        // OFFICE RESOLUTION
        // =========================
        if ($user->hasRole('super_admin')) {

            $targetOfficeId = $request->office_id;

            if (!$targetOfficeId) {
                return back()->with('error', 'Please select an office first.')->withInput();
            }

        } elseif ($user->hasRole('owner')) {

            // owner ke saare offices
            $ownerOfficeIds = Office::where('owner_id', $user->id)->pluck('id');

            if ($ownerOfficeIds->isEmpty()) {
                return back()->with('error', 'No office found for this owner.')->withInput();
            }

            // agar dropdown se office select ho raha hai to use lo, warna active
            $targetOfficeId = $request->office_id ?: $user->activeOfficeId();

            if (!$targetOfficeId || !$ownerOfficeIds->contains($targetOfficeId)) {
                return back()->with('error', 'Invalid office selected.')->withInput();
            }

            // ✅ PLAN CHECK (OWNER TOTAL EMPLOYEES)
            $plan = Plan::where('user_id', $user->id)->latest()->first();

            $employeeCount = User::whereIn('office_id', $ownerOfficeIds)->count();

            if ($plan && $employeeCount >= $plan->number_of_employees) {
                return back()->with('error', 'Your employee creation limit exceeded!')->withInput();
            }

        } else {

            $targetOfficeId = $user->activeOfficeId();

            if (!$targetOfficeId) {
                return back()->with('error', 'Please select an office first.')->withInput();
            }

            // owner निकालो
            $owner = optional($user->office)->owner;

            if ($owner) {
                $plan = Plan::where('user_id', $owner->id)->latest()->first();

                $employeeCount = User::where('office_id', $targetOfficeId)->count();

                if ($plan && $employeeCount >= $plan->number_of_employees) {
                    return back()->with('error', 'Your employee creation limit exceeded!')->withInput();
                }
            }
        }

        // =========================
        // EMAIL CHECK
        // =========================
        $existingEmployee = User::where('email', $request->email)->first();
        if ($existingEmployee) {
            return back()->withErrors(['email' => 'Email already exists.'])->withInput();
        }

        // =========================
        // TIME CALCULATION
        // =========================
        $checkInTime = Carbon::parse($request->check_in_time);
        $checkOutTime = Carbon::parse($request->check_out_time);

        // =========================
        // CREATE EMPLOYEE
        // =========================
        $employee = User::create($request->except([
            'joining_date',
            'office_id',
            'photo',
            'aadhar_attachment',
            'pan_attachment',
            'other_attachment',
            'role',
            'basic_salary',
            'house_rent_allowance',
            'transport_allowance',
            'medical_allowance',
            'special_allowance',
            'dearness_allowance',
            'relieving_charge',
            'additional_allowance',
        ]) + [
            'office_id' => $targetOfficeId,
            'password' => Hash::make('password'),
        ]);

        // =========================
        // FILE UPLOADS
        // =========================
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

        // =========================
        // SAVE EXTRA DATA
        // =========================
        $employee->office_time = $checkInTime->diffInMinutes($checkOutTime);
        $employee->joining_date = $request->joining_date;
        $employee->save();

        // =========================
        // ROLE ASSIGN
        // =========================
        $employee->assignRole($request->role ?? 'employee');

        // =========================
        // SALARY
        // =========================
        $basic_salary = $request->basic_salary ?? 0;
        $house_rent_allowance = $request->house_rent_allowance ?? 0;
        $transport_allowance = $request->transport_allowance ?? 0;
        $medical_allowance = $request->medical_allowance ?? 0;
        $special_allowance = $request->special_allowance ?? 0;
        $dearness_allowance = $request->dearness_allowance ?? 0;
        $relieving_charge = $request->relieving_charge ?? 0;
        $additional_allowance = $request->additional_allowance ?? 0;

        $total_salary =
            $basic_salary +
            $house_rent_allowance +
            $transport_allowance +
            $medical_allowance +
            $special_allowance +
            $dearness_allowance +
            $relieving_charge +
            $additional_allowance;

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

            // super admin kisi bhi office ke employee ko edit kar sakta hai
        } elseif ($user->hasRole('owner')) {
            $ownerOfficeIds = Office::where('owner_id', $user->id)->pluck('id');

            if ($ownerOfficeIds->isEmpty()) {
                return back()->with('error', 'No office found for this owner.');
            }

            $activeOfficeId = $user->activeOfficeId();

            if (!$activeOfficeId) {
                $activeOfficeId = $ownerOfficeIds->first();
            }

            // security: owner sirf apne office ke employee ko edit kare
            if (!$ownerOfficeIds->contains($employee->office_id)) {
                abort(403, 'This employee does not belong to your office.');
            }

            $offices = Office::whereIn('id', $ownerOfficeIds)
                ->orderBy('name')
                ->get();

            $teamLeaders = User::where('office_id', $activeOfficeId)
                ->role('team_leader')
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

            $offices = Office::where('id', $activeOfficeId)
                ->orderBy('name')
                ->get();

            $teamLeaders = User::where('office_id', $activeOfficeId)
                ->role('team_leader')
                ->with('office')
                ->get();
        }

        $departments = Department::all();

        return view('dashboard.employee.edit', compact('employee', 'offices', 'teamLeaders', 'departments'));
    }

    public function update(Request $request, User $employee)
    {
        $user = $request->user();

        // =========================
        // TARGET OFFICE RESOLUTION
        // =========================
        if ($user->hasRole('super_admin')) {

            $targetOfficeId = $request->office_id ?: $employee->office_id;

            if (!$targetOfficeId) {
                return back()->with('error', 'Please select an office first.')->withInput();
            }

        } elseif ($user->hasRole('owner')) {

            $ownerOfficeIds = Office::where('owner_id', $user->id)->pluck('id');

            if ($ownerOfficeIds->isEmpty()) {
                return back()->with('error', 'No office found for this owner.')->withInput();
            }

            // security: employee owner ke kisi office ka hi hona chahiye
            if (!$ownerOfficeIds->contains($employee->office_id)) {
                abort(403, 'This employee does not belong to your office.');
            }

            // owner dropdown se office change kar sakta hai
            $targetOfficeId = $request->office_id ?: $employee->office_id;

            if (!$ownerOfficeIds->contains($targetOfficeId)) {
                return back()->with('error', 'Invalid office selected.')->withInput();
            }

        } else {

            $targetOfficeId = $user->activeOfficeId();

            if (!$targetOfficeId) {
                return back()->with('error', 'Please select an office first.')->withInput();
            }

            if ((int) $employee->office_id !== (int) $targetOfficeId) {
                abort(403, 'This employee does not belong to the selected office.');
            }
        }

        // =========================
        // OPTIONAL EMAIL UNIQUE CHECK
        // =========================
        if ($request->filled('email')) {
            $emailExists = User::where('email', $request->email)
                ->where('id', '!=', $employee->id)
                ->exists();

            if ($emailExists) {
                return back()->withErrors(['email' => 'Email already exists.'])->withInput();
            }
        }

        // =========================
        // UPDATE BASIC FIELDS
        // =========================
        $employee->fill($request->except([
            'password',
            'photo',
            'aadhar_attachment',
            'pan_attachment',
            'other_attachment',
            'joining_date',
            'office_id',
            'role',
            'basic_salary',
            'house_rent_allowance',
            'transport_allowance',
            'medical_allowance',
            'special_allowance',
            'dearness_allowance',
            'relieving_charge',
            'additional_allowance',
            'provident_fund',
            'employee_state_insurance_corporation',
        ]));

        if ($request->filled('password')) {
            $employee->password = Hash::make($request->password);
        }

        // =========================
        // FILE UPLOADS
        // =========================
        if ($request->file('photo')) {
            if ($employee->photo) {
                $oldFile = public_path('storage/' . $employee->photo);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $file = $request->file('photo')->store('public/photos');
            $employee->photo = str_replace('public/', '', $file);
        }

        if ($request->file('aadhar_attachment')) {
            if ($employee->aadhar_attachment) {
                $oldFile = public_path('storage/' . $employee->aadhar_attachment);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $file = $request->file('aadhar_attachment')->store('public/aadhar_attachments');
            $employee->aadhar_attachment = str_replace('public/', '', $file);
        }

        if ($request->file('pan_attachment')) {
            if ($employee->pan_attachment) {
                $oldFile = public_path('storage/' . $employee->pan_attachment);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $file = $request->file('pan_attachment')->store('public/pan_attachments');
            $employee->pan_attachment = str_replace('public/', '', $file);
        }

        if ($request->file('other_attachment')) {
            if ($employee->other_attachment) {
                $oldFile = public_path('storage/' . $employee->other_attachment);
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $file = $request->file('other_attachment')->store('public/other_attachments');
            $employee->other_attachment = str_replace('public/', '', $file);
        }

        // =========================
        // EXTRA FIELDS
        // =========================
        $employee->joining_date = $request->joining_date;
        $employee->office_id = $targetOfficeId;

        if ($request->filled('check_in_time') && $request->filled('check_out_time')) {
            $checkInTime = Carbon::parse($request->check_in_time);
            $checkOutTime = Carbon::parse($request->check_out_time);
            $employee->office_time = $checkInTime->diffInMinutes($checkOutTime);
        }

        if ($request->role) {
            $employee->syncRoles([$request->role]);
        }

        $employee->save();

        // =========================
        // SALARY DATA
        // =========================
        $basicSalary = $request->basic_salary ?? 0;
        $houseRentAllowance = $request->house_rent_allowance ?? 0;
        $transportAllowance = $request->transport_allowance ?? 0;
        $medicalAllowance = $request->medical_allowance ?? 0;
        $specialAllowance = $request->special_allowance ?? 0;
        $dearnessAllowance = $request->dearness_allowance ?? 0;
        $relievingCharge = $request->relieving_charge ?? 0;
        $additionalAllowance = $request->additional_allowance ?? 0;
        $providentFund = $request->provident_fund ?? 0;
        $esic = $request->employee_state_insurance_corporation ?? 0;

        $totalSalary = $basicSalary
            + $houseRentAllowance
            + $transportAllowance
            + $medicalAllowance
            + $specialAllowance
            + $dearnessAllowance
            + $relievingCharge
            + $additionalAllowance;

        $userSalary = UserSalary::where('user_id', $employee->id)->first();

        if ($userSalary) {
            $userSalary->update([
                'basic_salary' => $basicSalary,
                'house_rent_allowance' => $houseRentAllowance,
                'transport_allowance' => $transportAllowance,
                'medical_allowance' => $medicalAllowance,
                'special_allowance' => $specialAllowance,
                'dearness_allowance' => $dearnessAllowance,
                'relieving_charge' => $relievingCharge,
                'additional_allowance' => $additionalAllowance,
                'provident_fund' => $providentFund,
                'employee_state_insurance_corporation' => $esic,
                'total_salary' => $totalSalary,
            ]);
        } else {
            UserSalary::create([
                'user_id' => $employee->id,
                'basic_salary' => $basicSalary,
                'house_rent_allowance' => $houseRentAllowance,
                'transport_allowance' => $transportAllowance,
                'medical_allowance' => $medicalAllowance,
                'special_allowance' => $specialAllowance,
                'dearness_allowance' => $dearnessAllowance,
                'relieving_charge' => $relievingCharge,
                'additional_allowance' => $additionalAllowance,
                'provident_fund' => $providentFund,
                'employee_state_insurance_corporation' => $esic,
                'total_salary' => $totalSalary,
            ]);
        }

        return redirect('employee')->with('success', 'Record Updated successfully');
    }


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


}
