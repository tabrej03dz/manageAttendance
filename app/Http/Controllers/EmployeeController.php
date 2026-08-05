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

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{







private function hasSwitchOfficeAccess($user): bool
{
    return $user->hasRole('super_admin')
        || $user->hasRole('owner')
        || $user->can('switch offices')
        || $user->can('switch office');
}

private function activeOfficeId(Request $request): ?int
{
    $sessionOfficeId = $request->session()->get('active_office_id');

    if ($sessionOfficeId && (int) $sessionOfficeId > 0) {
        return (int) $sessionOfficeId;
    }

    $userOfficeId = $request->user()?->office_id;

    return $userOfficeId && (int) $userOfficeId > 0
        ? (int) $userOfficeId
        : null;
}

private function switchableOfficeIds(Request $request): array
{
    $user = $request->user();

    if (!$user) {
        return [];
    }

    if ($user->hasRole('super_admin')) {
        return Office::query()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    if ($user->hasRole('owner')) {
        return Office::query()
            ->where('owner_id', $user->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    if ($user->can('switch offices') || $user->can('switch office')) {
        $currentOffice = $user->office;

        if ($currentOffice && $currentOffice->owner_id) {
            return Office::query()
                ->where('owner_id', $currentOffice->owner_id)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        }
    }

    return $user->office_id && (int) $user->office_id > 0
        ? [(int) $user->office_id]
        : [];
}

private function allowedOfficeIds(Request $request): array
{
    $switchableOfficeIds = $this->switchableOfficeIds($request);
    $activeOfficeId = $this->activeOfficeId($request);

    if ($activeOfficeId) {
        return in_array((int) $activeOfficeId, $switchableOfficeIds, true)
            ? [(int) $activeOfficeId]
            : [];
    }

    return $switchableOfficeIds;
}

private function officeEmployeesQuery(Request $request)
{
    $officeIds = $this->allowedOfficeIds($request);

    return User::query()
        ->when(!empty($officeIds), function ($q) use ($officeIds) {
            $q->whereIn('office_id', $officeIds);
        }, function ($q) {
            $q->whereRaw('1 = 0');
        });
}



    // private function sortEmployeesHierarchically($employees)
    // {
    //     $employees = collect($employees);

    //     $grouped = $employees->groupBy('team_leader_id');
    //     $sorted = collect();

    //     $appendChildren = function ($leaderId) use (&$appendChildren, $grouped, &$sorted) {
    //         if (!isset($grouped[$leaderId])) {
    //             return;
    //         }

    //         foreach ($grouped[$leaderId]->sortBy('name') as $employee) {
    //             $sorted->push($employee);
    //             $appendChildren($employee->id);
    //         }
    //     };

    //     if (isset($grouped[null])) {
    //         foreach ($grouped[null]->sortBy('name') as $employee) {
    //             $sorted->push($employee);
    //             $appendChildren($employee->id);
    //         }
    //     }

    //     $remaining = $employees->whereNotIn('id', $sorted->pluck('id'));

    //     foreach ($remaining->sortBy('name') as $employee) {
    //         $sorted->push($employee);
    //         $appendChildren($employee->id);
    //     }

    //     return $sorted->unique('id')->values();
    // }


    private function sortEmployeesHierarchically(
    \Illuminate\Support\Collection $employees
): \Illuminate\Support\Collection {
    /*
    |--------------------------------------------------------------------------
    | Normalize employees
    |--------------------------------------------------------------------------
    */

    $employees = $employees
        ->filter(function ($employee) {
            return !empty($employee->id);
        })
        ->unique('id')
        ->values();

    if ($employees->isEmpty()) {
        return collect();
    }

    /*
    |--------------------------------------------------------------------------
    | Employee lookup
    |--------------------------------------------------------------------------
    */

    $employeeById = $employees->keyBy(function ($employee) {
        return (int) $employee->id;
    });

    /*
    |--------------------------------------------------------------------------
    | Group employees by team leader
    |--------------------------------------------------------------------------
    */

    $childrenByLeader = [];

    foreach ($employees as $employee) {
        $employeeId = (int) $employee->id;

        $leaderId = !empty($employee->team_leader_id)
            ? (int) $employee->team_leader_id
            : 0;

        /*
         * Self-reference को root employee मानेंगे।
         */
        if ($leaderId === $employeeId) {
            $leaderId = 0;
        }

        /*
         * Leader employee current collection में मौजूद नहीं है,
         * तो employee root level पर दिखेगा।
         */
        if (
            $leaderId > 0
            && !$employeeById->has($leaderId)
        ) {
            $leaderId = 0;
        }

        $childrenByLeader[$leaderId][] = $employee;
    }

    /*
    |--------------------------------------------------------------------------
    | Sort each group alphabetically
    |--------------------------------------------------------------------------
    */

    foreach ($childrenByLeader as &$children) {
        usort(
            $children,
            function ($first, $second) {
                return strcasecmp(
                    (string) ($first->name ?? ''),
                    (string) ($second->name ?? '')
                );
            }
        );
    }

    unset($children);

    /*
    |--------------------------------------------------------------------------
    | Safe hierarchy traversal
    |--------------------------------------------------------------------------
    */

    $sorted = collect();
    $visited = [];
    $processing = [];

    $appendEmployee = function (
        $employee,
        int $level = 0
    ) use (
        &$appendEmployee,
        &$sorted,
        &$visited,
        &$processing,
        $childrenByLeader
    ) {
        $employeeId = (int) $employee->id;

        /*
         * Employee पहले add हो चुका है।
         */
        if (isset($visited[$employeeId])) {
            return;
        }

        /*
         * Circular hierarchy detected.
         */
        if (isset($processing[$employeeId])) {
            return;
        }

        $processing[$employeeId] = true;

        $employee->hierarchy_level = $level;

        $sorted->push($employee);

        $visited[$employeeId] = true;

        $children = $childrenByLeader[$employeeId] ?? [];

        foreach ($children as $child) {
            $appendEmployee(
                $child,
                $level + 1
            );
        }

        unset($processing[$employeeId]);
    };

    /*
    |--------------------------------------------------------------------------
    | First append root employees
    |--------------------------------------------------------------------------
    */

    foreach ($childrenByLeader[0] ?? [] as $rootEmployee) {
        $appendEmployee($rootEmployee, 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Append remaining employees
    |--------------------------------------------------------------------------
    |
    | Circular or broken hierarchy वाले employees छूटने नहीं चाहिए।
    |
    */

    foreach ($employees as $employee) {
        $employeeId = (int) $employee->id;

        if (!isset($visited[$employeeId])) {
            $appendEmployee($employee, 0);
        }
    }

    return $sorted->values();
}



    // public function index(Request $request)
    // {
    //     $query = $this->officeEmployeesQuery($request);

    //     if ($request->filled('q')) {
    //         $q = trim($request->q);
    //         $query->where(function ($qq) use ($q) {
    //             $qq->where('name', 'like', "%{$q}%")
    //                 ->orWhere('email', 'like', "%{$q}%")
    //                 ->orWhere('phone', 'like', "%{$q}%");
    //         });
    //     }

    //     if ($request->filled('status')) {
    //         $query->where('status', $request->status);
    //     } else {
    //         $query->where('status', '1');
    //     }

    //     if ($request->filled('department_id')) {
    //         $query->where('department_id', $request->department_id);
    //     }

    //     $allowedOfficeIds = $this->allowedOfficeIds($request);

    //     if ($request->filled('office_id')) {
    //         $requestedOfficeId = (int) $request->office_id;

    //         if (in_array($requestedOfficeId, $allowedOfficeIds, true)) {
    //             $query->where('office_id', $requestedOfficeId);
    //         } else {
    //             $query->whereRaw('1 = 0');
    //         }
    //     }

    //     if ($request->filled('office_unassigned') && $request->office_unassigned == '1') {
    //         $query->whereNull('office_id');
    //     }

    //     $employees = $query->get();

    //     // hierarchy order preserve
    //     $employees = $this->sortEmployeesHierarchically($employees);

    //     $departments = Department::all();

    //     $offices = Office::when(!empty($allowedOfficeIds), function ($q) use ($allowedOfficeIds) {
    //             $q->whereIn('id', $allowedOfficeIds);
    //         })
    //         ->orderBy('name')
    //         ->get();

    //     $unassignedCount = (clone $this->officeEmployeesQuery($request))
    //         ->whereNull('office_id')
    //         ->count();

    //     $perPage = 25;
    //     $currentPage = Paginator::resolveCurrentPage();

    //     $paginatedEmployees = new LengthAwarePaginator(
    //         $employees->forPage($currentPage, $perPage)->values(),
    //         $employees->count(),
    //         $perPage,
    //         $currentPage,
    //         [
    //             'path' => Paginator::resolveCurrentPath(),
    //             'query' => $request->query(),
    //         ]
    //     );

    //     return view('dashboard.employee.index', [
    //         'employees' => $paginatedEmployees,
    //         'departments' => $departments,
    //         'offices' => $offices,
    //         'unassignedCount' => $unassignedCount,
    //     ]);
    // }


public function index(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | Allowed offices
    |--------------------------------------------------------------------------
    */

    $allowedOfficeIds = $this->allowedOfficeIds($request);

    /*
    |--------------------------------------------------------------------------
    | Base employee query
    |--------------------------------------------------------------------------
    */

    $query = $this->officeEmployeesQuery($request)
        ->select([
            'id',
            'name',
            'email',
            'phone',
            'status',
            'office_id',
            'department_id',
            'team_leader_id',
            'created_at',
        ])
        ->with([
            'office:id,name',
            'department:id,name',
            'teamLeader:id,name',
        ]);

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($request->filled('q')) {
        $search = trim((string) $request->input('q'));

        $query->where(function ($subQuery) use ($search) {
            $subQuery
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('phone', 'like', '%' . $search . '%');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Status filter
    |--------------------------------------------------------------------------
    */

    if ($request->filled('status')) {
        $query->where(
            'status',
            (string) $request->input('status')
        );
    } else {
        $query->where('status', '1');
    }

    /*
    |--------------------------------------------------------------------------
    | Department filter
    |--------------------------------------------------------------------------
    */

    if ($request->filled('department_id')) {
        $query->where(
            'department_id',
            (int) $request->input('department_id')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Office filter
    |--------------------------------------------------------------------------
    */

    if ($request->filled('office_id')) {
        $requestedOfficeId = (int) $request->input('office_id');

        if (
            in_array(
                $requestedOfficeId,
                $allowedOfficeIds,
                true
            )
        ) {
            $query->where('office_id', $requestedOfficeId);
        } else {
            $query->whereRaw('1 = 0');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Unassigned office filter
    |--------------------------------------------------------------------------
    */

    if ($request->boolean('office_unassigned')) {
        $query->whereNull('office_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Database pagination
    |--------------------------------------------------------------------------
    |
    | Recursive hierarchy sorting intentionally removed.
    | केवल current page के 25 employees database से load होंगे।
    |
    */

    $employees = $query
        ->orderByRaw(
            'CASE WHEN team_leader_id IS NULL THEN 0 ELSE 1 END'
        )
        ->orderBy('team_leader_id')
        ->orderBy('name')
        ->paginate(25)
        ->withQueryString();

    /*
    |--------------------------------------------------------------------------
    | Departments
    |--------------------------------------------------------------------------
    */

    $departments = Department::query()
        ->select([
            'id',
            'name',
        ])
        ->orderBy('name')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Offices
    |--------------------------------------------------------------------------
    */

    $offices = Office::query()
        ->select([
            'id',
            'name',
        ])
        ->when(
            !empty($allowedOfficeIds),
            function ($officeQuery) use ($allowedOfficeIds) {
                $officeQuery->whereIn(
                    'id',
                    $allowedOfficeIds
                );
            }
        )
        ->orderBy('name')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Unassigned employees count
    |--------------------------------------------------------------------------
    */

    $unassignedQuery = $this->officeEmployeesQuery($request)
        ->whereNull('office_id');

    if (
        empty($allowedOfficeIds)
        && !$request->user()->hasRole('super_admin')
    ) {
        $unassignedQuery->whereRaw('1 = 0');
    }

    $unassignedCount = $unassignedQuery->count();

    return view('dashboard.employee.index', [
        'employees' => $employees,
        'departments' => $departments,
        'offices' => $offices,
        'unassignedCount' => $unassignedCount,
    ]);
}


    // public function create(Request $request)
    // {
    //     $user = $request->user();

    //     if ($user->hasRole('super_admin')) {
    //         $offices = Office::orderBy('name')->get();

    //         $teamLeaders = User::whereHas('roles', function ($q) {
    //                 $q->where('name', 'team_leader');
    //             })
    //             ->with('office')
    //             ->get();
    //     } elseif ($user->hasRole('owner')) {
    //         // owner ke saare offices lao
    //         $offices = Office::where('owner_id', $user->id)
    //             ->orderBy('name')
    //             ->get();

    //         if ($offices->isEmpty()) {
    //             return back()->with('error', 'No office found for this owner.');
    //         }

    //         // owner ke total employees count sab offices ka
    //         $officeIds = $offices->pluck('id');

    //         $plan = Plan::where('user_id', $user->id)->latest()->first();
    //         $employeeCount = User::whereIn('office_id', $officeIds)->count();

    //         if ($plan && $employeeCount >= $plan->number_of_employees) {
    //             return back()->with('error', 'Your employee creation limit exceeded!');
    //         }

    //         $teamLeaders = User::whereIn('office_id', $officeIds)
    //             ->role('team_leader')
    //             ->with('office')
    //             ->get();
    //     } else {
    //         $activeOfficeId = $user->activeOfficeId();

    //         if (!$activeOfficeId) {
    //             return back()->with('error', 'Please select an office first.');
    //         }

    //         $owner = optional($user->office)->owner;

    //         if ($owner) {
    //             $plan = Plan::where('user_id', $owner->id)->latest()->first();

    //             $employeeCount = User::where('office_id', $activeOfficeId)->count();

    //             if ($plan && $employeeCount >= $plan->number_of_employees) {
    //                 return back()->with('error', 'Your employee creation limit exceeded!');
    //             }
    //         }

    //         $offices = Office::where('id', $activeOfficeId)->orderBy('name')->get();

    //         $teamLeaders = User::where('office_id', $activeOfficeId)
    //             ->role('team_leader')
    //             ->with('office')
    //             ->get();
    //     }

    //     $departments = Department::all();

    //     return view('dashboard.employee.create', compact('offices', 'teamLeaders', 'departments'));
    // }


    // public function create(Request $request)
    // {
    //     $loggedInUser = $request->user();

    //     if ($loggedInUser->hasRole('super_admin')) {
    //         $offices = Office::query()
    //             ->select('id', 'name', 'owner_id')
    //             ->orderBy('name')
    //             ->get();

    //         $allowedOfficeIds = $offices->pluck('id');

    //     } elseif ($loggedInUser->hasRole('owner')) {
    //         $offices = Office::query()
    //             ->select('id', 'name', 'owner_id')
    //             ->where('owner_id', $loggedInUser->id)
    //             ->orderBy('name')
    //             ->get();

    //         if ($offices->isEmpty()) {
    //             return back()->with('error', 'No office found for this owner.');
    //         }

    //         $allowedOfficeIds = $offices->pluck('id');

    //         $plan = Plan::query()
    //             ->where('user_id', $loggedInUser->id)
    //             ->latest('id')
    //             ->first();

    //         $employeeCount = User::query()
    //             ->whereIn('office_id', $allowedOfficeIds)
    //             ->count();

    //         if ($plan && $employeeCount >= $plan->number_of_employees) {
    //             return back()->with(
    //                 'error',
    //                 'Your employee creation limit exceeded!'
    //             );
    //         }

    //     } else {
    //         $activeOfficeId = $loggedInUser->activeOfficeId();

    //         if (!$activeOfficeId) {
    //             return back()->with(
    //                 'error',
    //                 'Please select an office first.'
    //             );
    //         }

    //         $office = Office::query()
    //             ->select('id', 'name', 'owner_id')
    //             ->find($activeOfficeId);

    //         if (!$office) {
    //             return back()->with(
    //                 'error',
    //                 'Selected office was not found.'
    //             );
    //         }

    //         $offices = collect([$office]);
    //         $allowedOfficeIds = collect([(int) $office->id]);

    //         $owner = $office->owner;

    //         if ($owner) {
    //             $plan = Plan::query()
    //                 ->where('user_id', $owner->id)
    //                 ->latest('id')
    //                 ->first();

    //             $employeeCount = User::query()
    //                 ->where('office_id', $office->id)
    //                 ->count();

    //             if ($plan && $employeeCount >= $plan->number_of_employees) {
    //                 return back()->with(
    //                     'error',
    //                     'Your employee creation limit exceeded!'
    //                 );
    //             }
    //         }
    //     }

    //     $teamLeaders = User::query()
    //         ->select('id', 'name', 'office_id')
    //         ->where('status', 1)
    //         ->whereIn('office_id', $allowedOfficeIds)
    //         ->role('team_leader')
    //         ->with('office:id,name')
    //         ->orderBy('name')
    //         ->get();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Leave authorities
    //     |--------------------------------------------------------------------------
    //     |
    //     | Admin, team leader, owner and super admin can be selected.
    //     | Same accessible offices are used, so hierarchy/security remains intact.
    //     |
    //     */

    //     $leaveAuthorities = User::query()
    //         ->select('id', 'name', 'office_id', 'status')
    //         ->where('status', 1)
    //         ->whereIn('office_id', $allowedOfficeIds)
    //         ->whereHas('roles', function ($roleQuery) {
    //             $roleQuery->whereIn('name', [
    //                 'owner',
    //                 'admin',
    //                 'team_leader',
    //                 'super_admin',
    //             ]);
    //         })
    //         ->with('office:id,name')
    //         ->orderBy('name')
    //         ->get();

    //     /*
    //     * Super admin generally has no office_id. Include active super admins too.
    //     */
    //     if ($loggedInUser->hasRole('super_admin')) {
    //         $superAdmins = User::query()
    //             ->select('id', 'name', 'office_id', 'status')
    //             ->where('status', 1)
    //             ->whereHas('roles', function ($roleQuery) {
    //                 $roleQuery->where('name', 'super_admin');
    //             })
    //             ->with('office:id,name')
    //             ->orderBy('name')
    //             ->get();

    //         $leaveAuthorities = $leaveAuthorities
    //             ->merge($superAdmins)
    //             ->unique('id')
    //             ->sortBy('name')
    //             ->values();
    //     }

    //     $departments = Department::query()
    //         ->select('id', 'name')
    //         ->orderBy('name')
    //         ->get();

    //     return view('dashboard.employee.create', compact(
    //         'offices',
    //         'teamLeaders',
    //         'leaveAuthorities',
    //         'departments'
    //     ));
    // }

    public function create(Request $request)
{
    $loggedInUser = $request->user();

    if ($loggedInUser->hasRole('super_admin')) {
        $offices = Office::query()
            ->select('id', 'name', 'owner_id')
            ->orderBy('name')
            ->get();

        $allowedOfficeIds = $offices->pluck('id')->map(fn ($id) => (int) $id);

    } elseif ($loggedInUser->hasRole('owner')) {
        $offices = Office::query()
            ->select('id', 'name', 'owner_id')
            ->where('owner_id', $loggedInUser->id)
            ->orderBy('name')
            ->get();

        if ($offices->isEmpty()) {
            return back()->with('error', 'No office found for this owner.');
        }

        $allowedOfficeIds = $offices->pluck('id')->map(fn ($id) => (int) $id);

        $plan = Plan::query()
            ->where('user_id', $loggedInUser->id)
            ->latest('id')
            ->first();

        $employeeCount = User::query()
            ->whereIn('office_id', $allowedOfficeIds)
            ->count();

        if ($plan && $employeeCount >= (int) $plan->number_of_employees) {
            return back()->with('error', 'Your employee creation limit exceeded!');
        }

    } else {
        $activeOfficeId = (int) $loggedInUser->activeOfficeId();

        if (!$activeOfficeId) {
            return back()->with('error', 'Please select an office first.');
        }

        $office = Office::query()
            ->select('id', 'name', 'owner_id')
            ->with('owner:id,name')
            ->find($activeOfficeId);

        if (!$office) {
            return back()->with('error', 'Selected office was not found.');
        }

        $offices = collect([$office]);
        $allowedOfficeIds = collect([(int) $office->id]);

        if ($office->owner) {
            $plan = Plan::query()
                ->where('user_id', $office->owner->id)
                ->latest('id')
                ->first();

            $employeeCount = User::query()
                ->where('office_id', $office->id)
                ->count();

            if ($plan && $employeeCount >= (int) $plan->number_of_employees) {
                return back()->with('error', 'Your employee creation limit exceeded!');
            }
        }
    }

    $teamLeaders = User::query()
        ->select([
            'users.id',
            'users.name',
            'users.office_id',
            'users.status',
        ])
        ->whereIn('users.office_id', $allowedOfficeIds)
        ->whereHas('roles', function ($query) {
            $query->whereIn('roles.name', [
                'team_leader',
                'admin',
                'owner',
            ]);
        })
        ->with([
            'office:id,name',
            'roles:id,name',
        ])
        ->orderBy('users.name')
        ->get();


    $leaveAuthorities = User::query()
        ->select('users.id', 'users.name', 'users.office_id', 'users.status')
        ->where('users.status', 1)
        ->whereIn('users.office_id', $allowedOfficeIds)
        ->whereHas('roles', function ($roleQuery) {
            $roleQuery->whereIn('roles.name', [
                'owner',
                'admin',
                'team_leader',
            ]);
        })
        ->with('office:id,name')
        ->orderBy('users.name')
        ->get();

    if ($loggedInUser->hasRole('super_admin')) {
        $superAdmins = User::query()
            ->select('users.id', 'users.name', 'users.office_id', 'users.status')
            ->where('users.status', 1)
            ->whereHas('roles', function ($roleQuery) {
                $roleQuery->where('roles.name', 'super_admin');
            })
            ->with('office:id,name')
            ->get();

        $leaveAuthorities = $leaveAuthorities
            ->merge($superAdmins)
            ->unique('id')
            ->sortBy('name')
            ->values();
    }

    $departments = Department::query()
        ->select('id', 'name')
        ->orderBy('name')
        ->get();

    return view('dashboard.employee.create', compact(
        'offices',
        'teamLeaders',
        'leaveAuthorities',
        'departments'
    ));
}







    // public function store(EmployeeRequest $request)
    // {
    //     $user = $request->user();

    //     // =========================
    //     // OFFICE RESOLUTION
    //     // =========================
    //     if ($user->hasRole('super_admin')) {

    //         $targetOfficeId = $request->office_id;

    //         if (!$targetOfficeId) {
    //             return back()->with('error', 'Please select an office first.')->withInput();
    //         }

    //     } elseif ($user->hasRole('owner')) {

    //         // owner ke saare offices
    //         $ownerOfficeIds = Office::where('owner_id', $user->id)->pluck('id');

    //         if ($ownerOfficeIds->isEmpty()) {
    //             return back()->with('error', 'No office found for this owner.')->withInput();
    //         }

    //         // agar dropdown se office select ho raha hai to use lo, warna active
    //         $targetOfficeId = $request->office_id ?: $user->activeOfficeId();

    //         if (!$targetOfficeId || !$ownerOfficeIds->contains($targetOfficeId)) {
    //             return back()->with('error', 'Invalid office selected.')->withInput();
    //         }

    //         // ✅ PLAN CHECK (OWNER TOTAL EMPLOYEES)
    //         $plan = Plan::where('user_id', $user->id)->latest()->first();

    //         $employeeCount = User::whereIn('office_id', $ownerOfficeIds)->count();

    //         if ($plan && $employeeCount >= $plan->number_of_employees) {
    //             return back()->with('error', 'Your employee creation limit exceeded!')->withInput();
    //         }

    //     } else {

    //         $targetOfficeId = $user->activeOfficeId();

    //         if (!$targetOfficeId) {
    //             return back()->with('error', 'Please select an office first.')->withInput();
    //         }

    //         // owner निकालो
    //         $owner = optional($user->office)->owner;

    //         if ($owner) {
    //             $plan = Plan::where('user_id', $owner->id)->latest()->first();

    //             $employeeCount = User::where('office_id', $targetOfficeId)->count();

    //             if ($plan && $employeeCount >= $plan->number_of_employees) {
    //                 return back()->with('error', 'Your employee creation limit exceeded!')->withInput();
    //             }
    //         }
    //     }

    //     // =========================
    //     // EMAIL CHECK
    //     // =========================
    //     $existingEmployee = User::where('email', $request->email)->first();
    //     if ($existingEmployee) {
    //         return back()->withErrors(['email' => 'Email already exists.'])->withInput();
    //     }

    //     // =========================
    //     // TIME CALCULATION
    //     // =========================
    //     $checkInTime = Carbon::parse($request->check_in_time);
    //     $checkOutTime = Carbon::parse($request->check_out_time);

    //     // =========================
    //     // CREATE EMPLOYEE
    //     // =========================
    //     $employee = User::create($request->except([
    //         'joining_date',
    //         'office_id',
    //         'photo',
    //         'aadhar_attachment',
    //         'pan_attachment',
    //         'other_attachment',
    //         'role',
    //         'basic_salary',
    //         'house_rent_allowance',
    //         'transport_allowance',
    //         'medical_allowance',
    //         'special_allowance',
    //         'dearness_allowance',
    //         'relieving_charge',
    //         'additional_allowance',
    //     ]) + [
    //         'office_id' => $targetOfficeId,
    //         'password' => Hash::make('password'),
    //     ]);

    //     // =========================
    //     // FILE UPLOADS
    //     // =========================
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

    //     // =========================
    //     // SAVE EXTRA DATA
    //     // =========================
    //     $employee->office_time = $checkInTime->diffInMinutes($checkOutTime);
    //     $employee->joining_date = $request->joining_date;
    //     $employee->save();

    //     // =========================
    //     // ROLE ASSIGN
    //     // =========================
    //     $employee->assignRole($request->role ?? 'employee');

    //     // =========================
    //     // SALARY
    //     // =========================
    //     $basic_salary = $request->basic_salary ?? 0;
    //     $house_rent_allowance = $request->house_rent_allowance ?? 0;
    //     $transport_allowance = $request->transport_allowance ?? 0;
    //     $medical_allowance = $request->medical_allowance ?? 0;
    //     $special_allowance = $request->special_allowance ?? 0;
    //     $dearness_allowance = $request->dearness_allowance ?? 0;
    //     $relieving_charge = $request->relieving_charge ?? 0;
    //     $additional_allowance = $request->additional_allowance ?? 0;

    //     $total_salary =
    //         $basic_salary +
    //         $house_rent_allowance +
    //         $transport_allowance +
    //         $medical_allowance +
    //         $special_allowance +
    //         $dearness_allowance +
    //         $relieving_charge +
    //         $additional_allowance;

    //     UserSalary::create([
    //         'user_id' => $employee->id,
    //         'basic_salary' => $basic_salary,
    //         'house_rent_allowance' => $house_rent_allowance,
    //         'transport_allowance' => $transport_allowance,
    //         'medical_allowance' => $medical_allowance,
    //         'special_allowance' => $special_allowance,
    //         'dearness_allowance' => $dearness_allowance,
    //         'relieving_charge' => $relieving_charge,
    //         'additional_allowance' => $additional_allowance,
    //         'total_salary' => $total_salary,
    //     ]);

    //     return redirect('employee')->with('success', 'Employee Registered successfully');
    // }

    // public function store(EmployeeRequest $request)
    // {
    //     $loggedInUser = $request->user();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Resolve target office
    //     |--------------------------------------------------------------------------
    //     */

    //     if ($loggedInUser->hasRole('super_admin')) {
    //         $targetOfficeId = (int) $request->input('office_id');

    //         if (!$targetOfficeId || !Office::whereKey($targetOfficeId)->exists()) {
    //             return back()
    //                 ->withErrors([
    //                     'office_id' => 'Please select a valid office.',
    //                 ])
    //                 ->withInput();
    //         }

    //     } elseif ($loggedInUser->hasRole('owner')) {
    //         $ownerOfficeIds = Office::query()
    //             ->where('owner_id', $loggedInUser->id)
    //             ->pluck('id')
    //             ->map(fn ($id) => (int) $id);

    //         if ($ownerOfficeIds->isEmpty()) {
    //             return back()
    //                 ->with('error', 'No office found for this owner.')
    //                 ->withInput();
    //         }

    //         $targetOfficeId = (int) (
    //             $request->input('office_id')
    //             ?: $loggedInUser->activeOfficeId()
    //         );

    //         if (!$targetOfficeId || !$ownerOfficeIds->contains($targetOfficeId)) {
    //             return back()
    //                 ->withErrors([
    //                     'office_id' => 'Invalid office selected.',
    //                 ])
    //                 ->withInput();
    //         }

    //         $plan = Plan::query()
    //             ->where('user_id', $loggedInUser->id)
    //             ->latest('id')
    //             ->first();

    //         $employeeCount = User::query()
    //             ->whereIn('office_id', $ownerOfficeIds)
    //             ->count();

    //         if ($plan && $employeeCount >= $plan->number_of_employees) {
    //             return back()
    //                 ->with('error', 'Your employee creation limit exceeded!')
    //                 ->withInput();
    //         }

    //     } else {
    //         $targetOfficeId = (int) $loggedInUser->activeOfficeId();

    //         if (!$targetOfficeId) {
    //             return back()
    //                 ->with('error', 'Please select an office first.')
    //                 ->withInput();
    //         }

    //         $office = Office::query()->find($targetOfficeId);

    //         if (!$office) {
    //             return back()
    //                 ->withErrors([
    //                     'office_id' => 'Selected office was not found.',
    //                 ])
    //                 ->withInput();
    //         }

    //         $owner = $office->owner;

    //         if ($owner) {
    //             $plan = Plan::query()
    //                 ->where('user_id', $owner->id)
    //                 ->latest('id')
    //                 ->first();

    //             $employeeCount = User::query()
    //                 ->where('office_id', $targetOfficeId)
    //                 ->count();

    //             if ($plan && $employeeCount >= $plan->number_of_employees) {
    //                 return back()
    //                     ->with('error', 'Your employee creation limit exceeded!')
    //                     ->withInput();
    //             }
    //         }
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Leave authority validation
    //     |--------------------------------------------------------------------------
    //     */

    //     $request->validate([
    //         'leave_authority_id' => [
    //             'nullable',
    //             'integer',
    //             Rule::exists('users', 'id')->where(function ($query) use ($targetOfficeId) {
    //                 $query->where('status', 1)
    //                     ->where(function ($officeQuery) use ($targetOfficeId) {
    //                         $officeQuery
    //                             ->where('office_id', $targetOfficeId)
    //                             ->orWhereNull('office_id');
    //                     });
    //             }),
    //         ],
    //     ]);

    //     $leaveAuthorityId = $request->filled('leave_authority_id')
    //         ? (int) $request->input('leave_authority_id')
    //         : null;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Reporting manager validation
    //     |--------------------------------------------------------------------------
    //     */

    //     if ($request->filled('team_leader_id')) {
    //         $validTeamLeader = User::query()
    //             ->whereKey((int) $request->input('team_leader_id'))
    //             ->where('office_id', $targetOfficeId)
    //             ->where('status', 1)
    //             ->role('team_leader')
    //             ->exists();

    //         if (!$validTeamLeader) {
    //             return back()
    //                 ->withErrors([
    //                     'team_leader_id' => 'Invalid reporting manager selected.',
    //                 ])
    //                 ->withInput();
    //         }
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Email and time checks
    //     |--------------------------------------------------------------------------
    //     */

    //     if (
    //         $request->filled('email')
    //         && User::where('email', $request->input('email'))->exists()
    //     ) {
    //         return back()
    //             ->withErrors([
    //                 'email' => 'Email already exists.',
    //             ])
    //             ->withInput();
    //     }

    //     try {
    //         $checkInTime = Carbon::parse($request->input('check_in_time'));
    //         $checkOutTime = Carbon::parse($request->input('check_out_time'));
    //     } catch (\Throwable $exception) {
    //         return back()
    //             ->withErrors([
    //                 'check_in_time' => 'Please enter valid check-in and check-out times.',
    //             ])
    //             ->withInput();
    //     }

    //     if ($checkOutTime->lessThanOrEqualTo($checkInTime)) {
    //         $checkOutTime->addDay();
    //     }

    //     DB::beginTransaction();

    //     try {
    //         $employeeData = $request->except([
    //             'joining_date',
    //             'office_id',
    //             'photo',
    //             'aadhar_attachment',
    //             'pan_attachment',
    //             'other_attachment',
    //             'role',
    //             'leave_authority_id',
    //             'basic_salary',
    //             'house_rent_allowance',
    //             'transport_allowance',
    //             'medical_allowance',
    //             'special_allowance',
    //             'dearness_allowance',
    //             'relieving_charge',
    //             'additional_allowance',
    //             'provident_fund',
    //             'employee_state_insurance_corporation',
    //         ]);

    //         $employeeData['office_id'] = $targetOfficeId;
    //         $employeeData['leave_authority_id'] = $leaveAuthorityId;
    //         $employeeData['password'] = Hash::make('password');
    //         $employeeData['joining_date'] = $request->input('joining_date');
    //         $employeeData['office_time'] = $checkInTime->diffInMinutes($checkOutTime);
    //         $employeeData['status'] = $request->input('status', 1);

    //         $employee = User::create($employeeData);

    //         if ($request->hasFile('photo')) {
    //             $path = $request->file('photo')->store('public/photos');
    //             $employee->photo = str_replace('public/', '', $path);
    //         }

    //         if ($request->hasFile('aadhar_attachment')) {
    //             $path = $request->file('aadhar_attachment')
    //                 ->store('public/aadhar_attachments');

    //             $employee->aadhar_attachment = str_replace('public/', '', $path);
    //         }

    //         if ($request->hasFile('pan_attachment')) {
    //             $path = $request->file('pan_attachment')
    //                 ->store('public/pan_attachments');

    //             $employee->pan_attachment = str_replace('public/', '', $path);
    //         }

    //         if ($request->hasFile('other_attachment')) {
    //             $path = $request->file('other_attachment')
    //                 ->store('public/other_attachments');

    //             $employee->other_attachment = str_replace('public/', '', $path);
    //         }

    //         $employee->save();

    //         $employee->syncRoles([
    //             $request->input('role', 'employee'),
    //         ]);

    //         $basicSalary = (float) $request->input('basic_salary', 0);
    //         $houseRentAllowance = (float) $request->input('house_rent_allowance', 0);
    //         $transportAllowance = (float) $request->input('transport_allowance', 0);
    //         $medicalAllowance = (float) $request->input('medical_allowance', 0);
    //         $specialAllowance = (float) $request->input('special_allowance', 0);
    //         $dearnessAllowance = (float) $request->input('dearness_allowance', 0);
    //         $relievingCharge = (float) $request->input('relieving_charge', 0);
    //         $additionalAllowance = (float) $request->input('additional_allowance', 0);
    //         $providentFund = (float) $request->input('provident_fund', 0);
    //         $esic = (float) $request->input(
    //             'employee_state_insurance_corporation',
    //             0
    //         );

    //         $totalSalary =
    //             $basicSalary
    //             + $houseRentAllowance
    //             + $transportAllowance
    //             + $medicalAllowance
    //             + $specialAllowance
    //             + $dearnessAllowance
    //             + $relievingCharge
    //             + $additionalAllowance;

    //         UserSalary::create([
    //             'user_id' => $employee->id,
    //             'basic_salary' => $basicSalary,
    //             'house_rent_allowance' => $houseRentAllowance,
    //             'transport_allowance' => $transportAllowance,
    //             'medical_allowance' => $medicalAllowance,
    //             'special_allowance' => $specialAllowance,
    //             'dearness_allowance' => $dearnessAllowance,
    //             'relieving_charge' => $relievingCharge,
    //             'additional_allowance' => $additionalAllowance,
    //             'provident_fund' => $providentFund,
    //             'employee_state_insurance_corporation' => $esic,
    //             'total_salary' => $totalSalary,
    //         ]);

    //         DB::commit();

    //         return redirect()
    //             ->route('employee.index')
    //             ->with('success', 'Employee registered successfully.');

    //     } catch (\Throwable $exception) {
    //         DB::rollBack();

    //         report($exception);

    //         return back()
    //             ->with('error', 'Employee could not be registered. Please try again.')
    //             ->withInput();
    //     }
    // }



    public function store(EmployeeRequest $request)
{
    $loggedInUser = $request->user();

    if ($loggedInUser->hasRole('super_admin')) {
        $targetOfficeId = (int) $request->input('office_id');

        if (!$targetOfficeId || !Office::whereKey($targetOfficeId)->exists()) {
            return back()->withErrors([
                'office_id' => 'Please select a valid office.',
            ])->withInput();
        }

    } elseif ($loggedInUser->hasRole('owner')) {
        $ownerOfficeIds = Office::query()
            ->where('owner_id', $loggedInUser->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        if ($ownerOfficeIds->isEmpty()) {
            return back()->with('error', 'No office found for this owner.')->withInput();
        }

        $targetOfficeId = (int) (
            $request->input('office_id') ?: $loggedInUser->activeOfficeId()
        );

        if (!$targetOfficeId || !$ownerOfficeIds->contains($targetOfficeId)) {
            return back()->withErrors([
                'office_id' => 'Invalid office selected.',
            ])->withInput();
        }

        $plan = Plan::query()
            ->where('user_id', $loggedInUser->id)
            ->latest('id')
            ->first();

        $employeeCount = User::query()
            ->whereIn('office_id', $ownerOfficeIds)
            ->count();

        if ($plan && $employeeCount >= (int) $plan->number_of_employees) {
            return back()->with('error', 'Your employee creation limit exceeded!')->withInput();
        }

    } else {
        $targetOfficeId = (int) $loggedInUser->activeOfficeId();

        if (!$targetOfficeId) {
            return back()->with('error', 'Please select an office first.')->withInput();
        }

        $office = Office::query()
            ->with('owner:id,name')
            ->find($targetOfficeId);

        if (!$office) {
            return back()->withErrors([
                'office_id' => 'Selected office was not found.',
            ])->withInput();
        }

        if ($office->owner) {
            $plan = Plan::query()
                ->where('user_id', $office->owner->id)
                ->latest('id')
                ->first();

            $employeeCount = User::query()
                ->where('office_id', $targetOfficeId)
                ->count();

            if ($plan && $employeeCount >= (int) $plan->number_of_employees) {
                return back()->with('error', 'Your employee creation limit exceeded!')->withInput();
            }
        }
    }

        $validatedExtra = $request->validate([
        'role' => [
            'required',
            Rule::in([
                'admin',
                'team_leader',
                'employee',
            ]),
        ],

        'status' => [
            'required',
            Rule::in([
                0,
                1,
                '0',
                '1',
            ]),
        ],

        'team_leader_id' => [
            'nullable',
            'integer',
            'exists:users,id',
        ],

        'leave_authority_id' => [
            'nullable',
            'integer',
            'exists:users,id',
        ],
    ]);

    if ($request->filled('team_leader_id')) {
        $validReportingManager = User::query()
            ->whereKey((int) $request->input('team_leader_id'))
            ->where('office_id', $targetOfficeId)
            ->whereHas('roles', function ($query) {
                $query->whereIn('roles.name', [
                    'team_leader',
                    'admin',
                    'owner',
                ]);
            })
            ->exists();

        if (!$validReportingManager) {
            return back()->withErrors([
                'team_leader_id' => 'Invalid reporting manager selected.',
            ])->withInput();
        }
    }

    if ($request->filled('leave_authority_id')) {
        $validLeaveAuthority = User::query()
            ->whereKey((int) $request->input('leave_authority_id'))
            ->where('office_id', $targetOfficeId)
            ->whereHas('roles', function ($query) {
                $query->whereIn('roles.name', [
                    'team_leader',
                    'admin',
                    'owner',
                ]);
            })
            ->exists();

        if (!$validLeaveAuthority) {
            return back()->withErrors([
                'leave_authority_id' => 'Invalid leave authority selected.',
            ])->withInput();
        }
    }

    if ($request->filled('email') && User::where('email', $request->email)->exists()) {
        return back()->withErrors([
            'email' => 'Email already exists.',
        ])->withInput();
    }

    try {
        $checkInTime = Carbon::parse($request->check_in_time);
        $checkOutTime = Carbon::parse($request->check_out_time);
    } catch (\Throwable $exception) {
        return back()->withErrors([
            'check_in_time' => 'Please enter valid check-in and check-out times.',
        ])->withInput();
    }

    if ($checkOutTime->lessThanOrEqualTo($checkInTime)) {
        $checkOutTime->addDay();
    }

    DB::beginTransaction();

    try {
        $employeeData = $request->except([
            'joining_date',
            'office_id',
            'photo',
            'aadhar_attachment',
            'pan_attachment',
            'other_attachment',
            'role',
            'team_leader_id',
            'leave_authority_id',
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
        ]);

        $employeeData['office_id'] = $targetOfficeId;
        $employeeData['team_leader_id'] = $request->filled('team_leader_id')
            ? (int) $validatedExtra['team_leader_id']
            : null;
        $employeeData['leave_authority_id'] = $request->filled('leave_authority_id')
            ? (int) $validatedExtra['leave_authority_id']
            : null;
        $employeeData['password'] = Hash::make('password');
        $employeeData['joining_date'] = $request->joining_date;
        $employeeData['office_time'] = $checkInTime->diffInMinutes($checkOutTime);
        // $employeeData['status'] = $request->input('status', 1);

        $employeeData['status'] = (int) $validatedExtra['status'];
        $employee = User::create($employeeData);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('public/photos');
            $employee->photo = str_replace('public/', '', $path);
        }

        if ($request->hasFile('aadhar_attachment')) {
            $path = $request->file('aadhar_attachment')->store('public/aadhar_attachments');
            $employee->aadhar_attachment = str_replace('public/', '', $path);
        }

        if ($request->hasFile('pan_attachment')) {
            $path = $request->file('pan_attachment')->store('public/pan_attachments');
            $employee->pan_attachment = str_replace('public/', '', $path);
        }

        if ($request->hasFile('other_attachment')) {
            $path = $request->file('other_attachment')->store('public/other_attachments');
            $employee->other_attachment = str_replace('public/', '', $path);
        }

        $employee->save();

        $employee->syncRoles([
            $validatedExtra['role'],
        ]);

        $basicSalary = (float) $request->input('basic_salary', 0);
        $houseRentAllowance = (float) $request->input('house_rent_allowance', 0);
        $transportAllowance = (float) $request->input('transport_allowance', 0);
        $medicalAllowance = (float) $request->input('medical_allowance', 0);
        $specialAllowance = (float) $request->input('special_allowance', 0);
        $dearnessAllowance = (float) $request->input('dearness_allowance', 0);
        $relievingCharge = (float) $request->input('relieving_charge', 0);
        $additionalAllowance = (float) $request->input('additional_allowance', 0);
        $providentFund = (float) $request->input('provident_fund', 0);
        $esic = (float) $request->input('employee_state_insurance_corporation', 0);

        $totalSalary = $basicSalary
            + $houseRentAllowance
            + $transportAllowance
            + $medicalAllowance
            + $specialAllowance
            + $dearnessAllowance
            + $relievingCharge
            + $additionalAllowance;

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

        DB::commit();

        return redirect()
            ->route('employee.index')
            ->with('success', 'Employee registered successfully.');

    } catch (\Throwable $exception) {
        DB::rollBack();
        report($exception);

        return back()
            ->with('error', 'Employee could not be registered. Please try again.')
            ->withInput();
    }
}



    // public function edit(Request $request, User $employee)
    // {
    //     $user = $request->user();

    //     if ($user->hasRole('super_admin')) {
    //         $offices = Office::orderBy('name')->get();

    //         $teamLeaders = User::whereHas('roles', function ($q) {
    //                 $q->where('name', 'team_leader');
    //             })
    //             ->with('office')
    //             ->get();

    //         // super admin kisi bhi office ke employee ko edit kar sakta hai
    //     } elseif ($user->hasRole('owner')) {
    //         $ownerOfficeIds = Office::where('owner_id', $user->id)->pluck('id');

    //         if ($ownerOfficeIds->isEmpty()) {
    //             return back()->with('error', 'No office found for this owner.');
    //         }

    //         $activeOfficeId = $user->activeOfficeId();

    //         if (!$activeOfficeId) {
    //             $activeOfficeId = $ownerOfficeIds->first();
    //         }

    //         // security: owner sirf apne office ke employee ko edit kare
    //         if (!$ownerOfficeIds->contains($employee->office_id)) {
    //             abort(403, 'This employee does not belong to your office.');
    //         }

    //         $offices = Office::whereIn('id', $ownerOfficeIds)
    //             ->orderBy('name')
    //             ->get();

    //         $teamLeaders = User::where('office_id', $activeOfficeId)
    //             ->role('team_leader')
    //             ->with('office')
    //             ->get();
    //     } else {
    //         $activeOfficeId = $user->activeOfficeId();

    //         if (!$activeOfficeId) {
    //             return back()->with('error', 'Please select an office first.');
    //         }

    //         if ((int) $employee->office_id !== (int) $activeOfficeId) {
    //             abort(403, 'This employee does not belong to the selected office.');
    //         }

    //         $offices = Office::where('id', $activeOfficeId)
    //             ->orderBy('name')
    //             ->get();

    //         $teamLeaders = User::where('office_id', $activeOfficeId)
    //             ->role('team_leader')
    //             ->with('office')
    //             ->get();
    //     }

    //     $departments = Department::all();

    //     return view('dashboard.employee.edit', compact('employee', 'offices', 'teamLeaders', 'departments'));
    // }


    // public function edit(Request $request, User $employee)
    // {
    //     $loggedInUser = $request->user();

    //     if ($loggedInUser->hasRole('super_admin')) {
    //         $offices = Office::query()
    //             ->select('id', 'name', 'owner_id')
    //             ->orderBy('name')
    //             ->get();

    //         $allowedOfficeIds = $offices->pluck('id');

    //     } elseif ($loggedInUser->hasRole('owner')) {
    //         $ownerOfficeIds = Office::query()
    //             ->where('owner_id', $loggedInUser->id)
    //             ->pluck('id')
    //             ->map(fn ($id) => (int) $id);

    //         if ($ownerOfficeIds->isEmpty()) {
    //             return back()->with('error', 'No office found for this owner.');
    //         }

    //         if (!$ownerOfficeIds->contains((int) $employee->office_id)) {
    //             abort(403, 'This employee does not belong to your office.');
    //         }

    //         $offices = Office::query()
    //             ->select('id', 'name', 'owner_id')
    //             ->whereIn('id', $ownerOfficeIds)
    //             ->orderBy('name')
    //             ->get();

    //         $allowedOfficeIds = $ownerOfficeIds;

    //     } else {
    //         $activeOfficeId = (int) $loggedInUser->activeOfficeId();

    //         if (!$activeOfficeId) {
    //             return back()->with(
    //                 'error',
    //                 'Please select an office first.'
    //             );
    //         }

    //         if ((int) $employee->office_id !== $activeOfficeId) {
    //             abort(
    //                 403,
    //                 'This employee does not belong to the selected office.'
    //             );
    //         }

    //         $offices = Office::query()
    //             ->select('id', 'name', 'owner_id')
    //             ->whereKey($activeOfficeId)
    //             ->get();

    //         $allowedOfficeIds = collect([$activeOfficeId]);
    //     }

    //     $teamLeaders = User::query()
    //         ->select('id', 'name', 'office_id')
    //         ->where('status', 1)
    //         ->whereIn('office_id', $allowedOfficeIds)
    //         ->whereKeyNot($employee->id)
    //         ->role('team_leader')
    //         ->with('office:id,name')
    //         ->orderBy('name')
    //         ->get();

    //     $leaveAuthorities = User::query()
    //         ->select('id', 'name', 'office_id', 'status')
    //         ->where('status', 1)
    //         ->whereIn('office_id', $allowedOfficeIds)
    //         ->whereKeyNot($employee->id)
    //         ->whereHas('roles', function ($roleQuery) {
    //             $roleQuery->whereIn('name', [
    //                 'owner',
    //                 'admin',
    //                 'team_leader',
    //                 'super_admin',
    //             ]);
    //         })
    //         ->with('office:id,name')
    //         ->orderBy('name')
    //         ->get();

    //     if ($loggedInUser->hasRole('super_admin')) {
    //         $superAdmins = User::query()
    //             ->select('id', 'name', 'office_id', 'status')
    //             ->where('status', 1)
    //             ->whereKeyNot($employee->id)
    //             ->whereHas('roles', function ($roleQuery) {
    //                 $roleQuery->where('name', 'super_admin');
    //             })
    //             ->with('office:id,name')
    //             ->get();

    //         $leaveAuthorities = $leaveAuthorities
    //             ->merge($superAdmins)
    //             ->unique('id')
    //             ->sortBy('name')
    //             ->values();
    //     }

    //     $departments = Department::query()
    //         ->select('id', 'name')
    //         ->orderBy('name')
    //         ->get();

    //     $employee->load([
    //         'office:id,name',
    //         'department:id,name',
    //         'teamLeader:id,name',
    //         'leaveAuthority:id,name',
    //         'userSalary',
    //     ]);

    //     return view('dashboard.employee.edit', compact(
    //         'employee',
    //         'offices',
    //         'teamLeaders',
    //         'leaveAuthorities',
    //         'departments'
    //     ));
    // }


    public function edit(Request $request, User $employee)
    {
        $loggedInUser = $request->user();

        if ($loggedInUser->hasRole('super_admin')) {
            $offices = Office::query()
                ->select('id', 'name', 'owner_id')
                ->orderBy('name')
                ->get();

            $allowedOfficeIds = $offices
                ->pluck('id')
                ->map(fn ($id) => (int) $id);

        } elseif ($loggedInUser->hasRole('owner')) {
            $ownerOfficeIds = Office::query()
                ->where('owner_id', $loggedInUser->id)
                ->pluck('id')
                ->map(fn ($id) => (int) $id);

            if ($ownerOfficeIds->isEmpty()) {
                return back()->with(
                    'error',
                    'No office found for this owner.'
                );
            }

            if (!$ownerOfficeIds->contains((int) $employee->office_id)) {
                abort(
                    403,
                    'This employee does not belong to your office.'
                );
            }

            $offices = Office::query()
                ->select('id', 'name', 'owner_id')
                ->whereIn('id', $ownerOfficeIds)
                ->orderBy('name')
                ->get();

            $allowedOfficeIds = $ownerOfficeIds;

        } else {
            $activeOfficeId = (int) $loggedInUser->activeOfficeId();

            if (!$activeOfficeId) {
                return back()->with(
                    'error',
                    'Please select an office first.'
                );
            }

            if ((int) $employee->office_id !== $activeOfficeId) {
                abort(
                    403,
                    'This employee does not belong to the selected office.'
                );
            }

            $offices = Office::query()
                ->select('id', 'name', 'owner_id')
                ->whereKey($activeOfficeId)
                ->get();

            $allowedOfficeIds = collect([$activeOfficeId]);
        }

        /*
        |--------------------------------------------------------------------------
        | Reporting managers
        |--------------------------------------------------------------------------
        |
        | Team Leader, Admin और Owner तीनों आएंगे।
        | Status check नहीं होगा।
        |
        */

        $teamLeaders = User::query()
            ->select([
                'users.id',
                'users.name',
                'users.office_id',
                'users.status',
            ])
            ->whereIn('users.office_id', $allowedOfficeIds)
            ->whereKeyNot($employee->id)
            ->whereHas('roles', function ($query) {
                $query->whereIn('roles.name', [
                    'team_leader',
                    'admin',
                    'owner',
                ]);
            })
            ->with([
                'office:id,name',
                'roles:id,name',
            ])
            ->orderBy('users.name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Leave authorities
        |--------------------------------------------------------------------------
        |
        | Create form की तरह वही list Leave Authority में भी जाएगी।
        |
        */

        $leaveAuthorities = $teamLeaders;

        $departments = Department::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $employee->load([
            'office:id,name',
            'department:id,name',
            'teamLeader:id,name',
            'leaveAuthority:id,name',
            'userSalary',
        ]);

        return view('dashboard.employee.edit', compact(
            'employee',
            'offices',
            'teamLeaders',
            'leaveAuthorities',
            'departments'
        ));
    }


    // public function update(Request $request, User $employee)
    // {
    //     $user = $request->user();
    //     // =========================
    //     // TARGET OFFICE RESOLUTION
    //     // =========================
    //     if ($user->hasRole('super_admin')) {

    //         $targetOfficeId = $request->office_id ?: $employee->office_id;

    //         if (!$targetOfficeId) {
    //             return back()->with('error', 'Please select an office first.')->withInput();
    //         }

    //     } elseif ($user->hasRole('owner')) {

    //         $ownerOfficeIds = Office::where('owner_id', $user->id)->pluck('id');

    //         if ($ownerOfficeIds->isEmpty()) {
    //             return back()->with('error', 'No office found for this owner.')->withInput();
    //         }

    //         // security: employee owner ke kisi office ka hi hona chahiye
    //         if (!$ownerOfficeIds->contains($employee->office_id)) {
    //             abort(403, 'This employee does not belong to your office.');
    //         }

    //         // owner dropdown se office change kar sakta hai
    //         $targetOfficeId = $request->office_id ?: $employee->office_id;

    //         if (!$ownerOfficeIds->contains($targetOfficeId)) {
    //             return back()->with('error', 'Invalid office selected.')->withInput();
    //         }

    //     } else {

    //         $targetOfficeId = $user->activeOfficeId();

    //         if (!$targetOfficeId) {
    //             return back()->with('error', 'Please select an office first.')->withInput();
    //         }

    //         if ((int) $employee->office_id !== (int) $targetOfficeId) {
    //             abort(403, 'This employee does not belong to the selected office.');
    //         }
    //     }

    //     // =========================
    //     // OPTIONAL EMAIL UNIQUE CHECK
    //     // =========================
    //     if ($request->filled('email')) {
    //         $emailExists = User::where('email', $request->email)
    //             ->where('id', '!=', $employee->id)
    //             ->exists();

    //         if ($emailExists) {
    //             return back()->withErrors(['email' => 'Email already exists.'])->withInput();
    //         }
    //     }

    //     // =========================
    //     // UPDATE BASIC FIELDS
    //     // =========================
    //     $employee->fill($request->except([
    //         'password',
    //         'photo',
    //         'aadhar_attachment',
    //         'pan_attachment',
    //         'other_attachment',
    //         'joining_date',
    //         'office_id',
    //         'role',
    //         'basic_salary',
    //         'house_rent_allowance',
    //         'transport_allowance',
    //         'medical_allowance',
    //         'special_allowance',
    //         'dearness_allowance',
    //         'relieving_charge',
    //         'additional_allowance',
    //         'provident_fund',
    //         'employee_state_insurance_corporation',
    //     ]));

    //     if ($request->filled('password')) {
    //         $employee->password = Hash::make($request->password);
    //     }

    //     // =========================
    //     // FILE UPLOADS
    //     // =========================
    //     if ($request->file('photo')) {
    //         if ($employee->photo) {
    //             $oldFile = public_path('storage/' . $employee->photo);
    //             if (file_exists($oldFile)) {
    //                 unlink($oldFile);
    //             }
    //         }

    //         $file = $request->file('photo')->store('public/photos');
    //         $employee->photo = str_replace('public/', '', $file);
    //     }

    //     if ($request->file('aadhar_attachment')) {
    //         if ($employee->aadhar_attachment) {
    //             $oldFile = public_path('storage/' . $employee->aadhar_attachment);
    //             if (file_exists($oldFile)) {
    //                 unlink($oldFile);
    //             }
    //         }

    //         $file = $request->file('aadhar_attachment')->store('public/aadhar_attachments');
    //         $employee->aadhar_attachment = str_replace('public/', '', $file);
    //     }

    //     if ($request->file('pan_attachment')) {
    //         if ($employee->pan_attachment) {
    //             $oldFile = public_path('storage/' . $employee->pan_attachment);
    //             if (file_exists($oldFile)) {
    //                 unlink($oldFile);
    //             }
    //         }

    //         $file = $request->file('pan_attachment')->store('public/pan_attachments');
    //         $employee->pan_attachment = str_replace('public/', '', $file);
    //     }

    //     if ($request->file('other_attachment')) {
    //         if ($employee->other_attachment) {
    //             $oldFile = public_path('storage/' . $employee->other_attachment);
    //             if (file_exists($oldFile)) {
    //                 unlink($oldFile);
    //             }
    //         }

    //         $file = $request->file('other_attachment')->store('public/other_attachments');
    //         $employee->other_attachment = str_replace('public/', '', $file);
    //     }

    //     // =========================
    //     // EXTRA FIELDS
    //     // =========================
    //     $employee->joining_date = $request->joining_date;
    //     $employee->office_id = $targetOfficeId;

    //     if ($request->filled('check_in_time') && $request->filled('check_out_time')) {
    //         $checkInTime = Carbon::parse($request->check_in_time);
    //         $checkOutTime = Carbon::parse($request->check_out_time);
    //         $employee->office_time = $checkInTime->diffInMinutes($checkOutTime);
    //     }

    //     if ($request->role) {
    //         $employee->syncRoles([$request->role]);
    //     }

    //     $employee->save();

    //     // =========================
    //     // SALARY DATA
    //     // =========================
    //     $basicSalary = $request->basic_salary ?? 0;
    //     $houseRentAllowance = $request->house_rent_allowance ?? 0;
    //     $transportAllowance = $request->transport_allowance ?? 0;
    //     $medicalAllowance = $request->medical_allowance ?? 0;
    //     $specialAllowance = $request->special_allowance ?? 0;
    //     $dearnessAllowance = $request->dearness_allowance ?? 0;
    //     $relievingCharge = $request->relieving_charge ?? 0;
    //     $additionalAllowance = $request->additional_allowance ?? 0;
    //     $providentFund = $request->provident_fund ?? 0;
    //     $esic = $request->employee_state_insurance_corporation ?? 0;

    //     $totalSalary = $basicSalary
    //         + $houseRentAllowance
    //         + $transportAllowance
    //         + $medicalAllowance
    //         + $specialAllowance
    //         + $dearnessAllowance
    //         + $relievingCharge
    //         + $additionalAllowance;

    //     $userSalary = UserSalary::where('user_id', $employee->id)->first();

    //     if ($userSalary) {
    //         $userSalary->update([
    //             'basic_salary' => $basicSalary,
    //             'house_rent_allowance' => $houseRentAllowance,
    //             'transport_allowance' => $transportAllowance,
    //             'medical_allowance' => $medicalAllowance,
    //             'special_allowance' => $specialAllowance,
    //             'dearness_allowance' => $dearnessAllowance,
    //             'relieving_charge' => $relievingCharge,
    //             'additional_allowance' => $additionalAllowance,
    //             'provident_fund' => $providentFund,
    //             'employee_state_insurance_corporation' => $esic,
    //             'total_salary' => $totalSalary,
    //         ]);
    //     } else {
    //         UserSalary::create([
    //             'user_id' => $employee->id,
    //             'basic_salary' => $basicSalary,
    //             'house_rent_allowance' => $houseRentAllowance,
    //             'transport_allowance' => $transportAllowance,
    //             'medical_allowance' => $medicalAllowance,
    //             'special_allowance' => $specialAllowance,
    //             'dearness_allowance' => $dearnessAllowance,
    //             'relieving_charge' => $relievingCharge,
    //             'additional_allowance' => $additionalAllowance,
    //             'provident_fund' => $providentFund,
    //             'employee_state_insurance_corporation' => $esic,
    //             'total_salary' => $totalSalary,
    //         ]);
    //     }

    //     return redirect('employee')->with('success', 'Record Updated successfully');
    // }

public function update(Request $request, User $employee)
{
    $loggedInUser = $request->user();

    /*
    |--------------------------------------------------------------------------
    | Target office resolution and authorization
    |--------------------------------------------------------------------------
    */

    if ($loggedInUser->hasRole('super_admin')) {
        $targetOfficeId = $request->input('office_id', $employee->office_id);

        if (!$targetOfficeId) {
            return back()
                ->with('error', 'Please select an office first.')
                ->withInput();
        }

        if (!Office::whereKey($targetOfficeId)->exists()) {
            return back()
                ->withErrors([
                    'office_id' => 'The selected office is invalid.',
                ])
                ->withInput();
        }
    } elseif ($loggedInUser->hasRole('owner')) {
        $ownerOfficeIds = Office::query()
            ->where('owner_id', $loggedInUser->id)
            ->pluck('id');

        if ($ownerOfficeIds->isEmpty()) {
            return back()
                ->with('error', 'No office found for this owner.')
                ->withInput();
        }

        if (!$ownerOfficeIds->contains((int) $employee->office_id)) {
            abort(403, 'This employee does not belong to your office.');
        }

        $targetOfficeId = (int) $request->input(
            'office_id',
            $employee->office_id
        );

        if (!$ownerOfficeIds->contains($targetOfficeId)) {
            return back()
                ->withErrors([
                    'office_id' => 'Invalid office selected.',
                ])
                ->withInput();
        }
    } else {
        $targetOfficeId = $loggedInUser->activeOfficeId();

        if (!$targetOfficeId) {
            return back()
                ->with('error', 'Please select an office first.')
                ->withInput();
        }

        if ((int) $employee->office_id !== (int) $targetOfficeId) {
            abort(
                403,
                'This employee does not belong to the selected office.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Normalize Aadhaar and PAN before validation
    |--------------------------------------------------------------------------
    */

    if ($request->filled('adhar_number')) {
        $request->merge([
            'adhar_number' => preg_replace(
                '/\D/',
                '',
                (string) $request->adhar_number
            ),
        ]);
    }

    if ($request->filled('pan_number')) {
        $request->merge([
            'pan_number' => strtoupper(
                trim((string) $request->pan_number)
            ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    $validated = $request->validate(
        [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($employee->id),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'dob' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'joining_date' => [
                'nullable',
                'date',
            ],

            'address' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'department_id' => [
                'nullable',
                'integer',
                'exists:departments,id',
            ],

            'designation' => [
                'nullable',
                'string',
                'max:255',
            ],

            'office_id' => [
                'nullable',
                'integer',
                'exists:offices,id',
            ],

            'team_leader_id' => [
                'nullable',
                'integer',
                'exists:users,id',
                Rule::notIn([$employee->id]),
            ],

            'role' => [
                'nullable',
                Rule::in([
                    'admin',
                    'team_leader',
                    'employee',
                ]),
            ],

            'status' => [
                'required',
                Rule::in([
                    0,
                    1,
                    '0',
                    '1',
                ]),
            ],

            'salary' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'responsibility' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'check_in_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'check_out_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'break' => [
                'nullable',
                'integer',
                'min:0',
                'max:1440',
            ],

            'location_required' => [
                'nullable',
                Rule::in(['yes', 'no']),
            ],

            'password' => [
                'nullable',
                'string',
                'min:8',
            ],

            'confirm_password' => [
                'nullable',
                'same:password',
            ],

            'employee_id' => [
                'nullable',
                'string',
                'max:100',
            ],

            'uan_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'esic_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'account_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'adhar_number' => [
                'nullable',
                'digits:12',
            ],

            'pan_number' => [
                'nullable',
                'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'aadhar_attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120',
            ],

            'pan_attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120',
            ],

            'other_attachment' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf,doc,docx',
                'max:5120',
            ],

            'basic_salary' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'house_rent_allowance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'transport_allowance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'medical_allowance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'special_allowance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'dearness_allowance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'relieving_charge' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'additional_allowance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'provident_fund' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],

            'employee_state_insurance_corporation' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
        ],
        [
            'email.unique' =>
                'This email address is already being used by another employee.',

            'team_leader_id.not_in' =>
                'An employee cannot be assigned as their own team leader.',

            'adhar_number.digits' =>
                'Aadhaar number must contain exactly 12 digits.',

            'pan_number.regex' =>
                'Please enter a valid PAN number, for example ABCDE1234F.',

            'confirm_password.same' =>
                'Password and confirm password do not match.',

            'photo.max' =>
                'Employee photo must not be larger than 5 MB.',

            'aadhar_attachment.mimes' =>
                'Aadhaar attachment must be JPG, JPEG, PNG, WEBP or PDF.',

            'aadhar_attachment.max' =>
                'Aadhaar attachment must not be larger than 5 MB.',

            'pan_attachment.mimes' =>
                'PAN attachment must be JPG, JPEG, PNG, WEBP or PDF.',

            'pan_attachment.max' =>
                'PAN attachment must not be larger than 5 MB.',

            'other_attachment.max' =>
                'Other attachment must not be larger than 5 MB.',
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | Check reporting employee belongs to target office
    |--------------------------------------------------------------------------
    */

    if (!empty($validated['team_leader_id'])) {
        $validTeamLeader = User::query()
            ->whereKey($validated['team_leader_id'])
            ->where('office_id', $targetOfficeId)
            ->exists();

        if (!$validTeamLeader) {
            return back()
                ->withErrors([
                    'team_leader_id' =>
                        'The selected team leader does not belong to the selected office.',
                ])
                ->withInput();
        }
    }

    $newUploadedFiles = [];

    try {
        DB::beginTransaction();

        /*
        |--------------------------------------------------------------------------
        | Basic employee details
        |--------------------------------------------------------------------------
        */

        $employee->name = $validated['name'];
        $employee->email = $validated['email'] ?? null;
        $employee->phone = $validated['phone'] ?? null;
        $employee->dob = $validated['dob'] ?? null;
        $employee->joining_date = $validated['joining_date'] ?? null;
        $employee->address = $validated['address'] ?? null;
        $employee->department_id = $validated['department_id'] ?? null;
        $employee->designation = $validated['designation'] ?? null;
        $employee->office_id = $targetOfficeId;
        $employee->team_leader_id =
            $validated['team_leader_id'] ?? null;
        // $employee->status = (int) ($validated['status'] ?? $employee->status);
        $employee->status =  $validated['status'];
        $employee->salary = $validated['salary'] ?? null;
        $employee->responsibility =
            $validated['responsibility'] ?? null;
        $employee->location_required =
            $validated['location_required'] ?? 'no';

        $employee->check_in_time =
            $validated['check_in_time'] ?? null;
        $employee->check_out_time =
            $validated['check_out_time'] ?? null;

        if (array_key_exists('break', $validated)) {
            $employee->break = $validated['break'];
        }

        $employee->employee_id =
            $validated['employee_id'] ?? null;
        $employee->uan_number =
            $validated['uan_number'] ?? null;
        $employee->esic_number =
            $validated['esic_number'] ?? null;
        $employee->account_number =
            $validated['account_number'] ?? null;

        $employee->adhar_number =
            $validated['adhar_number'] ?? null;
        $employee->pan_number =
            $validated['pan_number'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['password'])) {
            $employee->password = Hash::make(
                $validated['password']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate office duration
        |--------------------------------------------------------------------------
        */

        if (
            !empty($validated['check_in_time']) &&
            !empty($validated['check_out_time'])
        ) {
            $checkInTime = Carbon::createFromFormat(
                'H:i',
                $validated['check_in_time']
            );

            $checkOutTime = Carbon::createFromFormat(
                'H:i',
                $validated['check_out_time']
            );

            /*
             * Night shift support:
             * Example: check-in 10 PM and check-out 6 AM.
             */
            if ($checkOutTime->lessThanOrEqualTo($checkInTime)) {
                $checkOutTime->addDay();
            }

            $employee->office_time =
                $checkInTime->diffInMinutes($checkOutTime);
        } else {
            $employee->office_time = null;
        }

        /*
        |--------------------------------------------------------------------------
        | File uploads
        |--------------------------------------------------------------------------
        */

        $fileUploadMap = [
            'photo' => [
                'directory' => 'photos',
                'column' => 'photo',
            ],

            'aadhar_attachment' => [
                'directory' => 'aadhar_attachments',
                'column' => 'aadhar_attachment',
            ],

            'pan_attachment' => [
                'directory' => 'pan_attachments',
                'column' => 'pan_attachment',
            ],

            'other_attachment' => [
                'directory' => 'other_attachments',
                'column' => 'other_attachment',
            ],
        ];

        foreach ($fileUploadMap as $inputName => $fileConfig) {
            if (!$request->hasFile($inputName)) {
                continue;
            }

            $uploadedFile = $request->file($inputName);

            if (!$uploadedFile->isValid()) {
                throw new \RuntimeException(
                    "The {$inputName} upload failed."
                );
            }

            $oldFilePath = $employee->{$fileConfig['column']};

            $newFilePath = $uploadedFile->store(
                $fileConfig['directory'],
                'public'
            );

            if (!$newFilePath) {
                throw new \RuntimeException(
                    "Unable to upload {$inputName}."
                );
            }

            $newUploadedFiles[] = $newFilePath;

            $employee->{$fileConfig['column']} = $newFilePath;

            /*
             * Old file will be deleted only after the new upload succeeds.
             */
            if (
                !empty($oldFilePath) &&
                $oldFilePath !== $newFilePath &&
                Storage::disk('public')->exists($oldFilePath)
            ) {
                Storage::disk('public')->delete($oldFilePath);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Save employee and role
        |--------------------------------------------------------------------------
        */

        $employee->save();

        if (!empty($validated['role'])) {
            $employee->syncRoles([
                $validated['role'],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Salary details
        |--------------------------------------------------------------------------
        */

        $basicSalary = (float) ($validated['basic_salary'] ?? 0);
        $houseRentAllowance =
            (float) ($validated['house_rent_allowance'] ?? 0);
        $transportAllowance =
            (float) ($validated['transport_allowance'] ?? 0);
        $medicalAllowance =
            (float) ($validated['medical_allowance'] ?? 0);
        $specialAllowance =
            (float) ($validated['special_allowance'] ?? 0);
        $dearnessAllowance =
            (float) ($validated['dearness_allowance'] ?? 0);
        $relievingCharge =
            (float) ($validated['relieving_charge'] ?? 0);
        $additionalAllowance =
            (float) ($validated['additional_allowance'] ?? 0);
        $providentFund =
            (float) ($validated['provident_fund'] ?? 0);
        $esic =
            (float) (
                $validated[
                    'employee_state_insurance_corporation'
                ] ?? 0
            );

        $totalSalary =
            $basicSalary +
            $houseRentAllowance +
            $transportAllowance +
            $medicalAllowance +
            $specialAllowance +
            $dearnessAllowance +
            $relievingCharge +
            $additionalAllowance;

        UserSalary::updateOrCreate(
            [
                'user_id' => $employee->id,
            ],
            [
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
            ]
        );

        DB::commit();

        return redirect()
            ->route('employee.index')
            ->with(
                'success',
                'Employee record updated successfully.'
            );
    } catch (\Throwable $exception) {
        DB::rollBack();

        /*
         * If database update fails, delete only newly uploaded files.
         */
        foreach ($newUploadedFiles as $newUploadedFile) {
            if (Storage::disk('public')->exists($newUploadedFile)) {
                Storage::disk('public')->delete($newUploadedFile);
            }
        }

        report($exception);

        return back()
            ->with(
                'error',
                'Employee record could not be updated. Please try again.'
            )
            ->withInput();
    }
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


    // public function employeeAttendance(Request $request)
    // {
    //     $activeOfficeId = $request->user()->activeOfficeId();

    //     if (!$activeOfficeId) {
    //         $employees = collect();
    //     } else {
    //         $employees = User::where('office_id', $activeOfficeId)
    //             ->orderBy('name')
    //             ->get();
    //     }

    //     return view('dashboard.employee.list', compact('employees'));
    // }

    public function employeeAttendance(Request $request)
{
    $activeOfficeId = $request->user()?->activeOfficeId();

    if (!$activeOfficeId) {
        $employees = collect();
    } else {
        $employees = User::query()
            ->where('office_id', $activeOfficeId)
            ->where('status', '1') // केवल active employees
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
