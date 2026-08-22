<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\EmployeeAddress;
use App\Models\EmployeeFamilyDetail;
use App\Models\EmployeeNominee;
use App\Models\Office;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserSalary;
use App\Models\EmployeeEducationalQualification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\EmployeeFamilyMember;
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


private function employeeExtraProfileRules(): array
{
    return [
        // Structured address
        'premise_details' => ['nullable', 'string', 'max:255'],
        'street_road' => ['nullable', 'string', 'max:255'],
        'locality_area' => ['nullable', 'string', 'max:255'],
        'landmark' => ['nullable', 'string', 'max:255'],
        'city' => ['nullable', 'string', 'max:150'],
        'district' => ['nullable', 'string', 'max:150'],
        'state' => ['nullable', 'string', 'max:150'],
        'pin_code' => ['nullable', 'regex:/^[1-9][0-9]{5}$/'],

        // Marital / spouse details
        'marital_status' => [
            'required',
            Rule::in([
                'single',
                'married',
                'divorced',
                'widowed',
                'separated',
            ]),
        ],
        'spouse_name' => ['nullable', 'required_if:marital_status,married', 'string', 'max:255'],
        'spouse_phone' => ['nullable', 'string', 'max:20'],
        'spouse_dob' => ['nullable', 'date', 'before_or_equal:today'],
        'spouse_occupation' => ['nullable', 'string', 'max:255'],

        // Nominee details
        'has_nominee' => [
            'required',
            Rule::in(['yes', 'no']),
        ],
        'nominee_name' => ['nullable', 'required_if:has_nominee,yes', 'string', 'max:255'],
        'nominee_relationship' => ['nullable', 'required_if:has_nominee,yes', 'string', 'max:100'],
        'nominee_phone' => ['nullable', 'string', 'max:20'],
        'nominee_dob' => ['nullable', 'date', 'before_or_equal:today'],
        'nominee_aadhaar_number' => ['nullable', 'digits:12'],
        'nominee_address' => ['nullable', 'string', 'max:2000'],
        'nominee_bank_name' => [
            'nullable',
            'string',
            'max:255',
        ],

        'nominee_account_holder_name' => [
            'nullable',
            'string',
            'max:255',
        ],

        'nominee_account_number' => [
            'nullable',
            'string',
            'max:30',
        ],

        'nominee_ifsc_code' => [
            'nullable',
            'string',
            'max:20',
        ],

        'nominee_branch_name' => [
            'nullable',
            'string',
            'max:255',
        ],
    ];
}

private function cleanNullableString($value): ?string
{
    if ($value === null) {
        return null;
    }

    $value = trim((string) $value);

    return $value === '' ? null : $value;
}

private function employeeAddressPayload(array $validated): array
{
    return [
        'premise_details' => $this->cleanNullableString(
            $validated['premise_details'] ?? null
        ),
        'street_road' => $this->cleanNullableString(
            $validated['street_road'] ?? null
        ),
        'locality_area' => $this->cleanNullableString(
            $validated['locality_area'] ?? null
        ),
        'landmark' => $this->cleanNullableString(
            $validated['landmark'] ?? null
        ),
        'city' => $this->cleanNullableString(
            $validated['city'] ?? null
        ),
        'district' => $this->cleanNullableString(
            $validated['district'] ?? null
        ),
        'state' => $this->cleanNullableString(
            $validated['state'] ?? null
        ),
        'pin_code' => $this->cleanNullableString(
            $validated['pin_code'] ?? null
        ),
    ];
}

private function hasAnyEmployeeAddressValue(array $address): bool
{
    foreach ($address as $value) {
        if ($value !== null && $value !== '') {
            return true;
        }
    }

    return false;
}

private function formattedEmployeeAddress(array $address): ?string
{
    $parts = [];

    foreach ([
        'premise_details',
        'street_road',
        'locality_area',
        'landmark',
        'city',
        'district',
        'state',
    ] as $key) {
        if (!empty($address[$key])) {
            $parts[] = trim((string) $address[$key]);
        }
    }

    $formatted = implode(', ', $parts);

    if (!empty($address['pin_code'])) {
        $formatted .= ($formatted !== '' ? ' - ' : '') . $address['pin_code'];
    }

    return $formatted !== '' ? $formatted : null;
}


    private function saveEmployeeExtraProfile(
        User $employee,
        array $validated
    ): void {

        $address = $this->employeeAddressPayload($validated);

        if ($this->hasAnyEmployeeAddressValue($address)) {
            EmployeeAddress::updateOrCreate(
                ['user_id' => $employee->id],
                $address
            );
        } else {
            EmployeeAddress::query()
                ->where('user_id', $employee->id)
                ->delete();
        }

        /*
        |--------------------------------------------------------------------------
        | Marital / spouse details
        |--------------------------------------------------------------------------
        */
        $maritalStatus = (string) ($validated['marital_status'] ?? 'single');

        EmployeeFamilyDetail::updateOrCreate(
            ['user_id' => $employee->id],
            [
                'marital_status' => $maritalStatus,

                'spouse_name' => $maritalStatus === 'married'
                    ? $this->cleanNullableString(
                        $validated['spouse_name'] ?? null
                    )
                    : null,

                'spouse_phone' => $maritalStatus === 'married'
                    ? $this->cleanNullableString(
                        $validated['spouse_phone'] ?? null
                    )
                    : null,

                'spouse_dob' => $maritalStatus === 'married'
                    ? ($validated['spouse_dob'] ?? null)
                    : null,

                'spouse_occupation' => $maritalStatus === 'married'
                    ? $this->cleanNullableString(
                        $validated['spouse_occupation'] ?? null
                    )
                    : null,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Nominee details
        |--------------------------------------------------------------------------
        */
        if (($validated['has_nominee'] ?? 'no') === 'yes') {
            /*
            |--------------------------------------------------------------------------
            | Nominee + nominee bank details
            |--------------------------------------------------------------------------
            |
            | firstOrNew + forceFill is intentionally used so newly added nominee bank
            | columns are saved even if EmployeeNominee::$fillable has not yet been
            | updated. This same helper is used by both store() and update().
            |
            */
            $employeeNominee = EmployeeNominee::query()->firstOrNew([
                'user_id' => $employee->id,
            ]);

            $employeeNominee->forceFill([
                'user_id' => $employee->id,

                'name' => $this->cleanNullableString(
                    $validated['nominee_name'] ?? null
                ),

                'relationship' => $this->cleanNullableString(
                    $validated['nominee_relationship'] ?? null
                ),

                'phone' => $this->cleanNullableString(
                    $validated['nominee_phone'] ?? null
                ),

                'dob' => $validated['nominee_dob'] ?? null,

                'aadhaar_number' => !empty($validated['nominee_aadhaar_number'])
                    ? preg_replace(
                        '/\D+/',
                        '',
                        (string) $validated['nominee_aadhaar_number']
                    )
                    : null,

                'address' => $this->cleanNullableString(
                    $validated['nominee_address'] ?? null
                ),

                /* Nominee bank details */
                'bank_name' => $this->cleanNullableString(
                    $validated['nominee_bank_name'] ?? null
                ),

                'account_holder_name' => $this->cleanNullableString(
                    $validated['nominee_account_holder_name'] ?? null
                ),

                'account_number' => $this->cleanNullableString(
                    $validated['nominee_account_number'] ?? null
                ),

                'ifsc_code' => !empty($validated['nominee_ifsc_code'])
                    ? strtoupper(trim((string) $validated['nominee_ifsc_code']))
                    : null,

                'branch_name' => $this->cleanNullableString(
                    $validated['nominee_branch_name'] ?? null
                ),
            ]);

            $employeeNominee->save();
        } else {
            EmployeeNominee::query()
                ->where('user_id', $employee->id)
                ->delete();
        }
    }



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


    public function create(Request $request)
    {
        $loggedInUser = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Resolve accessible offices
        |--------------------------------------------------------------------------
        */

        if ($loggedInUser->hasRole('super_admin')) {
            $offices = Office::query()
                ->select([
                    'id',
                    'name',
                    'owner_id',
                    'employee_prefix',
                    'employee_sequence',
                ])
                ->orderBy('name')
                ->get();

            $allowedOfficeIds = $offices
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values();

        } elseif ($loggedInUser->hasRole('owner')) {
            $offices = Office::query()
                ->select([
                    'id',
                    'name',
                    'owner_id',
                    'employee_prefix',
                    'employee_sequence',
                ])
                ->where('owner_id', $loggedInUser->id)
                ->orderBy('name')
                ->get();

            if ($offices->isEmpty()) {
                return back()->with(
                    'error',
                    'No office found for this owner.'
                );
            }

            $allowedOfficeIds = $offices
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values();

            $plan = Plan::query()
                ->where('user_id', $loggedInUser->id)
                ->latest('id')
                ->first();

            $employeeCount = User::query()
                ->whereIn('office_id', $allowedOfficeIds)
                ->count();

            if (
                $plan &&
                $employeeCount >= (int) $plan->number_of_employees
            ) {
                return back()->with(
                    'error',
                    'Your employee creation limit exceeded!'
                );
            }

        } else {
            $activeOfficeId = (int) $loggedInUser->activeOfficeId();

            if (!$activeOfficeId) {
                return back()->with(
                    'error',
                    'Please select an office first.'
                );
            }

            $office = Office::query()
                ->select([
                    'id',
                    'name',
                    'owner_id',
                    'employee_prefix',
                    'employee_sequence',
                ])
                ->with('owner:id,name')
                ->find($activeOfficeId);

            if (!$office) {
                return back()->with(
                    'error',
                    'Selected office was not found.'
                );
            }

            $offices = collect([$office]);

            $allowedOfficeIds = collect([
                (int) $office->id,
            ]);

            if ($office->owner) {
                $plan = Plan::query()
                    ->where('user_id', $office->owner->id)
                    ->latest('id')
                    ->first();

                $employeeCount = User::query()
                    ->where('office_id', $office->id)
                    ->count();

                if (
                    $plan &&
                    $employeeCount >= (int) $plan->number_of_employees
                ) {
                    return back()->with(
                        'error',
                        'Your employee creation limit exceeded!'
                    );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Office owners
        |--------------------------------------------------------------------------
        */

        $officeOwnerIds = $offices
            ->pluck('owner_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Reporting Managers and Leave Authorities
        |--------------------------------------------------------------------------
        |
        | Same $teamLeaders variable is used for both dropdowns.
        |
        | Selected office candidates:
        | 1. Owner of that office
        | 2. Active admins of that office
        | 3. Active team leaders of that office
        |
        */

        $teamLeaders = User::query()
            ->select([
                'users.id',
                'users.name',
                'users.office_id',
                'users.status',
            ])
            ->where('users.status', '1')
            ->where(function ($userQuery) use (
                $allowedOfficeIds,
                $officeOwnerIds
            ) {
                /*
                * Office Admins and Team Leaders
                */
                $userQuery->where(function ($officeUserQuery) use (
                    $allowedOfficeIds
                ) {
                    $officeUserQuery
                        ->whereIn(
                            'users.office_id',
                            $allowedOfficeIds
                        )
                        ->whereHas('roles', function ($roleQuery) {
                            $roleQuery->whereIn('roles.name', [
                                'admin',
                                'team_leader',
                            ]);
                        });
                });

                /*
                * Office Owners
                */
                if ($officeOwnerIds->isNotEmpty()) {
                    $userQuery->orWhere(function ($ownerQuery) use (
                        $officeOwnerIds
                    ) {
                        $ownerQuery
                            ->whereIn(
                                'users.id',
                                $officeOwnerIds
                            )
                            ->whereHas('roles', function ($roleQuery) {
                                $roleQuery->where(
                                    'roles.name',
                                    'owner'
                                );
                            });
                    });
                }
            })
            ->with([
                'office:id,name',
                'roles:id,name',
            ])
            ->orderBy('users.name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Attach selectable office IDs
        |--------------------------------------------------------------------------
        |
        | Admin/team leader: users.office_id
        | Owner: offices.owner_id ke according office IDs
        |
        */

        $ownerOfficeMap = $offices
            ->filter(fn ($office) => !empty($office->owner_id))
            ->groupBy(fn ($office) => (int) $office->owner_id)
            ->map(function ($ownerOffices) {
                return $ownerOffices
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values()
                    ->all();
            });

        $teamLeaders->each(function ($manager) use ($ownerOfficeMap) {
            if ($manager->hasRole('owner')) {
                $manager->selectable_office_ids = $ownerOfficeMap->get(
                    (int) $manager->id,
                    []
                );
            } else {
                $manager->selectable_office_ids = $manager->office_id
                    ? [(int) $manager->office_id]
                    : [];
            }
        });

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
            |------------------------------------------------------------------
            | Use the actually selected/active office for the ID preview
            |------------------------------------------------------------------
            */

            $preferredOfficeId = (int) old(
                'office_id',
                $loggedInUser->activeOfficeId()
            );

            $defaultOffice = $preferredOfficeId
                ? $offices->firstWhere('id', $preferredOfficeId)
                : null;

            $defaultOffice = $defaultOffice ?: $offices->first();

            $nextEmployeeId = null;

            if ($defaultOffice) {
                $nextEmployeeId = $this->generateEmployeeId(
                    $defaultOffice
                );
            }

        // return view(
        //     'dashboard.employee.create',
        //     compact(
        //         'offices',
        //         'teamLeaders',
        //         'departments'
        //     )
        // );
        return view(
            'dashboard.employee.create',
            compact(
                'offices',
                'teamLeaders',
                'departments',
                'nextEmployeeId'
            )
        );
    }



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

//         if (
//             !$targetOfficeId ||
//             !Office::query()->whereKey($targetOfficeId)->exists()
//         ) {
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

//         if (
//             !$targetOfficeId ||
//             !$ownerOfficeIds->contains($targetOfficeId)
//         ) {
//             return back()
//                 ->withErrors([
//                     'office_id' => 'Invalid office selected.',
//                 ])
//                 ->withInput();
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Owner plan employee limit
//         |--------------------------------------------------------------------------
//         */

//         $plan = Plan::query()
//             ->where('user_id', $loggedInUser->id)
//             ->latest('id')
//             ->first();

//         $employeeCount = User::query()
//             ->whereIn('office_id', $ownerOfficeIds)
//             ->count();

//         if (
//             $plan &&
//             $employeeCount >= (int) $plan->number_of_employees
//         ) {
//             return back()
//                 ->with(
//                     'error',
//                     'Your employee creation limit exceeded!'
//                 )
//                 ->withInput();
//         }

//     } else {

//         /*
//         |--------------------------------------------------------------------------
//         | Admin / Team Leader etc. active office
//         |--------------------------------------------------------------------------
//         */

//         $targetOfficeId = (int) $loggedInUser->activeOfficeId();

//         if (!$targetOfficeId) {
//             return back()
//                 ->with(
//                     'error',
//                     'Please select an office first.'
//                 )
//                 ->withInput();
//         }

//         $office = Office::query()
//             ->with('owner:id,name')
//             ->find($targetOfficeId);

//         if (!$office) {
//             return back()
//                 ->withErrors([
//                     'office_id' => 'Selected office was not found.',
//                 ])
//                 ->withInput();
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Office owner's plan employee limit
//         |--------------------------------------------------------------------------
//         */

//         if ($office->owner) {

//             $plan = Plan::query()
//                 ->where('user_id', $office->owner->id)
//                 ->latest('id')
//                 ->first();

//             $employeeCount = User::query()
//                 ->where('office_id', $targetOfficeId)
//                 ->count();

//             if (
//                 $plan &&
//                 $employeeCount >= (int) $plan->number_of_employees
//             ) {
//                 return back()
//                     ->with(
//                         'error',
//                         'Your employee creation limit exceeded!'
//                     )
//                     ->withInput();
//             }
//         }
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | Additional validation
//     |--------------------------------------------------------------------------
//     */

//     $validatedExtra = $request->validate([

//         /*
//         |--------------------------------------------------------------------------
//         | Role
//         |--------------------------------------------------------------------------
//         */

//         'role' => [
//             'required',
//             Rule::in([
//                 'admin',
//                 'team_leader',
//                 'employee',
//             ]),
//         ],

//         /*
//         |--------------------------------------------------------------------------
//         | Status
//         |--------------------------------------------------------------------------
//         */

//         'status' => [
//             'required',
//             Rule::in([
//                 '0',
//                 '1',
//                 0,
//                 1,
//             ]),
//         ],

//         /*
//         |--------------------------------------------------------------------------
//         | Reporting Manager
//         |--------------------------------------------------------------------------
//         */

//         'team_leader_id' => [
//             'nullable',
//             'integer',
//             'exists:users,id',
//         ],

//         /*
//         |--------------------------------------------------------------------------
//         | Leave Authority
//         |--------------------------------------------------------------------------
//         */

//         'leave_authority_id' => [
//             'nullable',
//             'integer',
//             'exists:users,id',
//         ],

//         /*
//         |--------------------------------------------------------------------------
//         | Educational Qualifications
//         |--------------------------------------------------------------------------
//         */

//         'qualifications' => [
//             'nullable',
//             'array',
//         ],

//         'qualifications.*.qualification' => [
//             'nullable',
//             'string',
//             'max:255',
//         ],

//         'qualifications.*.course_name' => [
//             'nullable',
//             'string',
//             'max:255',
//         ],

//         'qualifications.*.board_university' => [
//             'nullable',
//             'string',
//             'max:255',
//         ],

//         'qualifications.*.institute_name' => [
//             'nullable',
//             'string',
//             'max:255',
//         ],

//         'qualifications.*.passing_year' => [
//             'nullable',
//             'integer',
//             'min:1950',
//             'max:' . (now()->year + 10),
//         ],

//         'qualifications.*.result' => [
//             'nullable',
//             'string',
//             'max:100',
//         ],

//         'qualifications.*.document_type' => [
//             'nullable',
//             Rule::in([
//                 'marksheet',
//                 'degree',
//                 'certificate',
//             ]),
//         ],

//         'qualifications.*.document' => [
//             'nullable',
//             'file',
//             'mimes:jpg,jpeg,png,webp,pdf',
//             'max:5120',
//         ],

//         /*
//         |--------------------------------------------------------------------------
//         | Existing address / spouse / nominee / bank rules
//         |--------------------------------------------------------------------------
//         */

//         ...$this->employeeExtraProfileRules(),
//     ]);


//     try {

//         $checkInTime = Carbon::createFromFormat(
//             'H:i',
//             $request->input('check_in_time')
//         );

//         $checkOutTime = Carbon::createFromFormat(
//             'H:i',
//             $request->input('check_out_time')
//         );

//     } catch (\Throwable $exception) {

//         return back()
//             ->withErrors([
//                 'check_in_time' =>
//                     'Please enter valid check-in and check-out times.',
//             ])
//             ->withInput();
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | Handle night shift
//     |--------------------------------------------------------------------------
//     */

//     if ($checkOutTime->lessThanOrEqualTo($checkInTime)) {
//         $checkOutTime->addDay();
//     }

//     $officeMinutes = $checkInTime->diffInMinutes(
//         $checkOutTime
//     );



//     $employeeStatus =
//         (string) $request->input('status') === '1'
//             ? '1'
//             : '0';

//     /*
//     |--------------------------------------------------------------------------
//     | Structured Address
//     |--------------------------------------------------------------------------
//     */

//     $structuredAddress =
//         $this->employeeAddressPayload(
//             $validatedExtra
//         );

//     $formattedAddress =
//         $this->formattedEmployeeAddress(
//             $structuredAddress
//         );

//     /*
//     |--------------------------------------------------------------------------
//     | Start Database Transaction
//     |--------------------------------------------------------------------------
//     */

//     DB::beginTransaction();

//     try {

//         /*
//         |--------------------------------------------------------------------------
//         | Employee Data
//         |--------------------------------------------------------------------------
//         */

//         $employeeData = [

//             /*
//             |--------------------------------------------------------------------------
//             | Basic Details
//             |--------------------------------------------------------------------------
//             */

//             'name' => trim(
//                 (string) $request->input('name')
//             ),

//             'email' => $request->filled('email')
//                 ? strtolower(
//                     trim(
//                         (string) $request->input('email')
//                     )
//                 )
//                 : null,

//             'phone' => trim(
//                 (string) $request->input('phone')
//             ),

//             'dob' => $request->input('dob'),

//             'joining_date' =>
//                 $request->input('joining_date'),

//             'employee_id' =>
//                 $request->input('employee_id'),

//             /*
//             |--------------------------------------------------------------------------
//             | Address
//             |--------------------------------------------------------------------------
//             */

//             'address' => $formattedAddress
//                 ?? $this->cleanNullableString(
//                     $request->input('address')
//                 ),

//             /*
//             |--------------------------------------------------------------------------
//             | Employment Details
//             |--------------------------------------------------------------------------
//             */

//             'department_id' =>
//                 $request->filled('department_id')
//                     ? (int) $request->input(
//                         'department_id'
//                     )
//                     : null,

//             'designation' =>
//                 $request->input('designation'),

//             'responsibility' =>
//                 $request->input('responsibility'),

//             'salary' =>
//                 $request->filled('salary')
//                     ? (float) $request->input(
//                         'salary'
//                     )
//                     : null,

//             /*
//             |--------------------------------------------------------------------------
//             | Attendance
//             |--------------------------------------------------------------------------
//             */

//             'check_in_time' =>
//                 $request->input('check_in_time'),

//             'check_out_time' =>
//                 $request->input('check_out_time'),

//             'office_time' => $officeMinutes,

//             'break' =>
//                 $request->input('break'),

//             'location_required' =>
//                 $request->input(
//                     'location_required',
//                     'no'
//                 ),

//             /*
//             |--------------------------------------------------------------------------
//             | Office
//             |--------------------------------------------------------------------------
//             */

//             'office_id' => $targetOfficeId,

//             /*
//             |--------------------------------------------------------------------------
//             | Reporting Manager
//             |--------------------------------------------------------------------------
//             */

//             'team_leader_id' =>
//                 $request->filled(
//                     'team_leader_id'
//                 )
//                     ? (int) $validatedExtra[
//                         'team_leader_id'
//                     ]
//                     : null,

//             /*
//             |--------------------------------------------------------------------------
//             | Leave Authority
//             |--------------------------------------------------------------------------
//             */

//             'leave_authority_id' =>
//                 $request->filled(
//                     'leave_authority_id'
//                 )
//                     ? (int) $validatedExtra[
//                         'leave_authority_id'
//                     ]
//                     : null,

//             /*
//             |--------------------------------------------------------------------------
//             | Aadhaar Number
//             |--------------------------------------------------------------------------
//             */

//             'adhar_number' =>
//                 $request->filled(
//                     'adhar_number'
//                 )
//                     ? preg_replace(
//                         '/\D+/',
//                         '',
//                         (string) $request->input(
//                             'adhar_number'
//                         )
//                     )
//                     : null,

//             /*
//             |--------------------------------------------------------------------------
//             | PAN Number
//             |--------------------------------------------------------------------------
//             */

//             'pan_number' =>
//                 $request->filled(
//                     'pan_number'
//                 )
//                     ? strtoupper(
//                         trim(
//                             (string) $request->input(
//                                 'pan_number'
//                             )
//                         )
//                     )
//                     : null,

//             /*
//             |--------------------------------------------------------------------------
//             | Official Identifiers
//             |--------------------------------------------------------------------------
//             */

//             'uan_number' =>
//                 $request->input(
//                     'uan_number'
//                 ),

//             'esic_number' =>
//                 $request->input(
//                     'esic_number'
//                 ),

//             /*
//             |--------------------------------------------------------------------------
//             | Employee Bank Details
//             |--------------------------------------------------------------------------
//             */

//             'account_holder_name' =>
//                 $request->input(
//                     'account_holder_name'
//                 ),

//             'bank_name' =>
//                 $request->input(
//                     'bank_name'
//                 ),

//             'bank_branch' =>
//                 $request->input(
//                     'bank_branch'
//                 ),

//             'account_number' =>
//                 $request->filled(
//                     'account_number'
//                 )
//                     ? trim(
//                         (string) $request->input(
//                             'account_number'
//                         )
//                     )
//                     : null,

//             'ifsc_code' =>
//                 $request->filled(
//                     'ifsc_code'
//                 )
//                     ? strtoupper(
//                         trim(
//                             (string) $request->input(
//                                 'ifsc_code'
//                             )
//                         )
//                     )
//                     : null,

//             'account_type' =>
//                 $request->input(
//                     'account_type'
//                 ),

//             'upi_id' =>
//                 $request->filled(
//                     'upi_id'
//                 )
//                     ? strtolower(
//                         trim(
//                             (string) $request->input(
//                                 'upi_id'
//                             )
//                         )
//                     )
//                     : null,

//             /*
//             |--------------------------------------------------------------------------
//             | Default Password
//             |--------------------------------------------------------------------------
//             */

//             'password' => Hash::make(
//                 'password'
//             ),
//         ];

//         /*
//         |--------------------------------------------------------------------------
//         | Create Employee
//         |--------------------------------------------------------------------------
//         */

//         $employee = new User();


//         $employee->forceFill(
//             $employeeData
//         );

//         $employee->save();

//         /*
//         |--------------------------------------------------------------------------
//         | Employee Photo
//         |--------------------------------------------------------------------------
//         */

//         if ($request->hasFile('photo')) {

//             $employee->photo =
//                 $request
//                     ->file('photo')
//                     ->store(
//                         'photos',
//                         'public'
//                     );
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Aadhaar Attachment
//         |--------------------------------------------------------------------------
//         */

//         if (
//             $request->hasFile(
//                 'aadhar_attachment'
//             )
//         ) {

//             $employee->aadhar_attachment =
//                 $request
//                     ->file(
//                         'aadhar_attachment'
//                     )
//                     ->store(
//                         'aadhar_attachments',
//                         'public'
//                     );
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | PAN Attachment
//         |--------------------------------------------------------------------------
//         */

//         if (
//             $request->hasFile(
//                 'pan_attachment'
//             )
//         ) {

//             $employee->pan_attachment =
//                 $request
//                     ->file(
//                         'pan_attachment'
//                     )
//                     ->store(
//                         'pan_attachments',
//                         'public'
//                     );
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Other Attachment
//         |--------------------------------------------------------------------------
//         */

//         if (
//             $request->hasFile(
//                 'other_attachment'
//             )
//         ) {

//             $employee->other_attachment =
//                 $request
//                     ->file(
//                         'other_attachment'
//                     )
//                     ->store(
//                         'other_attachments',
//                         'public'
//                     );
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Save uploaded file paths
//         |--------------------------------------------------------------------------
//         */

//         if ($employee->isDirty()) {
//             $employee->save();
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Assign Employee Role
//         |--------------------------------------------------------------------------
//         */

//         $employee->syncRoles([
//             $validatedExtra['role'],
//         ]);


//         DB::table('users')
//             ->where(
//                 'id',
//                 $employee->id
//             )
//             ->update([
//                 'status' => $employeeStatus,
//                 'updated_at' => now(),
//             ]);

//         /*
//         |--------------------------------------------------------------------------
//         | Refresh Employee
//         |--------------------------------------------------------------------------
//         */

//         $employee->refresh();

//         /*
//         |--------------------------------------------------------------------------
//         | Confirm Status Saved Properly
//         |--------------------------------------------------------------------------
//         */

//         if (
//             (string) $employee->status
//             !== $employeeStatus
//         ) {
//             throw new \RuntimeException(
//                 'Employee status could not be saved correctly.'
//             );
//         }

//         $this->saveEmployeeExtraProfile(
//             $employee,
//             $validatedExtra
//         );

//         /*
//         |--------------------------------------------------------------------------
//         | Save Educational Qualifications
//         |--------------------------------------------------------------------------
//         */

//         $qualifications =
//             $request->input(
//                 'qualifications',
//                 []
//             );

//         foreach (
//             $qualifications as $index => $qualification
//         ) {

//             /*
//             |--------------------------------------------------------------------------
//             | Clean qualification values
//             |--------------------------------------------------------------------------
//             */

//             $qualificationName =
//                 $this->cleanNullableString(
//                     $qualification[
//                         'qualification'
//                     ] ?? null
//                 );

//             $courseName =
//                 $this->cleanNullableString(
//                     $qualification[
//                         'course_name'
//                     ] ?? null
//                 );

//             $boardUniversity =
//                 $this->cleanNullableString(
//                     $qualification[
//                         'board_university'
//                     ] ?? null
//                 );

//             $instituteName =
//                 $this->cleanNullableString(
//                     $qualification[
//                         'institute_name'
//                     ] ?? null
//                 );

//             $passingYear =
//                 $qualification[
//                     'passing_year'
//                 ] ?? null;

//             $result =
//                 $this->cleanNullableString(
//                     $qualification[
//                         'result'
//                     ] ?? null
//                 );

//                 $documentType =
//                     $this->cleanNullableString(
//                         $qualification[
//                             'document_type'
//                         ] ?? null
//                     );

//                 $documentPath = null;

//                 /*
//                 |--------------------------------------------------------------------------
//                 | Upload Marksheet / Degree
//                 |--------------------------------------------------------------------------
//                 */

//                 if (
//                     $request->hasFile(
//                         "qualifications.$index.document"
//                     )
//                 ) {
//                     $documentPath = $request
//                         ->file(
//                             "qualifications.$index.document"
//                         )
//                         ->store(
//                             "employee_qualifications/{$employee->id}",
//                             'public'
//                         );
//                 }

//             /*
//             |--------------------------------------------------------------------------
//             | Check whether row contains any value
//             |--------------------------------------------------------------------------
//             */

//             $hasAnyQualificationValue =
//                 $qualificationName !== null
//                 || $courseName !== null
//                 || $boardUniversity !== null
//                 || $instituteName !== null
//                 || filled($passingYear)
//                 || $result !== null
//                 || $documentType !== null
//                 || $documentPath !== null;

//             /*
//             |--------------------------------------------------------------------------
//             | Ignore completely empty rows
//             |--------------------------------------------------------------------------
//             */

//             if (
//                 !$hasAnyQualificationValue
//             ) {
//                 continue;
//             }

//             /*
//             |--------------------------------------------------------------------------
//             | Qualification name is required for a non-empty row
//             |--------------------------------------------------------------------------
//             |
//             | Migration me qualification nullable nahi hai.
//             |
//             */

//             if (
//                 $qualificationName === null
//             ) {

//                 throw \Illuminate\Validation\ValidationException::withMessages([
//                     "qualifications.$index.qualification" =>
//                         'Please select qualification.',
//                 ]);
//             }

//             /*
//             |--------------------------------------------------------------------------
//             | Create Qualification
//             |--------------------------------------------------------------------------
//             */

//             EmployeeEducationalQualification::query()
//                 ->create([

//                     'user_id' =>
//                         $employee->id,

//                     'qualification' =>
//                         $qualificationName,

//                     'course_name' =>
//                         $courseName,

//                     'board_university' =>
//                         $boardUniversity,

//                     'institute_name' =>
//                         $instituteName,

//                     'passing_year' =>
//                         filled($passingYear)
//                             ? (int) $passingYear
//                             : null,

//                     'result' =>
//                         $result,

//                     'document_type' =>
//                         $documentType,

//                     'document_path' =>
//                         $documentPath,
//                 ]);
//         }

//         /*
//         |--------------------------------------------------------------------------
//         | Salary Details
//         |--------------------------------------------------------------------------
//         */

//         $basicSalary =
//             (float) $request->input(
//                 'basic_salary',
//                 0
//             );

//         $houseRentAllowance =
//             (float) $request->input(
//                 'house_rent_allowance',
//                 0
//             );

//         $transportAllowance =
//             (float) $request->input(
//                 'transport_allowance',
//                 0
//             );

//         $medicalAllowance =
//             (float) $request->input(
//                 'medical_allowance',
//                 0
//             );

//         $specialAllowance =
//             (float) $request->input(
//                 'special_allowance',
//                 0
//             );

//         $dearnessAllowance =
//             (float) $request->input(
//                 'dearness_allowance',
//                 0
//             );

//         $relievingCharge =
//             (float) $request->input(
//                 'relieving_charge',
//                 0
//             );

//         $additionalAllowance =
//             (float) $request->input(
//                 'additional_allowance',
//                 0
//             );

//         $providentFund =
//             (float) $request->input(
//                 'provident_fund',
//                 0
//             );

//         $esic =
//             (float) $request->input(
//                 'employee_state_insurance_corporation',
//                 0
//             );

//         /*
//         |--------------------------------------------------------------------------
//         | Total Salary
//         |--------------------------------------------------------------------------
//         */

//         $totalSalary =
//             $basicSalary
//             + $houseRentAllowance
//             + $transportAllowance
//             + $medicalAllowance
//             + $specialAllowance
//             + $dearnessAllowance
//             + $relievingCharge
//             + $additionalAllowance;

//         /*
//         |--------------------------------------------------------------------------
//         | Save Salary
//         |--------------------------------------------------------------------------
//         */

//         UserSalary::query()
//             ->create([

//                 'user_id' =>
//                     $employee->id,

//                 'basic_salary' =>
//                     $basicSalary,

//                 'house_rent_allowance' =>
//                     $houseRentAllowance,

//                 'transport_allowance' =>
//                     $transportAllowance,

//                 'medical_allowance' =>
//                     $medicalAllowance,

//                 'special_allowance' =>
//                     $specialAllowance,

//                 'dearness_allowance' =>
//                     $dearnessAllowance,

//                 'relieving_charge' =>
//                     $relievingCharge,

//                 'additional_allowance' =>
//                     $additionalAllowance,

//                 'provident_fund' =>
//                     $providentFund,

//                 'employee_state_insurance_corporation' =>
//                     $esic,

//                 'total_salary' =>
//                     $totalSalary,
//             ]);

//         /*
//         |--------------------------------------------------------------------------
//         | Commit Transaction
//         |--------------------------------------------------------------------------
//         */

//         DB::commit();

//         /*
//         |--------------------------------------------------------------------------
//         | Success Response
//         |--------------------------------------------------------------------------
//         */

//         return redirect()
//             ->route('employee.index')
//             ->with(
//                 'success',
//                 $employeeStatus === '1'
//                     ? 'Active employee registered successfully.'
//                     : 'Inactive employee registered successfully.'
//             );

//     } catch (
//         \Illuminate\Validation\ValidationException $exception
//     ) {

//         /*
//         |--------------------------------------------------------------------------
//         | Validation failure after transaction started
//         |--------------------------------------------------------------------------
//         */

//         DB::rollBack();

//         throw $exception;

//     } catch (\Throwable $exception) {

//         DB::rollBack();

//         report($exception);

//         /*
//         |--------------------------------------------------------------------------
//         | Return Error
//         |--------------------------------------------------------------------------
//         */

//         return back()
//             ->with(
//                 'error',
//                 config('app.debug')
//                     ? $exception->getMessage()
//                     : 'Employee could not be registered. Please try again.'
//             )
//             ->withInput();
//     }
// }


public function store(EmployeeRequest $request)
{
    $loggedInUser = $request->user();

    /*
    |--------------------------------------------------------------------------
    | Resolve target office
    |--------------------------------------------------------------------------
    */

    if ($loggedInUser->hasRole('super_admin')) {

        $targetOfficeId = (int) $request->input('office_id');

        if (
            !$targetOfficeId ||
            !Office::query()->whereKey($targetOfficeId)->exists()
        ) {
            return back()
                ->withErrors([
                    'office_id' => 'Please select a valid office.',
                ])
                ->withInput();
        }

    } elseif ($loggedInUser->hasRole('owner')) {

        $ownerOfficeIds = Office::query()
            ->where('owner_id', $loggedInUser->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id);

        if ($ownerOfficeIds->isEmpty()) {
            return back()
                ->with('error', 'No office found for this owner.')
                ->withInput();
        }

        $targetOfficeId = (int) (
            $request->input('office_id')
            ?: $loggedInUser->activeOfficeId()
        );

        if (
            !$targetOfficeId ||
            !$ownerOfficeIds->contains($targetOfficeId)
        ) {
            return back()
                ->withErrors([
                    'office_id' => 'Invalid office selected.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Owner plan employee limit
        |--------------------------------------------------------------------------
        */

        $plan = Plan::query()
            ->where('user_id', $loggedInUser->id)
            ->latest('id')
            ->first();

        $employeeCount = User::query()
            ->whereIn('office_id', $ownerOfficeIds)
            ->count();

        if (
            $plan &&
            $employeeCount >= (int) $plan->number_of_employees
        ) {
            return back()
                ->with(
                    'error',
                    'Your employee creation limit exceeded!'
                )
                ->withInput();
        }

    } else {

        /*
        |--------------------------------------------------------------------------
        | Admin / Team Leader etc. active office
        |--------------------------------------------------------------------------
        */

        $targetOfficeId = (int) $loggedInUser->activeOfficeId();

        if (!$targetOfficeId) {
            return back()
                ->with(
                    'error',
                    'Please select an office first.'
                )
                ->withInput();
        }

        $office = Office::query()
            ->with('owner:id,name')
            ->find($targetOfficeId);

        if (!$office) {
            return back()
                ->withErrors([
                    'office_id' => 'Selected office was not found.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Office owner's plan employee limit
        |--------------------------------------------------------------------------
        */

        if ($office->owner) {

            $plan = Plan::query()
                ->where('user_id', $office->owner->id)
                ->latest('id')
                ->first();

            $employeeCount = User::query()
                ->where('office_id', $targetOfficeId)
                ->count();

            if (
                $plan &&
                $employeeCount >= (int) $plan->number_of_employees
            ) {
                return back()
                    ->with(
                        'error',
                        'Your employee creation limit exceeded!'
                    )
                    ->withInput();
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Additional validation
    |--------------------------------------------------------------------------
    */

    $validatedExtra = $request->validate([

        /*
        |--------------------------------------------------------------------------
        | Role
        |--------------------------------------------------------------------------
        */

        'role' => [
            'required',
            Rule::in([
                'admin',
                'team_leader',
                'employee',
            ]),
        ],

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        'status' => [
            'required',
            Rule::in([
                '0',
                '1',
                0,
                1,
            ]),
        ],

        /*
        |--------------------------------------------------------------------------
        | Reporting Manager
        |--------------------------------------------------------------------------
        */

        'team_leader_id' => [
            'nullable',
            'integer',
            'exists:users,id',
        ],

        /*
        |--------------------------------------------------------------------------
        | Leave Authority
        |--------------------------------------------------------------------------
        */

        'leave_authority_id' => [
            'nullable',
            'integer',
            'exists:users,id',
        ],

        /*
        |--------------------------------------------------------------------------
        | Alternate Number
        |--------------------------------------------------------------------------
        */

        'alternate_number' => [
            'nullable',
            'string',
            'max:20',
        ],

        'last_working_date' => [
            'nullable',
            'date',
            'after_or_equal:joining_date',
        ],

        'joining_date' => [
            'nullable',
            'required_with:last_working_date',
            'date',
        ],

        /*
        |--------------------------------------------------------------------------
        | Family Members
        |--------------------------------------------------------------------------
        */

        'family_members' => [
            'nullable',
            'array',
            'max:20',
        ],

        'family_members.*.name' => [
            'required',
            'string',
            'max:255',
        ],

        'family_members.*.relation' => [
            'required',
            'string',
            'max:100',
        ],

        'family_members.*.occupation' => [
            'nullable',
            'string',
            'max:255',
        ],

        'family_members.*.age' => [
            'nullable',
            'integer',
            'min:0',
            'max:120',
        ],

        /*
        |--------------------------------------------------------------------------
        | Educational Qualifications
        |--------------------------------------------------------------------------
        */

        'qualifications' => [
            'nullable',
            'array',
        ],

        'qualifications.*.qualification' => [
            'nullable',
            'string',
            'max:255',
        ],

        'qualifications.*.course_name' => [
            'nullable',
            'string',
            'max:255',
        ],

        'qualifications.*.board_university' => [
            'nullable',
            'string',
            'max:255',
        ],

        'qualifications.*.institute_name' => [
            'nullable',
            'string',
            'max:255',
        ],

        'qualifications.*.passing_year' => [
            'nullable',
            'integer',
            'min:1950',
            'max:' . (now()->year + 10),
        ],

        'qualifications.*.result' => [
            'nullable',
            'string',
            'max:100',
        ],

        'qualifications.*.document_type' => [
            'nullable',
            Rule::in([
                'marksheet',
                'degree',
                'certificate',
            ]),
        ],

        'qualifications.*.document' => [
            'nullable',
            'file',
            'mimes:jpg,jpeg,png,webp,pdf',
            'max:5120',
        ],

        /*
        |--------------------------------------------------------------------------
        | Existing address / spouse / nominee / bank rules
        |--------------------------------------------------------------------------
        */

        ...$this->employeeExtraProfileRules(),
    ]);


    try {

        $checkInTime = Carbon::createFromFormat(
            'H:i',
            $request->input('check_in_time')
        );

        $checkOutTime = Carbon::createFromFormat(
            'H:i',
            $request->input('check_out_time')
        );

    } catch (\Throwable $exception) {

        return back()
            ->withErrors([
                'check_in_time' =>
                    'Please enter valid check-in and check-out times.',
            ])
            ->withInput();
    }

    /*
    |--------------------------------------------------------------------------
    | Handle night shift
    |--------------------------------------------------------------------------
    */

    if ($checkOutTime->lessThanOrEqualTo($checkInTime)) {
        $checkOutTime->addDay();
    }

    $officeMinutes = $checkInTime->diffInMinutes(
        $checkOutTime
    );



    $employeeStatus =
        (string) $request->input('status') === '1'
            ? '1'
            : '0';

    /*
    |--------------------------------------------------------------------------
    | Structured Address
    |--------------------------------------------------------------------------
    */

    $structuredAddress =
        $this->employeeAddressPayload(
            $validatedExtra
        );

    $formattedAddress =
        $this->formattedEmployeeAddress(
            $structuredAddress
        );

    /*
    |--------------------------------------------------------------------------
    | Start Database Transaction
    |--------------------------------------------------------------------------
    */

    DB::beginTransaction();

    try {

        /*
        |--------------------------------------------------------------------------
        | Allocate Employee ID safely
        |--------------------------------------------------------------------------
        |
        | The submitted readonly field is only a preview. The final employee ID is
        | generated on the server while the office row is locked, so two users
        | cannot receive the same employee ID at the same time.
        |
        */

        $employeeId = $this->allocateEmployeeId($targetOfficeId);

        /*
        |--------------------------------------------------------------------------
        | Employee Data
        |--------------------------------------------------------------------------
        */

        $employeeData = [

            /*
            |--------------------------------------------------------------------------
            | Basic Details
            |--------------------------------------------------------------------------
            */

            'name' => trim(
                (string) $request->input('name')
            ),

            'email' => $request->filled('email')
                ? strtolower(
                    trim(
                        (string) $request->input('email')
                    )
                )
                : null,

            'phone' => trim(
                (string) $request->input('phone')
            ),

            'alternate_number' =>
                $this->cleanNullableString(
                    $request->input('alternate_number')
                ),

            'dob' => $request->input('dob'),

            'joining_date' =>
                $request->input('joining_date'),

            'last_working_date' =>
                $request->input('last_working_date'),

            'employee_id' =>
                $employeeId,

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            'address' => $formattedAddress
                ?? $this->cleanNullableString(
                    $request->input('address')
                ),

            /*
            |--------------------------------------------------------------------------
            | Employment Details
            |--------------------------------------------------------------------------
            */

            'department_id' =>
                $request->filled('department_id')
                    ? (int) $request->input(
                        'department_id'
                    )
                    : null,

            'designation' =>
                $request->input('designation'),

            'responsibility' =>
                $request->input('responsibility'),

            'salary' =>
                $request->filled('salary')
                    ? (float) $request->input(
                        'salary'
                    )
                    : null,

            /*
            |--------------------------------------------------------------------------
            | Attendance
            |--------------------------------------------------------------------------
            */

            'check_in_time' =>
                $request->input('check_in_time'),

            'check_out_time' =>
                $request->input('check_out_time'),

            'office_time' => $officeMinutes,

            'break' =>
                $request->input('break'),

            'location_required' =>
                $request->input(
                    'location_required',
                    'no'
                ),

            /*
            |--------------------------------------------------------------------------
            | Office
            |--------------------------------------------------------------------------
            */

            'office_id' => $targetOfficeId,

            /*
            |--------------------------------------------------------------------------
            | Reporting Manager
            |--------------------------------------------------------------------------
            */

            'team_leader_id' =>
                $request->filled(
                    'team_leader_id'
                )
                    ? (int) $validatedExtra[
                        'team_leader_id'
                    ]
                    : null,

            /*
            |--------------------------------------------------------------------------
            | Leave Authority
            |--------------------------------------------------------------------------
            */

            'leave_authority_id' =>
                $request->filled(
                    'leave_authority_id'
                )
                    ? (int) $validatedExtra[
                        'leave_authority_id'
                    ]
                    : null,

            /*
            |--------------------------------------------------------------------------
            | Aadhaar Number
            |--------------------------------------------------------------------------
            */

            'adhar_number' =>
                $request->filled(
                    'adhar_number'
                )
                    ? preg_replace(
                        '/\D+/',
                        '',
                        (string) $request->input(
                            'adhar_number'
                        )
                    )
                    : null,

            /*
            |--------------------------------------------------------------------------
            | PAN Number
            |--------------------------------------------------------------------------
            */

            'pan_number' =>
                $request->filled(
                    'pan_number'
                )
                    ? strtoupper(
                        trim(
                            (string) $request->input(
                                'pan_number'
                            )
                        )
                    )
                    : null,

            /*
            |--------------------------------------------------------------------------
            | Official Identifiers
            |--------------------------------------------------------------------------
            */

            'uan_number' =>
                $request->input(
                    'uan_number'
                ),

            'esic_number' =>
                $request->input(
                    'esic_number'
                ),

            /*
            |--------------------------------------------------------------------------
            | Employee Bank Details
            |--------------------------------------------------------------------------
            */

            'account_holder_name' =>
                $request->input(
                    'account_holder_name'
                ),

            'bank_name' =>
                $request->input(
                    'bank_name'
                ),

            'bank_branch' =>
                $request->input(
                    'bank_branch'
                ),

            'account_number' =>
                $request->filled(
                    'account_number'
                )
                    ? trim(
                        (string) $request->input(
                            'account_number'
                        )
                    )
                    : null,

            'ifsc_code' =>
                $request->filled(
                    'ifsc_code'
                )
                    ? strtoupper(
                        trim(
                            (string) $request->input(
                                'ifsc_code'
                            )
                        )
                    )
                    : null,

            'account_type' =>
                $request->input(
                    'account_type'
                ),

            'upi_id' =>
                $request->filled(
                    'upi_id'
                )
                    ? strtolower(
                        trim(
                            (string) $request->input(
                                'upi_id'
                            )
                        )
                    )
                    : null,

            /*
            |--------------------------------------------------------------------------
            | Default Password
            |--------------------------------------------------------------------------
            */

            'password' => Hash::make(
                'password'
            ),
        ];

        /*
        |--------------------------------------------------------------------------
        | Create Employee
        |--------------------------------------------------------------------------
        */

        $employee = new User();


        $employee->forceFill(
            $employeeData
        );

        $employee->save();

        /*
        |--------------------------------------------------------------------------
        | Employee Photo
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('photo')) {

            $employee->photo =
                $request
                    ->file('photo')
                    ->store(
                        'photos',
                        'public'
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | Aadhaar Attachment
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile(
                'aadhar_attachment'
            )
        ) {

            $employee->aadhar_attachment =
                $request
                    ->file(
                        'aadhar_attachment'
                    )
                    ->store(
                        'aadhar_attachments',
                        'public'
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | PAN Attachment
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile(
                'pan_attachment'
            )
        ) {

            $employee->pan_attachment =
                $request
                    ->file(
                        'pan_attachment'
                    )
                    ->store(
                        'pan_attachments',
                        'public'
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | Other Attachment
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile(
                'other_attachment'
            )
        ) {

            $employee->other_attachment =
                $request
                    ->file(
                        'other_attachment'
                    )
                    ->store(
                        'other_attachments',
                        'public'
                    );
        }

        /*
        |--------------------------------------------------------------------------
        | Save uploaded file paths
        |--------------------------------------------------------------------------
        */

        if ($employee->isDirty()) {
            $employee->save();
        }

        /*
        |--------------------------------------------------------------------------
        | Assign Employee Role
        |--------------------------------------------------------------------------
        */

        $employee->syncRoles([
            $validatedExtra['role'],
        ]);


        DB::table('users')
            ->where(
                'id',
                $employee->id
            )
            ->update([
                'status' => $employeeStatus,
                'updated_at' => now(),
            ]);

        /*
        |--------------------------------------------------------------------------
        | Refresh Employee
        |--------------------------------------------------------------------------
        */

        $employee->refresh();

        /*
        |--------------------------------------------------------------------------
        | Confirm Status Saved Properly
        |--------------------------------------------------------------------------
        */

        if (
            (string) $employee->status
            !== $employeeStatus
        ) {
            throw new \RuntimeException(
                'Employee status could not be saved correctly.'
            );
        }

        $this->saveEmployeeExtraProfile(
            $employee,
            $validatedExtra
        );

        /*
        |--------------------------------------------------------------------------
        | Save Family Members
        |--------------------------------------------------------------------------
        */

        $familyMembers = $request->input(
            'family_members',
            []
        );

        foreach ($familyMembers as $index => $familyMember) {

            $familyName = $this->cleanNullableString(
                $familyMember['name'] ?? null
            );

            $familyRelation = $this->cleanNullableString(
                $familyMember['relation'] ?? null
            );

            $familyOccupation = $this->cleanNullableString(
                $familyMember['occupation'] ?? null
            );

            $familyAge = $familyMember['age'] ?? null;

            /*
            |--------------------------------------------------------------------------
            | Ignore completely empty rows
            |--------------------------------------------------------------------------
            */

            $hasAnyFamilyValue =
                $familyName !== null
                || $familyRelation !== null
                || $familyOccupation !== null
                || filled($familyAge);

            if (!$hasAnyFamilyValue) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Name and relation are required for a non-empty family row
            |--------------------------------------------------------------------------
            */

            if ($familyName === null) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "family_members.$index.name" =>
                        'Please enter family member name.',
                ]);
            }

            if ($familyRelation === null) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "family_members.$index.relation" =>
                        'Please select family member relation.',
                ]);
            }

            \App\Models\EmployeeFamilyMember::query()
                ->create([
                    'user_id' => $employee->id,
                    'name' => $familyName,
                    'relation' => $familyRelation,
                    'occupation' => $familyOccupation,
                    'age' => filled($familyAge)
                        ? (int) $familyAge
                        : null,
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Save Educational Qualifications
        |--------------------------------------------------------------------------
        */

        $qualifications =
            $request->input(
                'qualifications',
                []
            );

        foreach (
            $qualifications as $index => $qualification
        ) {

            /*
            |--------------------------------------------------------------------------
            | Clean qualification values
            |--------------------------------------------------------------------------
            */

            $qualificationName =
                $this->cleanNullableString(
                    $qualification[
                        'qualification'
                    ] ?? null
                );

            $courseName =
                $this->cleanNullableString(
                    $qualification[
                        'course_name'
                    ] ?? null
                );

            $boardUniversity =
                $this->cleanNullableString(
                    $qualification[
                        'board_university'
                    ] ?? null
                );

            $instituteName =
                $this->cleanNullableString(
                    $qualification[
                        'institute_name'
                    ] ?? null
                );

            $passingYear =
                $qualification[
                    'passing_year'
                ] ?? null;

            $result =
                $this->cleanNullableString(
                    $qualification[
                        'result'
                    ] ?? null
                );

                $documentType =
                    $this->cleanNullableString(
                        $qualification[
                            'document_type'
                        ] ?? null
                    );

                $documentPath = null;

                /*
                |--------------------------------------------------------------------------
                | Upload Marksheet / Degree
                |--------------------------------------------------------------------------
                */

                if (
                    $request->hasFile(
                        "qualifications.$index.document"
                    )
                ) {
                    $documentPath = $request
                        ->file(
                            "qualifications.$index.document"
                        )
                        ->store(
                            "employee_qualifications/{$employee->id}",
                            'public'
                        );
                }

            /*
            |--------------------------------------------------------------------------
            | Check whether row contains any value
            |--------------------------------------------------------------------------
            */

            $hasAnyQualificationValue =
                $qualificationName !== null
                || $courseName !== null
                || $boardUniversity !== null
                || $instituteName !== null
                || filled($passingYear)
                || $result !== null
                || $documentType !== null
                || $documentPath !== null;

            /*
            |--------------------------------------------------------------------------
            | Ignore completely empty rows
            |--------------------------------------------------------------------------
            */

            if (
                !$hasAnyQualificationValue
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Qualification name is required for a non-empty row
            |--------------------------------------------------------------------------
            |
            | Migration me qualification nullable nahi hai.
            |
            */

            if (
                $qualificationName === null
            ) {

                throw \Illuminate\Validation\ValidationException::withMessages([
                    "qualifications.$index.qualification" =>
                        'Please select qualification.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Create Qualification
            |--------------------------------------------------------------------------
            */

            EmployeeEducationalQualification::query()
                ->create([

                    'user_id' =>
                        $employee->id,

                    'qualification' =>
                        $qualificationName,

                    'course_name' =>
                        $courseName,

                    'board_university' =>
                        $boardUniversity,

                    'institute_name' =>
                        $instituteName,

                    'passing_year' =>
                        filled($passingYear)
                            ? (int) $passingYear
                            : null,

                    'result' =>
                        $result,

                    'document_type' =>
                        $documentType,

                    'document_path' =>
                        $documentPath,
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Salary Details
        |--------------------------------------------------------------------------
        */

        $basicSalary =
            (float) $request->input(
                'basic_salary',
                0
            );

        $houseRentAllowance =
            (float) $request->input(
                'house_rent_allowance',
                0
            );

        $transportAllowance =
            (float) $request->input(
                'transport_allowance',
                0
            );

        $medicalAllowance =
            (float) $request->input(
                'medical_allowance',
                0
            );

        $specialAllowance =
            (float) $request->input(
                'special_allowance',
                0
            );

        $dearnessAllowance =
            (float) $request->input(
                'dearness_allowance',
                0
            );

        $relievingCharge =
            (float) $request->input(
                'relieving_charge',
                0
            );

        $additionalAllowance =
            (float) $request->input(
                'additional_allowance',
                0
            );

        $providentFund =
            (float) $request->input(
                'provident_fund',
                0
            );

        $esic =
            (float) $request->input(
                'employee_state_insurance_corporation',
                0
            );

        /*
        |--------------------------------------------------------------------------
        | Total Salary
        |--------------------------------------------------------------------------
        */

        $totalSalary =
            $basicSalary
            + $houseRentAllowance
            + $transportAllowance
            + $medicalAllowance
            + $specialAllowance
            + $dearnessAllowance
            + $relievingCharge
            + $additionalAllowance;

        /*
        |--------------------------------------------------------------------------
        | Save Salary
        |--------------------------------------------------------------------------
        */

        UserSalary::query()
            ->create([

                'user_id' =>
                    $employee->id,

                'basic_salary' =>
                    $basicSalary,

                'house_rent_allowance' =>
                    $houseRentAllowance,

                'transport_allowance' =>
                    $transportAllowance,

                'medical_allowance' =>
                    $medicalAllowance,

                'special_allowance' =>
                    $specialAllowance,

                'dearness_allowance' =>
                    $dearnessAllowance,

                'relieving_charge' =>
                    $relievingCharge,

                'additional_allowance' =>
                    $additionalAllowance,

                'provident_fund' =>
                    $providentFund,

                'employee_state_insurance_corporation' =>
                    $esic,

                'total_salary' =>
                    $totalSalary,
            ]);

        /*
        |--------------------------------------------------------------------------
        | Commit Transaction
        |--------------------------------------------------------------------------
        */

        DB::commit();

        /*
        |--------------------------------------------------------------------------
        | Success Response
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('employee.index')
            ->with(
                'success',
                $employeeStatus === '1'
                    ? 'Active employee registered successfully.'
                    : 'Inactive employee registered successfully.'
            );

    } catch (
        \Illuminate\Validation\ValidationException $exception
    ) {

        /*
        |--------------------------------------------------------------------------
        | Validation failure after transaction started
        |--------------------------------------------------------------------------
        */

        DB::rollBack();

        throw $exception;

    } catch (\Throwable $exception) {

        DB::rollBack();

        report($exception);

        /*
        |--------------------------------------------------------------------------
        | Return Error
        |--------------------------------------------------------------------------
        */

        return back()
            ->with(
                'error',
                config('app.debug')
                    ? $exception->getMessage()
                    : 'Employee could not be registered. Please try again.'
            )
            ->withInput();
    }
}

    // public function edit(Request $request, User $employee)
    // {
    //     $loggedInUser = $request->user();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Resolve accessible offices
    //     |--------------------------------------------------------------------------
    //     */

    //     if ($loggedInUser->hasRole('super_admin')) {

    //         $offices = Office::query()
    //             ->select([
    //                 'id',
    //                 'name',
    //                 'owner_id',
    //             ])
    //             ->orderBy('name')
    //             ->get();

    //         $allowedOfficeIds = $offices
    //             ->pluck('id')
    //             ->map(fn ($id) => (int) $id);

    //     } elseif ($loggedInUser->hasRole('owner')) {

    //         $ownerOfficeIds = Office::query()
    //             ->where('owner_id', $loggedInUser->id)
    //             ->pluck('id')
    //             ->map(fn ($id) => (int) $id);

    //         if ($ownerOfficeIds->isEmpty()) {
    //             return back()->with(
    //                 'error',
    //                 'No office found for this owner.'
    //             );
    //         }

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Employee must belong to owner's office
    //         |--------------------------------------------------------------------------
    //         */

    //         if (
    //             !$ownerOfficeIds->contains(
    //                 (int) $employee->office_id
    //             )
    //         ) {
    //             abort(
    //                 403,
    //                 'This employee does not belong to your office.'
    //             );
    //         }

    //         $offices = Office::query()
    //             ->select([
    //                 'id',
    //                 'name',
    //                 'owner_id',
    //             ])
    //             ->whereIn(
    //                 'id',
    //                 $ownerOfficeIds
    //             )
    //             ->orderBy('name')
    //             ->get();

    //         $allowedOfficeIds = $ownerOfficeIds;

    //     } else {

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Admin / Team Leader active office
    //         |--------------------------------------------------------------------------
    //         */

    //         $activeOfficeId = (int)
    //             $loggedInUser->activeOfficeId();

    //         if (!$activeOfficeId) {
    //             return back()->with(
    //                 'error',
    //                 'Please select an office first.'
    //             );
    //         }

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Employee must belong to active office
    //         |--------------------------------------------------------------------------
    //         */

    //         if (
    //             (int) $employee->office_id
    //             !== $activeOfficeId
    //         ) {
    //             abort(
    //                 403,
    //                 'This employee does not belong to the selected office.'
    //             );
    //         }

    //         $offices = Office::query()
    //             ->select([
    //                 'id',
    //                 'name',
    //                 'owner_id',
    //             ])
    //             ->whereKey($activeOfficeId)
    //             ->get();

    //         $allowedOfficeIds = collect([
    //             $activeOfficeId,
    //         ]);
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Reporting Managers
    //     |--------------------------------------------------------------------------
    //     |
    //     | Team Leader, Admin aur Owner tino list me aayenge.
    //     | Status filter intentionally nahi hai.
    //     |
    //     */

    //     $teamLeaders = User::query()
    //         ->select([
    //             'users.id',
    //             'users.name',
    //             'users.office_id',
    //             'users.status',
    //         ])
    //         ->whereIn(
    //             'users.office_id',
    //             $allowedOfficeIds
    //         )
    //         ->whereKeyNot($employee->id)
    //         ->whereHas(
    //             'roles',
    //             function ($query) {
    //                 $query->whereIn(
    //                     'roles.name',
    //                     [
    //                         'team_leader',
    //                         'admin',
    //                         'owner',
    //                     ]
    //                 );
    //             }
    //         )
    //         ->with([
    //             'office:id,name',
    //             'roles:id,name',
    //         ])
    //         ->orderBy('users.name')
    //         ->get();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Leave Authorities
    //     |--------------------------------------------------------------------------
    //     */

    //     $leaveAuthorities = $teamLeaders;

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Departments
    //     |--------------------------------------------------------------------------
    //     */

    //     $departments = Department::query()
    //         ->select([
    //             'id',
    //             'name',
    //         ])
    //         ->orderBy('name')
    //         ->get();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Employee Relations
    //     |--------------------------------------------------------------------------
    //     */

    //     $employee->load([
    //         'office:id,name',
    //         'department:id,name',
    //         'teamLeader:id,name',
    //         'leaveAuthority:id,name',
    //         'userSalary',
    //     ]);

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Structured Employee Address
    //     |--------------------------------------------------------------------------
    //     */

    //     $employeeAddress = EmployeeAddress::query()
    //         ->where(
    //             'user_id',
    //             $employee->id
    //         )
    //         ->first();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Family / Spouse Details
    //     |--------------------------------------------------------------------------
    //     */

    //     $employeeFamily =
    //         EmployeeFamilyDetail::query()
    //             ->where(
    //                 'user_id',
    //                 $employee->id
    //             )
    //             ->first();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Nominee Details
    //     |--------------------------------------------------------------------------
    //     */

    //     $employeeNominee =
    //         EmployeeNominee::query()
    //             ->where(
    //                 'user_id',
    //                 $employee->id
    //             )
    //             ->first();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Educational Qualifications
    //     |--------------------------------------------------------------------------
    //     |
    //     | Ek employee ki multiple qualifications ho sakti hain.
    //     |
    //     */

    //     $employeeQualifications =
    //         EmployeeEducationalQualification::query()
    //             ->where(
    //                 'user_id',
    //                 $employee->id
    //             )
    //             ->orderByRaw(
    //                 'passing_year IS NULL'
    //             )
    //             ->orderBy(
    //                 'passing_year'
    //             )
    //             ->orderBy(
    //                 'id'
    //             )
    //             ->get();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Edit View
    //     |--------------------------------------------------------------------------
    //     */

    //     return view(
    //         'dashboard.employee.edit',
    //         compact(
    //             'employee',
    //             'offices',
    //             'teamLeaders',
    //             'leaveAuthorities',
    //             'departments',
    //             'employeeAddress',
    //             'employeeFamily',
    //             'employeeNominee',
    //             'employeeQualifications'
    //         )
    //     );
    // }


    // public function update(Request $request, User $employee)
    // {
    //     $loggedInUser = $request->user();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 1. Resolve target office and authorize employee access
    //     |--------------------------------------------------------------------------
    //     */

    //     if ($loggedInUser->hasRole('super_admin')) {
    //         $targetOfficeId = (int) (
    //             $request->input('office_id')
    //             ?: $employee->office_id
    //         );

    //         if (
    //             !$targetOfficeId ||
    //             !Office::query()->whereKey($targetOfficeId)->exists()
    //         ) {
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

    //         if (
    //             !$employee->office_id ||
    //             !$ownerOfficeIds->contains((int) $employee->office_id)
    //         ) {
    //             abort(403, 'This employee does not belong to your office.');
    //         }

    //         $targetOfficeId = (int) (
    //             $request->input('office_id')
    //             ?: $employee->office_id
    //         );

    //         if (
    //             !$targetOfficeId ||
    //             !$ownerOfficeIds->contains($targetOfficeId)
    //         ) {
    //             return back()
    //                 ->withErrors([
    //                     'office_id' => 'Invalid office selected.',
    //                 ])
    //                 ->withInput();
    //         }
    //     } else {
    //         $targetOfficeId = (int) $loggedInUser->activeOfficeId();

    //         if (!$targetOfficeId) {
    //             return back()
    //                 ->with('error', 'Please select an office first.')
    //                 ->withInput();
    //         }

    //         if ((int) $employee->office_id !== $targetOfficeId) {
    //             abort(
    //                 403,
    //                 'This employee does not belong to the selected office.'
    //             );
    //         }
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 2. Same normalization used in EmployeeRequest
    //     |--------------------------------------------------------------------------
    //     */

    //     $request->merge([
    //         'status' => $request->input('status', '1'),

    //         'adhar_number' => $request->filled('adhar_number')
    //             ? preg_replace(
    //                 '/\D+/',
    //                 '',
    //                 (string) $request->input('adhar_number')
    //             )
    //             : null,

    //         'pan_number' => $request->filled('pan_number')
    //             ? strtoupper(trim((string) $request->input('pan_number')))
    //             : null,

    //         'ifsc_code' => $request->filled('ifsc_code')
    //             ? strtoupper(trim((string) $request->input('ifsc_code')))
    //             : null,

    //         'account_number' => $request->filled('account_number')
    //             ? trim((string) $request->input('account_number'))
    //             : null,

    //         'upi_id' => $request->filled('upi_id')
    //             ? strtolower(trim((string) $request->input('upi_id')))
    //             : null,

    //         'email' => $request->filled('email')
    //             ? strtolower(trim((string) $request->input('email')))
    //             : null,

    //         'phone' => $request->filled('phone')
    //             ? trim((string) $request->input('phone'))
    //             : null,

    //         'name' => trim((string) $request->input('name')),
    //     ]);

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 3. Validation based on the supplied EmployeeRequest
    //     |--------------------------------------------------------------------------
    //     |
    //     | Unique rules ignore the current employee because this is an update.
    //     |
    //     */

    //     $validated = $request->validate(
    //         [
    //             'name' => [
    //                 'required',
    //                 'string',
    //                 'max:255',
    //             ],

    //             'email' => [
    //                 'nullable',
    //                 'email',
    //                 'max:255',
    //                 Rule::unique('users', 'email')->ignore($employee->id),
    //             ],

    //             'phone' => [
    //                 'required',
    //                 'string',
    //                 'max:20',
    //             ],

    //             'dob' => [
    //                 'nullable',
    //                 'date',
    //                 'before_or_equal:today',
    //             ],

    //             'joining_date' => [
    //                 'nullable',
    //                 'date',
    //             ],

    //             'employee_id' => [
    //                 'nullable',
    //                 'string',
    //                 'max:255',
    //                 Rule::unique('users', 'employee_id')->ignore($employee->id),
    //             ],

    //             'address' => [
    //                 'nullable',
    //                 'string',
    //                 'max:5000',
    //             ],

    //             'department_id' => [
    //                 'nullable',
    //                 'integer',
    //                 'exists:departments,id',
    //             ],

    //             'designation' => [
    //                 'nullable',
    //                 'string',
    //                 'max:255',
    //             ],

    //             'responsibility' => [
    //                 'nullable',
    //                 'string',
    //                 'max:5000',
    //             ],

    //             'salary' => [
    //                 'nullable',
    //                 'numeric',
    //                 'min:0',
    //                 'max:99999999.99',
    //             ],

    //             'check_in_time' => [
    //                 'required',
    //                 'date_format:H:i',
    //             ],

    //             'check_out_time' => [
    //                 'required',
    //                 'date_format:H:i',
    //             ],

    //             'break' => [
    //                 'nullable',
    //                 'integer',
    //                 'min:0',
    //                 'max:1440',
    //             ],

    //             'location_required' => [
    //                 'required',
    //                 Rule::in([
    //                     'yes',
    //                     'no',
    //                 ]),
    //             ],

    //             'status' => [
    //                 'required',
    //                 Rule::in([
    //                     '0',
    //                     '1',
    //                     0,
    //                     1,
    //                 ]),
    //             ],

    //             'adhar_number' => [
    //                 'nullable',
    //                 'digits:12',
    //                 Rule::unique('users', 'adhar_number')->ignore($employee->id),
    //             ],

    //             'pan_number' => [
    //                 'nullable',
    //                 'string',
    //                 'size:10',
    //                 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/',
    //                 Rule::unique('users', 'pan_number')->ignore($employee->id),
    //             ],

    //             'account_holder_name' => [
    //                 'nullable',
    //                 'string',
    //                 'max:255',
    //             ],

    //             'bank_name' => [
    //                 'nullable',
    //                 'string',
    //                 'max:255',
    //             ],

    //             'bank_branch' => [
    //                 'nullable',
    //                 'string',
    //                 'max:255',
    //             ],

    //             'account_number' => [
    //                 'nullable',
    //                 'string',
    //                 'min:6',
    //                 'max:30',
    //             ],

    //             'ifsc_code' => [
    //                 'nullable',
    //                 'string',
    //                 'size:11',
    //                 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
    //             ],

    //             'account_type' => [
    //                 'nullable',
    //                 Rule::in([
    //                     'savings',
    //                     'current',
    //                     'salary',
    //                     'other',
    //                 ]),
    //             ],

    //             'upi_id' => [
    //                 'nullable',
    //                 'string',
    //                 'max:100',
    //                 'regex:/^[a-zA-Z0-9.\-_]{2,}@[a-zA-Z]{2,}$/',
    //             ],

    //             'uan_number' => [
    //                 'nullable',
    //                 'string',
    //                 'max:30',
    //             ],

    //             'esic_number' => [
    //                 'nullable',
    //                 'string',
    //                 'max:30',
    //             ],

    //             'photo' => [
    //                 'nullable',
    //                 'image',
    //                 'mimes:jpg,jpeg,png,webp',
    //                 'max:5120',
    //             ],

    //             'aadhar_attachment' => [
    //                 'nullable',
    //                 'file',
    //                 'mimes:jpg,jpeg,png,webp,pdf',
    //                 'max:5120',
    //             ],

    //             'pan_attachment' => [
    //                 'nullable',
    //                 'file',
    //                 'mimes:jpg,jpeg,png,webp,pdf',
    //                 'max:5120',
    //             ],

    //             'other_attachment' => [
    //                 'nullable',
    //                 'file',
    //                 'mimes:jpg,jpeg,png,webp,pdf,doc,docx',
    //                 'max:10240',
    //             ],

    //             'basic_salary' => [
    //                 'nullable',
    //                 'numeric',
    //                 'min:0',
    //             ],

    //             'house_rent_allowance' => [
    //                 'nullable',
    //                 'numeric',
    //                 'min:0',
    //             ],

    //             'transport_allowance' => [
    //                 'nullable',
    //                 'numeric',
    //                 'min:0',
    //             ],

    //             'medical_allowance' => [
    //                 'nullable',
    //                 'numeric',
    //                 'min:0',
    //             ],

    //             'special_allowance' => [
    //                 'nullable',
    //                 'numeric',
    //                 'min:0',
    //             ],

    //             'dearness_allowance' => [
    //                 'nullable',
    //                 'numeric',
    //                 'min:0',
    //             ],

    //             'relieving_charge' => [
    //                 'nullable',
    //                 'numeric',
    //                 'min:0',
    //             ],

    //             'additional_allowance' => [
    //                 'nullable',
    //                 'numeric',
    //                 'min:0',
    //             ],

    //             'provident_fund' => [
    //                 'nullable',
    //                 'numeric',
    //                 'min:0',
    //                 'max:100',
    //             ],

    //             'employee_state_insurance_corporation' => [
    //                 'nullable',
    //                 'numeric',
    //                 'min:0',
    //                 'max:100',
    //             ],

    //             /*
    //             |--------------------------------------------------------------------------
    //             | Edit-page hierarchy fields
    //             |--------------------------------------------------------------------------
    //             */

    //             'role' => [
    //                 'required',
    //                 Rule::in([
    //                     'admin',
    //                     'team_leader',
    //                     'employee',
    //                 ]),
    //             ],

    //             'office_id' => [
    //                 'nullable',
    //                 'integer',
    //                 'exists:offices,id',
    //             ],

    //             'team_leader_id' => [
    //                 'nullable',
    //                 'integer',
    //                 'exists:users,id',
    //                 Rule::notIn([
    //                     (int) $employee->id,
    //                 ]),
    //             ],

    //             'leave_authority_id' => [
    //                 'nullable',
    //                 'integer',
    //                 'exists:users,id',
    //                 Rule::notIn([
    //                     (int) $employee->id,
    //                 ]),
    //             ],

    //             /*
    //             |--------------------------------------------------------------------------
    //             | Educational Qualifications
    //             |--------------------------------------------------------------------------
    //             */

    //             'qualifications' => [
    //                 'nullable',
    //                 'array',
    //             ],

    //             'qualifications.*.id' => [
    //                 'nullable',
    //                 'integer',
    //                 Rule::exists('employee_educational_qualifications', 'id')
    //                     ->where(fn ($query) => $query->where('user_id', $employee->id)),
    //             ],

    //             'qualifications.*.qualification' => [
    //                 'nullable',
    //                 'string',
    //                 'max:255',
    //             ],

    //             'qualifications.*.course_name' => [
    //                 'nullable',
    //                 'string',
    //                 'max:255',
    //             ],

    //             'qualifications.*.board_university' => [
    //                 'nullable',
    //                 'string',
    //                 'max:255',
    //             ],

    //             'qualifications.*.institute_name' => [
    //                 'nullable',
    //                 'string',
    //                 'max:255',
    //             ],

    //             'qualifications.*.passing_year' => [
    //                 'nullable',
    //                 'integer',
    //                 'min:1950',
    //                 'max:' . (now()->year + 10),
    //             ],

    //             'qualifications.*.result' => [
    //                 'nullable',
    //                 'string',
    //                 'max:100',
    //             ],

    //             'qualifications.*.document_type' => [
    //                 'nullable',
    //                 Rule::in([
    //                     'marksheet',
    //                     'degree',
    //                     'certificate',
    //                 ]),
    //             ],

    //             'qualifications.*.document' => [
    //                 'nullable',
    //                 'file',
    //                 'mimes:jpg,jpeg,png,webp,pdf',
    //                 'max:5120',
    //             ],

    //             'deleted_qualification_ids' => [
    //                 'nullable',
    //                 'array',
    //             ],

    //             'deleted_qualification_ids.*' => [
    //                 'integer',
    //                 Rule::exists('employee_educational_qualifications', 'id')
    //                     ->where(fn ($query) => $query->where('user_id', $employee->id)),
    //             ],

    //             ...$this->employeeExtraProfileRules(),
    //         ],
    //         [
    //             'status.required' =>
    //                 'Please select employee status.',

    //             'status.in' =>
    //                 'Employee status must be Active or Inactive.',

    //             'email.unique' =>
    //                 'This email address is already being used by another employee.',

    //             'employee_id.unique' =>
    //                 'This employee ID is already registered.',

    //             'adhar_number.digits' =>
    //                 'Aadhaar number must contain exactly 12 digits.',

    //             'adhar_number.unique' =>
    //                 'This Aadhaar number is already registered.',

    //             'pan_number.size' =>
    //                 'PAN number must contain exactly 10 characters.',

    //             'pan_number.regex' =>
    //                 'Please enter a valid PAN number, for example ABCDE1234F.',

    //             'pan_number.unique' =>
    //                 'This PAN number is already registered.',

    //             'ifsc_code.size' =>
    //                 'IFSC code must contain exactly 11 characters.',

    //             'ifsc_code.regex' =>
    //                 'Please enter a valid IFSC code, for example SBIN0001234.',

    //             'upi_id.regex' =>
    //                 'Please enter a valid UPI ID, for example name@bank.',

    //             'team_leader_id.not_in' =>
    //                 'An employee cannot be assigned as their own reporting manager.',

    //             'leave_authority_id.not_in' =>
    //                 'An employee cannot be assigned as their own leave authority.',

    //             'pin_code.regex' =>
    //                 'PIN code must contain exactly 6 digits and cannot start with 0.',

    //             'spouse_name.required_if' =>
    //                 'Spouse name is required when marital status is Married.',

    //             'nominee_name.required_if' =>
    //                 'Nominee name is required when nominee option is Yes.',

    //             'nominee_relationship.required_if' =>
    //                 'Nominee relationship is required when nominee option is Yes.',

    //             'nominee_aadhaar_number.digits' =>
    //                 'Nominee Aadhaar number must contain exactly 12 digits.',
    //         ]
    //     );

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 4. Validate reporting manager and leave authority
    //     |--------------------------------------------------------------------------
    //     */

    //     $targetOffice = Office::query()
    //         ->select([
    //             'id',
    //             'owner_id',
    //         ])
    //         ->find($targetOfficeId);

    //     if (!$targetOffice) {
    //         return back()
    //             ->withErrors([
    //                 'office_id' => 'The selected office was not found.',
    //             ])
    //             ->withInput();
    //     }

    //     $authorityIds = collect([
    //         'team_leader_id' =>
    //             $validated['team_leader_id'] ?? null,

    //         'leave_authority_id' =>
    //             $validated['leave_authority_id'] ?? null,
    //     ])->filter()
    //         ->map(fn ($id) => (int) $id)
    //         ->unique()
    //         ->values();

    //     if ($authorityIds->isNotEmpty()) {
    //         $validManagerIds = User::query()
    //             ->whereIn('id', $authorityIds)
    //             ->where(function ($query) use (
    //                 $targetOfficeId,
    //                 $targetOffice
    //             ) {
    //                 $query->where(function ($officeUserQuery) use (
    //                     $targetOfficeId
    //                 ) {
    //                     $officeUserQuery
    //                         ->where('office_id', $targetOfficeId)
    //                         ->where('status', '1')
    //                         ->whereHas('roles', function ($roleQuery) {
    //                             $roleQuery->whereIn('roles.name', [
    //                                 'admin',
    //                                 'team_leader',
    //                             ]);
    //                         });
    //                 });

    //                 if (!empty($targetOffice->owner_id)) {
    //                     $query->orWhere(
    //                         'id',
    //                         (int) $targetOffice->owner_id
    //                     );
    //                 }
    //             })
    //             ->pluck('id')
    //             ->map(fn ($id) => (int) $id);

    //         if (
    //             !empty($validated['team_leader_id']) &&
    //             !$validManagerIds->contains(
    //                 (int) $validated['team_leader_id']
    //             )
    //         ) {
    //             return back()
    //                 ->withErrors([
    //                     'team_leader_id' =>
    //                         'Please select a valid active reporting manager for the selected office.',
    //                 ])
    //                 ->withInput();
    //         }

    //         if (
    //             !empty($validated['leave_authority_id']) &&
    //             !$validManagerIds->contains(
    //                 (int) $validated['leave_authority_id']
    //             )
    //         ) {
    //             return back()
    //                 ->withErrors([
    //                     'leave_authority_id' =>
    //                         'Please select a valid active leave authority for the selected office.',
    //                 ])
    //                 ->withInput();
    //         }
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 5. Calculate office time with overnight-shift support
    //     |--------------------------------------------------------------------------
    //     */

    //     try {
    //         $checkInTime = Carbon::createFromFormat(
    //             'H:i',
    //             $validated['check_in_time']
    //         );

    //         $checkOutTime = Carbon::createFromFormat(
    //             'H:i',
    //             $validated['check_out_time']
    //         );
    //     } catch (\Throwable $exception) {
    //         return back()
    //             ->withErrors([
    //                 'check_in_time' =>
    //                     'Please enter valid check-in and check-out times.',
    //             ])
    //             ->withInput();
    //     }

    //     if ($checkOutTime->lessThanOrEqualTo($checkInTime)) {
    //         $checkOutTime->addDay();
    //     }

    //     $officeMinutes =
    //         $checkInTime->diffInMinutes($checkOutTime);

    //     $employeeStatus =
    //         (string) $validated['status'] === '1'
    //             ? '1'
    //             : '0';

    //     $structuredAddress = $this->employeeAddressPayload($validated);
    //     $formattedAddress = $this->formattedEmployeeAddress($structuredAddress);

    //     $newUploadedFiles = [];
    //     $oldFilesToDelete = [];

    //     DB::beginTransaction();

    //     try {
    //         /*
    //         |--------------------------------------------------------------------------
    //         | 6. Update employee details
    //         |--------------------------------------------------------------------------
    //         */

    //         $employee->forceFill([
    //             'name' => $validated['name'],

    //             'email' => $validated['email'] ?? null,
    //             'phone' => $validated['phone'],

    //             'dob' => $validated['dob'] ?? null,
    //             'joining_date' => $validated['joining_date'] ?? null,
    //             'employee_id' => $validated['employee_id'] ?? null,

    //             'address' => $formattedAddress
    //                 ?? $this->cleanNullableString(
    //                     $validated['address'] ?? null
    //                 ),

    //             'department_id' =>
    //                 $validated['department_id'] ?? null,

    //             'designation' =>
    //                 $validated['designation'] ?? null,

    //             'responsibility' =>
    //                 $validated['responsibility'] ?? null,

    //             'salary' => array_key_exists('salary', $validated)
    //                 && $validated['salary'] !== null
    //                     ? (float) $validated['salary']
    //                     : null,

    //             'check_in_time' =>
    //                 $validated['check_in_time'],

    //             'check_out_time' =>
    //                 $validated['check_out_time'],

    //             'office_time' => $officeMinutes,

    //             'break' =>
    //                 array_key_exists('break', $validated) &&
    //                 $validated['break'] !== null
    //                     ? (int) $validated['break']
    //                     : null,

    //             'location_required' =>
    //                 $validated['location_required'],

    //             'status' => $employeeStatus,
    //             'office_id' => $targetOfficeId,

    //             'team_leader_id' =>
    //                 $validated['team_leader_id'] ?? null,

    //             'leave_authority_id' =>
    //                 $validated['leave_authority_id'] ?? null,

    //             'adhar_number' =>
    //                 $validated['adhar_number'] ?? null,

    //             'pan_number' =>
    //                 $validated['pan_number'] ?? null,

    //             'account_holder_name' =>
    //                 $validated['account_holder_name'] ?? null,

    //             'bank_name' =>
    //                 $validated['bank_name'] ?? null,

    //             'bank_branch' =>
    //                 $validated['bank_branch'] ?? null,

    //             'account_number' =>
    //                 $validated['account_number'] ?? null,

    //             'ifsc_code' =>
    //                 $validated['ifsc_code'] ?? null,

    //             'account_type' =>
    //                 $validated['account_type'] ?? null,

    //             'upi_id' =>
    //                 $validated['upi_id'] ?? null,

    //             'uan_number' =>
    //                 $validated['uan_number'] ?? null,

    //             'esic_number' =>
    //                 $validated['esic_number'] ?? null,
    //         ]);

    //         /*
    //         |--------------------------------------------------------------------------
    //         | 7. Replace files safely
    //         |--------------------------------------------------------------------------
    //         */

    //         $fileUploadMap = [
    //             'photo' => [
    //                 'directory' => 'photos',
    //                 'column' => 'photo',
    //             ],

    //             'aadhar_attachment' => [
    //                 'directory' => 'aadhar_attachments',
    //                 'column' => 'aadhar_attachment',
    //             ],

    //             'pan_attachment' => [
    //                 'directory' => 'pan_attachments',
    //                 'column' => 'pan_attachment',
    //             ],

    //             'other_attachment' => [
    //                 'directory' => 'other_attachments',
    //                 'column' => 'other_attachment',
    //             ],
    //         ];

    //         foreach ($fileUploadMap as $inputName => $fileConfig) {
    //             if (!$request->hasFile($inputName)) {
    //                 continue;
    //             }

    //             $uploadedFile = $request->file($inputName);

    //             if (!$uploadedFile || !$uploadedFile->isValid()) {
    //                 throw new \RuntimeException(
    //                     "The {$inputName} upload failed."
    //                 );
    //             }

    //             $newFilePath = $uploadedFile->store(
    //                 $fileConfig['directory'],
    //                 'public'
    //             );

    //             if (!$newFilePath) {
    //                 throw new \RuntimeException(
    //                     "Unable to upload {$inputName}."
    //                 );
    //             }

    //             $oldFilePath =
    //                 $employee->getOriginal($fileConfig['column']);

    //             $newUploadedFiles[] = $newFilePath;

    //             if (
    //                 !empty($oldFilePath) &&
    //                 $oldFilePath !== $newFilePath
    //             ) {
    //                 $oldFilesToDelete[] = $oldFilePath;
    //             }

    //             $employee->{$fileConfig['column']} = $newFilePath;
    //         }

    //         $employee->save();

    //         /*
    //         |--------------------------------------------------------------------------
    //         | 8. Structured address, marital/spouse and nominee details
    //         |--------------------------------------------------------------------------
    //         */

    //         $this->saveEmployeeExtraProfile(
    //             $employee,
    //             $validated
    //         );

    //         /*
    //         |--------------------------------------------------------------------------
    //         | 9. Update Educational Qualifications
    //         |--------------------------------------------------------------------------
    //         |
    //         | Existing qualification:
    //         | - update same row
    //         | - keep old document if no new file uploaded
    //         | - replace old document safely if a new file is uploaded
    //         |
    //         | New qualification:
    //         | - create new row
    //         |
    //         | Removed qualification:
    //         | - delete DB row in transaction
    //         | - delete document only after successful commit
    //         |
    //         */

    //         $submittedQualifications = $request->input(
    //             'qualifications',
    //             []
    //         );

    //         foreach ($submittedQualifications as $index => $qualificationInput) {

    //             $qualificationId = !empty($qualificationInput['id'])
    //                 ? (int) $qualificationInput['id']
    //                 : null;

    //             $qualificationName = $this->cleanNullableString(
    //                 $qualificationInput['qualification'] ?? null
    //             );

    //             $courseName = $this->cleanNullableString(
    //                 $qualificationInput['course_name'] ?? null
    //             );

    //             $boardUniversity = $this->cleanNullableString(
    //                 $qualificationInput['board_university'] ?? null
    //             );

    //             $instituteName = $this->cleanNullableString(
    //                 $qualificationInput['institute_name'] ?? null
    //             );

    //             $passingYear = $qualificationInput['passing_year'] ?? null;

    //             $result = $this->cleanNullableString(
    //                 $qualificationInput['result'] ?? null
    //             );

    //             $documentType = $this->cleanNullableString(
    //                 $qualificationInput['document_type'] ?? null
    //             );

    //             $documentInputName = "qualifications.$index.document";

    //             $hasNewDocument = $request->hasFile(
    //                 $documentInputName
    //             );

    //             /*
    //             |--------------------------------------------------------------------------
    //             | Completely empty new row can be ignored
    //             |--------------------------------------------------------------------------
    //             */

    //             $hasAnyQualificationValue =
    //                 $qualificationId !== null ||
    //                 $qualificationName !== null ||
    //                 $courseName !== null ||
    //                 $boardUniversity !== null ||
    //                 $instituteName !== null ||
    //                 filled($passingYear) ||
    //                 $result !== null ||
    //                 $documentType !== null ||
    //                 $hasNewDocument;

    //             if (!$hasAnyQualificationValue) {
    //                 continue;
    //             }

    //             /*
    //             |--------------------------------------------------------------------------
    //             | qualification column is required for every non-empty row
    //             |--------------------------------------------------------------------------
    //             */

    //             if ($qualificationName === null) {
    //                 throw \Illuminate\Validation\ValidationException::withMessages([
    //                     "qualifications.$index.qualification" =>
    //                         'Please select qualification.',
    //                 ]);
    //             }

    //             /*
    //             |--------------------------------------------------------------------------
    //             | Find existing row or prepare new row
    //             |--------------------------------------------------------------------------
    //             */

    //             if ($qualificationId !== null) {
    //                 $qualificationModel =
    //                     EmployeeEducationalQualification::query()
    //                         ->where('user_id', $employee->id)
    //                         ->whereKey($qualificationId)
    //                         ->firstOrFail();
    //             } else {
    //                 $qualificationModel =
    //                     new EmployeeEducationalQualification();

    //                 $qualificationModel->user_id =
    //                     $employee->id;
    //             }

    //             /*
    //             |--------------------------------------------------------------------------
    //             | Replace Marksheet / Degree document safely
    //             |--------------------------------------------------------------------------
    //             */

    //             if ($hasNewDocument) {

    //                 $uploadedQualificationDocument =
    //                     $request->file(
    //                         $documentInputName
    //                     );

    //                 if (
    //                     !$uploadedQualificationDocument ||
    //                     !$uploadedQualificationDocument->isValid()
    //                 ) {
    //                     throw new \RuntimeException(
    //                         "Qualification document upload failed for row " .
    //                         ($index + 1) . '.'
    //                     );
    //                 }

    //                 $newQualificationDocumentPath =
    //                     $uploadedQualificationDocument->store(
    //                         "employee_qualifications/{$employee->id}",
    //                         'public'
    //                     );

    //                 if (!$newQualificationDocumentPath) {
    //                     throw new \RuntimeException(
    //                         "Unable to upload qualification document for row " .
    //                         ($index + 1) . '.'
    //                     );
    //                 }

    //                 /*
    //                 * Track the new file so it can be removed if transaction fails.
    //                 */
    //                 $newUploadedFiles[] =
    //                     $newQualificationDocumentPath;

    //                 /*
    //                 * Existing document is not deleted now.
    //                 * It is deleted only after DB commit succeeds.
    //                 */
    //                 if (
    //                     $qualificationModel->exists &&
    //                     !empty($qualificationModel->document_path) &&
    //                     $qualificationModel->document_path !==
    //                         $newQualificationDocumentPath
    //                 ) {
    //                     $oldFilesToDelete[] =
    //                         $qualificationModel->document_path;
    //                 }

    //                 $qualificationModel->document_path =
    //                     $newQualificationDocumentPath;
    //             }

    //             /*
    //             |--------------------------------------------------------------------------
    //             | Update qualification fields
    //             |--------------------------------------------------------------------------
    //             */

    //             $qualificationModel->qualification =
    //                 $qualificationName;

    //             $qualificationModel->course_name =
    //                 $courseName;

    //             $qualificationModel->board_university =
    //                 $boardUniversity;

    //             $qualificationModel->institute_name =
    //                 $instituteName;

    //             $qualificationModel->passing_year =
    //                 filled($passingYear)
    //                     ? (int) $passingYear
    //                     : null;

    //             $qualificationModel->result =
    //                 $result;

    //             $qualificationModel->document_type =
    //                 $documentType;

    //             $qualificationModel->save();
    //         }

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Delete removed educational qualifications
    //         |--------------------------------------------------------------------------
    //         */

    //         $deletedQualificationIds = collect(
    //             $request->input(
    //                 'deleted_qualification_ids',
    //                 []
    //             )
    //         )
    //             ->filter()
    //             ->map(fn ($id) => (int) $id)
    //             ->unique()
    //             ->values();

    //         if ($deletedQualificationIds->isNotEmpty()) {

    //             $qualificationsToDelete =
    //                 EmployeeEducationalQualification::query()
    //                     ->where('user_id', $employee->id)
    //                     ->whereIn('id', $deletedQualificationIds)
    //                     ->get();

    //             foreach ($qualificationsToDelete as $qualificationToDelete) {

    //                 if (
    //                     !empty(
    //                         $qualificationToDelete->document_path
    //                     )
    //                 ) {
    //                     $oldFilesToDelete[] =
    //                         $qualificationToDelete->document_path;
    //                 }

    //                 $qualificationToDelete->delete();
    //             }
    //         }

    //         /*
    //         |--------------------------------------------------------------------------
    //         | 10. Update Spatie role
    //         |--------------------------------------------------------------------------
    //         */

    //         $employee->syncRoles([
    //             $validated['role'],
    //         ]);

    //         /*
    //         |--------------------------------------------------------------------------
    //         | 11. Update salary structure
    //         |--------------------------------------------------------------------------
    //         */

    //         $basicSalary =
    //             (float) ($validated['basic_salary'] ?? 0);

    //         $houseRentAllowance =
    //             (float) ($validated['house_rent_allowance'] ?? 0);

    //         $transportAllowance =
    //             (float) ($validated['transport_allowance'] ?? 0);

    //         $medicalAllowance =
    //             (float) ($validated['medical_allowance'] ?? 0);

    //         $specialAllowance =
    //             (float) ($validated['special_allowance'] ?? 0);

    //         $dearnessAllowance =
    //             (float) ($validated['dearness_allowance'] ?? 0);

    //         $relievingCharge =
    //             (float) ($validated['relieving_charge'] ?? 0);

    //         $additionalAllowance =
    //             (float) ($validated['additional_allowance'] ?? 0);

    //         $providentFund =
    //             (float) ($validated['provident_fund'] ?? 0);

    //         $esic =
    //             (float) (
    //                 $validated[
    //                     'employee_state_insurance_corporation'
    //                 ] ?? 0
    //             );

    //         $totalSalary =
    //             $basicSalary +
    //             $houseRentAllowance +
    //             $transportAllowance +
    //             $medicalAllowance +
    //             $specialAllowance +
    //             $dearnessAllowance +
    //             $relievingCharge +
    //             $additionalAllowance;

    //         UserSalary::updateOrCreate(
    //             [
    //                 'user_id' => $employee->id,
    //             ],
    //             [
    //                 'basic_salary' => $basicSalary,
    //                 'house_rent_allowance' => $houseRentAllowance,
    //                 'transport_allowance' => $transportAllowance,
    //                 'medical_allowance' => $medicalAllowance,
    //                 'special_allowance' => $specialAllowance,
    //                 'dearness_allowance' => $dearnessAllowance,
    //                 'relieving_charge' => $relievingCharge,
    //                 'additional_allowance' => $additionalAllowance,
    //                 'provident_fund' => $providentFund,
    //                 'employee_state_insurance_corporation' => $esic,
    //                 'total_salary' => $totalSalary,
    //             ]
    //         );

    //         DB::commit();

    //         /*
    //         |--------------------------------------------------------------------------
    //         | 12. Delete replaced old files after successful commit
    //         |--------------------------------------------------------------------------
    //         */

    //         foreach (array_unique($oldFilesToDelete) as $oldFilePath) {
    //             try {
    //                 if (
    //                     Storage::disk('public')->exists($oldFilePath)
    //                 ) {
    //                     Storage::disk('public')->delete($oldFilePath);
    //                 }
    //             } catch (\Throwable $fileDeleteException) {
    //                 report($fileDeleteException);
    //             }
    //         }

    //         return redirect()
    //             ->route('employee.index')
    //             ->with(
    //                 'success',
    //                 'Employee record updated successfully.'
    //             );
    //     } catch (\Throwable $exception) 
    //     {
    //         DB::rollBack();

    //         /*
    //         * Only files uploaded during this failed request are removed.
    //         * Existing employee files remain untouched.
    //         */
    //         foreach (array_unique($newUploadedFiles) as $newUploadedFile) {
    //             try {
    //                 if (
    //                     Storage::disk('public')->exists($newUploadedFile)
    //                 ) {
    //                     Storage::disk('public')->delete($newUploadedFile);
    //                 }
    //             } catch (\Throwable $fileDeleteException) {
    //                 report($fileDeleteException);
    //             }
    //         }

    //         report($exception);

    //         return back()->with('error','Employee record could not be updated. Please try again.')->withInput();
    //     }
        
    // }



    public function edit(Request $request, User $employee)
    {
        $loggedInUser = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Resolve accessible offices
        |--------------------------------------------------------------------------
        */

        if ($loggedInUser->hasRole('super_admin')) {

            $offices = Office::query()
                ->select([
                    'id',
                    'name',
                    'owner_id',
                ])
                ->orderBy('name')
                ->get();

            $allowedOfficeIds = $offices
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values();

        } elseif ($loggedInUser->hasRole('owner')) {

            $ownerOfficeIds = Office::query()
                ->where('owner_id', $loggedInUser->id)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values();

            if ($ownerOfficeIds->isEmpty()) {
                return back()->with(
                    'error',
                    'No office found for this owner.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Employee must belong to owner's office
            |--------------------------------------------------------------------------
            */

            if (
                !$ownerOfficeIds->contains(
                    (int) $employee->office_id
                )
            ) {
                abort(
                    403,
                    'This employee does not belong to your office.'
                );
            }

            $offices = Office::query()
                ->select([
                    'id',
                    'name',
                    'owner_id',
                ])
                ->whereIn(
                    'id',
                    $ownerOfficeIds
                )
                ->orderBy('name')
                ->get();

            $allowedOfficeIds = $ownerOfficeIds;

        } else {

            /*
            |--------------------------------------------------------------------------
            | Admin / Team Leader active office
            |--------------------------------------------------------------------------
            */

            $activeOfficeId = (int)
                $loggedInUser->activeOfficeId();

            if (!$activeOfficeId) {
                return back()->with(
                    'error',
                    'Please select an office first.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Employee must belong to active office
            |--------------------------------------------------------------------------
            */

            if (
                (int) $employee->office_id
                !== $activeOfficeId
            ) {
                abort(
                    403,
                    'This employee does not belong to the selected office.'
                );
            }

            $offices = Office::query()
                ->select([
                    'id',
                    'name',
                    'owner_id',
                ])
                ->whereKey($activeOfficeId)
                ->get();

            $allowedOfficeIds = collect([
                $activeOfficeId,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Office Owner IDs
        |--------------------------------------------------------------------------
        */

        $officeOwnerIds = $offices
            ->pluck('owner_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | Reporting Managers / Leave Authorities
        |--------------------------------------------------------------------------
        |
        | Valid candidates:
        | 1. Active Admin of accessible office
        | 2. Active Team Leader of accessible office
        | 3. Owner of accessible office
        |
        | Current employee is always excluded.
        |
        */

        $teamLeaders = User::query()
            ->select([
                'users.id',
                'users.name',
                'users.office_id',
                'users.status',
            ])
            ->whereKeyNot($employee->id)
            ->where(function ($userQuery) use (
                $allowedOfficeIds,
                $officeOwnerIds
            ) {
                /*
                | Admin / Team Leader
                */
                $userQuery->where(function ($officeUserQuery) use (
                    $allowedOfficeIds
                ) {
                    $officeUserQuery
                        ->where('users.status', '1')
                        ->whereIn(
                            'users.office_id',
                            $allowedOfficeIds
                        )
                        ->whereHas('roles', function ($roleQuery) {
                            $roleQuery->whereIn('roles.name', [
                                'admin',
                                'team_leader',
                            ]);
                        });
                });

                /*
                | Office Owner
                */
                if ($officeOwnerIds->isNotEmpty()) {
                    $userQuery->orWhere(function ($ownerQuery) use (
                        $officeOwnerIds
                    ) {
                        $ownerQuery
                            ->whereIn(
                                'users.id',
                                $officeOwnerIds
                            )
                            ->whereHas('roles', function ($roleQuery) {
                                $roleQuery->where(
                                    'roles.name',
                                    'owner'
                                );
                            });
                    });
                }
            })
            ->with([
                'office:id,name',
                'roles:id,name',
            ])
            ->orderBy('users.name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Attach selectable office IDs
        |--------------------------------------------------------------------------
        |
        | Owner can be valid for every office owned by them.
        | Admin / Team Leader is valid for users.office_id.
        |
        */

        $ownerOfficeMap = $offices
            ->filter(fn ($office) => !empty($office->owner_id))
            ->groupBy(fn ($office) => (int) $office->owner_id)
            ->map(function ($ownerOffices) {
                return $ownerOffices
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->unique()
                    ->values()
                    ->all();
            });

        $teamLeaders->each(function ($manager) use ($ownerOfficeMap) {
            if ($manager->hasRole('owner')) {
                $manager->selectable_office_ids = $ownerOfficeMap->get(
                    (int) $manager->id,
                    []
                );
            } else {
                $manager->selectable_office_ids = $manager->office_id
                    ? [(int) $manager->office_id]
                    : [];
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Leave Authorities
        |--------------------------------------------------------------------------
        */

        $leaveAuthorities = $teamLeaders;

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
        | Employee Relations
        |--------------------------------------------------------------------------
        */

        $employee->load([
            'office:id,name',
            'department:id,name',
            'teamLeader:id,name',
            'leaveAuthority:id,name',
            'userSalary',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Structured Employee Address
        |--------------------------------------------------------------------------
        */

        $employeeAddress = EmployeeAddress::query()
            ->where(
                'user_id',
                $employee->id
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Marital / Spouse Details
        |--------------------------------------------------------------------------
        */

        $employeeFamily = EmployeeFamilyDetail::query()
            ->where(
                'user_id',
                $employee->id
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Employee Family Members
        |--------------------------------------------------------------------------
        |
        | Multiple rows:
        | name, relation, occupation, age
        |
        */

        $employeeFamilyMembers = EmployeeFamilyMember::query()
            ->where(
                'user_id',
                $employee->id
            )
            ->orderBy('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Nominee Details
        |--------------------------------------------------------------------------
        */

        $employeeNominee = EmployeeNominee::query()
            ->where(
                'user_id',
                $employee->id
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Educational Qualifications
        |--------------------------------------------------------------------------
        */

        $employeeQualifications =
            EmployeeEducationalQualification::query()
                ->where(
                    'user_id',
                    $employee->id
                )
                ->orderByRaw(
                    'passing_year IS NULL'
                )
                ->orderBy(
                    'passing_year'
                )
                ->orderBy(
                    'id'
                )
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Edit View
        |--------------------------------------------------------------------------
        */

        return view(
            'dashboard.employee.edit',
            compact(
                'employee',
                'offices',
                'teamLeaders',
                'leaveAuthorities',
                'departments',
                'employeeAddress',
                'employeeFamily',
                'employeeFamilyMembers',
                'employeeNominee',
                'employeeQualifications'
            )
        );
    }


    public function update(Request $request, User $employee)
    {
        $loggedInUser = $request->user();

        /*
        |--------------------------------------------------------------------------
        | 1. Resolve target office and authorize employee access
        |--------------------------------------------------------------------------
        */

        if ($loggedInUser->hasRole('super_admin')) {
            $targetOfficeId = (int) (
                $request->input('office_id')
                ?: $employee->office_id
            );

            if (
                !$targetOfficeId ||
                !Office::query()->whereKey($targetOfficeId)->exists()
            ) {
                return back()
                    ->withErrors([
                        'office_id' => 'Please select a valid office.',
                    ])
                    ->withInput();
            }
        } elseif ($loggedInUser->hasRole('owner')) {
            $ownerOfficeIds = Office::query()
                ->where('owner_id', $loggedInUser->id)
                ->pluck('id')
                ->map(fn ($id) => (int) $id);

            if ($ownerOfficeIds->isEmpty()) {
                return back()
                    ->with('error', 'No office found for this owner.')
                    ->withInput();
            }

            if (
                !$employee->office_id ||
                !$ownerOfficeIds->contains((int) $employee->office_id)
            ) {
                abort(403, 'This employee does not belong to your office.');
            }

            $targetOfficeId = (int) (
                $request->input('office_id')
                ?: $employee->office_id
            );

            if (
                !$targetOfficeId ||
                !$ownerOfficeIds->contains($targetOfficeId)
            ) {
                return back()
                    ->withErrors([
                        'office_id' => 'Invalid office selected.',
                    ])
                    ->withInput();
            }
        } else {
            $targetOfficeId = (int) $loggedInUser->activeOfficeId();

            if (!$targetOfficeId) {
                return back()
                    ->with('error', 'Please select an office first.')
                    ->withInput();
            }

            if ((int) $employee->office_id !== $targetOfficeId) {
                abort(
                    403,
                    'This employee does not belong to the selected office.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Same normalization used in EmployeeRequest
        |--------------------------------------------------------------------------
        */

        $request->merge([
            'status' => $request->input('status', '1'),

            'adhar_number' => $request->filled('adhar_number')
                ? preg_replace(
                    '/\D+/',
                    '',
                    (string) $request->input('adhar_number')
                )
                : null,

            'pan_number' => $request->filled('pan_number')
                ? strtoupper(trim((string) $request->input('pan_number')))
                : null,

            'ifsc_code' => $request->filled('ifsc_code')
                ? strtoupper(trim((string) $request->input('ifsc_code')))
                : null,

            'account_number' => $request->filled('account_number')
                ? trim((string) $request->input('account_number'))
                : null,

            'upi_id' => $request->filled('upi_id')
                ? strtolower(trim((string) $request->input('upi_id')))
                : null,

            'email' => $request->filled('email')
                ? strtolower(trim((string) $request->input('email')))
                : null,

            'phone' => $request->filled('phone')
                ? trim((string) $request->input('phone'))
                : null,

            'alternate_number' => $request->filled('alternate_number')
                ? trim((string) $request->input('alternate_number'))
                : null,

            'name' => trim((string) $request->input('name')),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. Validation based on the supplied EmployeeRequest
        |--------------------------------------------------------------------------
        |
        | Unique rules ignore the current employee because this is an update.
        |
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
                    'required',
                    'string',
                    'max:20',
                ],

                'alternate_number' => [
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
                    'required_with:last_working_date',
                    'date',
                ],

                'last_working_date' => [
                    'nullable',
                    'date',
                    'after_or_equal:joining_date',
                ],

                'employee_id' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('users', 'employee_id')->ignore($employee->id),
                ],

                'address' => [
                    'nullable',
                    'string',
                    'max:5000',
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

                'responsibility' => [
                    'nullable',
                    'string',
                    'max:5000',
                ],

                'salary' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:99999999.99',
                ],

                'check_in_time' => [
                    'required',
                    'date_format:H:i',
                ],

                'check_out_time' => [
                    'required',
                    'date_format:H:i',
                ],

                'break' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:1440',
                ],

                'location_required' => [
                    'required',
                    Rule::in([
                        'yes',
                        'no',
                    ]),
                ],

                'status' => [
                    'required',
                    Rule::in([
                        '0',
                        '1',
                        0,
                        1,
                    ]),
                ],

                'adhar_number' => [
                    'nullable',
                    'digits:12',
                    Rule::unique('users', 'adhar_number')->ignore($employee->id),
                ],

                'pan_number' => [
                    'nullable',
                    'string',
                    'size:10',
                    'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/',
                    Rule::unique('users', 'pan_number')->ignore($employee->id),
                ],

                'account_holder_name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'bank_name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'bank_branch' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'account_number' => [
                    'nullable',
                    'string',
                    'min:6',
                    'max:30',
                ],

                'ifsc_code' => [
                    'nullable',
                    'string',
                    'size:11',
                    'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
                ],

                'account_type' => [
                    'nullable',
                    Rule::in([
                        'savings',
                        'current',
                        'salary',
                        'other',
                    ]),
                ],

                'upi_id' => [
                    'nullable',
                    'string',
                    'max:100',
                    'regex:/^[a-zA-Z0-9.\-_]{2,}@[a-zA-Z]{2,}$/',
                ],

                'uan_number' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                'esic_number' => [
                    'nullable',
                    'string',
                    'max:30',
                ],

                /*
                |--------------------------------------------------------------------------
                | Family Members
                |--------------------------------------------------------------------------
                */

                'family_members' => [
                    'nullable',
                    'array',
                    'max:50',
                ],

                'family_members.*.name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'family_members.*.relation' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'family_members.*.occupation' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'family_members.*.age' => [
                    'nullable',
                    'integer',
                    'min:0',
                    'max:120',
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
                    'max:10240',
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

                /*
                |--------------------------------------------------------------------------
                | Edit-page hierarchy fields
                |--------------------------------------------------------------------------
                */

                'role' => [
                    'required',
                    Rule::in([
                        'admin',
                        'team_leader',
                        'employee',
                    ]),
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
                    Rule::notIn([
                        (int) $employee->id,
                    ]),
                ],

                'leave_authority_id' => [
                    'nullable',
                    'integer',
                    'exists:users,id',
                    Rule::notIn([
                        (int) $employee->id,
                    ]),
                ],

                /*
                |--------------------------------------------------------------------------
                | Educational Qualifications
                |--------------------------------------------------------------------------
                */

                'qualifications' => [
                    'nullable',
                    'array',
                ],

                'qualifications.*.id' => [
                    'nullable',
                    'integer',
                    Rule::exists('employee_educational_qualifications', 'id')
                        ->where(fn ($query) => $query->where('user_id', $employee->id)),
                ],

                'qualifications.*.qualification' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'qualifications.*.course_name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'qualifications.*.board_university' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'qualifications.*.institute_name' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'qualifications.*.passing_year' => [
                    'nullable',
                    'integer',
                    'min:1950',
                    'max:' . (now()->year + 10),
                ],

                'qualifications.*.result' => [
                    'nullable',
                    'string',
                    'max:100',
                ],

                'qualifications.*.document_type' => [
                    'nullable',
                    Rule::in([
                        'marksheet',
                        'degree',
                        'certificate',
                    ]),
                ],

                'qualifications.*.document' => [
                    'nullable',
                    'file',
                    'mimes:jpg,jpeg,png,webp,pdf',
                    'max:5120',
                ],

                'deleted_qualification_ids' => [
                    'nullable',
                    'array',
                ],

                'deleted_qualification_ids.*' => [
                    'integer',
                    Rule::exists('employee_educational_qualifications', 'id')
                        ->where(fn ($query) => $query->where('user_id', $employee->id)),
                ],

                ...$this->employeeExtraProfileRules(),
            ],
            [
                'status.required' =>
                    'Please select employee status.',

                'status.in' =>
                    'Employee status must be Active or Inactive.',

                'email.unique' =>
                    'This email address is already being used by another employee.',

                'employee_id.unique' =>
                    'This employee ID is already registered.',

                'adhar_number.digits' =>
                    'Aadhaar number must contain exactly 12 digits.',

                'adhar_number.unique' =>
                    'This Aadhaar number is already registered.',

                'pan_number.size' =>
                    'PAN number must contain exactly 10 characters.',

                'pan_number.regex' =>
                    'Please enter a valid PAN number, for example ABCDE1234F.',

                'pan_number.unique' =>
                    'This PAN number is already registered.',

                'ifsc_code.size' =>
                    'IFSC code must contain exactly 11 characters.',

                'ifsc_code.regex' =>
                    'Please enter a valid IFSC code, for example SBIN0001234.',

                'upi_id.regex' =>
                    'Please enter a valid UPI ID, for example name@bank.',

                'team_leader_id.not_in' =>
                    'An employee cannot be assigned as their own reporting manager.',

                'leave_authority_id.not_in' =>
                    'An employee cannot be assigned as their own leave authority.',

                'pin_code.regex' =>
                    'PIN code must contain exactly 6 digits and cannot start with 0.',

                'spouse_name.required_if' =>
                    'Spouse name is required when marital status is Married.',

                'nominee_name.required_if' =>
                    'Nominee name is required when nominee option is Yes.',

                'nominee_relationship.required_if' =>
                    'Nominee relationship is required when nominee option is Yes.',

                'nominee_aadhaar_number.digits' =>
                    'Nominee Aadhaar number must contain exactly 12 digits.',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 4. Validate reporting manager and leave authority
        |--------------------------------------------------------------------------
        */

        $targetOffice = Office::query()
            ->select([
                'id',
                'owner_id',
            ])
            ->find($targetOfficeId);

        if (!$targetOffice) {
            return back()
                ->withErrors([
                    'office_id' => 'The selected office was not found.',
                ])
                ->withInput();
        }

        $authorityIds = collect([
            'team_leader_id' =>
                $validated['team_leader_id'] ?? null,

            'leave_authority_id' =>
                $validated['leave_authority_id'] ?? null,
        ])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($authorityIds->isNotEmpty()) {
            $validManagerIds = User::query()
                ->whereIn('id', $authorityIds)
                ->where(function ($query) use (
                    $targetOfficeId,
                    $targetOffice
                ) {
                    $query->where(function ($officeUserQuery) use (
                        $targetOfficeId
                    ) {
                        $officeUserQuery
                            ->where('office_id', $targetOfficeId)
                            ->where('status', '1')
                            ->whereHas('roles', function ($roleQuery) {
                                $roleQuery->whereIn('roles.name', [
                                    'admin',
                                    'team_leader',
                                ]);
                            });
                    });

                    if (!empty($targetOffice->owner_id)) {
                        $query->orWhere(
                            'id',
                            (int) $targetOffice->owner_id
                        );
                    }
                })
                ->pluck('id')
                ->map(fn ($id) => (int) $id);

            if (
                !empty($validated['team_leader_id']) &&
                !$validManagerIds->contains(
                    (int) $validated['team_leader_id']
                )
            ) {
                return back()
                    ->withErrors([
                        'team_leader_id' =>
                            'Please select a valid active reporting manager for the selected office.',
                    ])
                    ->withInput();
            }

            if (
                !empty($validated['leave_authority_id']) &&
                !$validManagerIds->contains(
                    (int) $validated['leave_authority_id']
                )
            ) {
                return back()
                    ->withErrors([
                        'leave_authority_id' =>
                            'Please select a valid active leave authority for the selected office.',
                    ])
                    ->withInput();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Calculate office time with overnight-shift support
        |--------------------------------------------------------------------------
        */

        try {
            $checkInTime = Carbon::createFromFormat(
                'H:i',
                $validated['check_in_time']
            );

            $checkOutTime = Carbon::createFromFormat(
                'H:i',
                $validated['check_out_time']
            );
        } catch (\Throwable $exception) {
            return back()
                ->withErrors([
                    'check_in_time' =>
                        'Please enter valid check-in and check-out times.',
                ])
                ->withInput();
        }

        if ($checkOutTime->lessThanOrEqualTo($checkInTime)) {
            $checkOutTime->addDay();
        }

        $officeMinutes =
            $checkInTime->diffInMinutes($checkOutTime);

        $employeeStatus =
            (string) $validated['status'] === '1'
                ? '1'
                : '0';

        $structuredAddress = $this->employeeAddressPayload($validated);
        $formattedAddress = $this->formattedEmployeeAddress($structuredAddress);

        $newUploadedFiles = [];
        $oldFilesToDelete = [];

        DB::beginTransaction();

        try {
            $employeeId = $employee->employee_id;

            if (!$employeeId) {
                $employeeId = $this->allocateEmployeeId($targetOfficeId);
            }

            /*
            |--------------------------------------------------------------------------
            | 6. Update employee details
            |--------------------------------------------------------------------------
            */

            $employee->forceFill([
                'name' => $validated['name'],

                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'],
                'alternate_number' => $validated['alternate_number'] ?? null,

                'dob' => $validated['dob'] ?? null,
                'joining_date' => $validated['joining_date'] ?? null,
                'last_working_date' => $validated['last_working_date'] ?? null,
                'employee_id' => $employeeId,

                'address' => $formattedAddress
                    ?? $this->cleanNullableString(
                        $validated['address'] ?? null
                    ),

                'department_id' =>
                    $validated['department_id'] ?? null,

                'designation' =>
                    $validated['designation'] ?? null,

                'responsibility' =>
                    $validated['responsibility'] ?? null,

                'salary' => array_key_exists('salary', $validated)
                    && $validated['salary'] !== null
                        ? (float) $validated['salary']
                        : null,

                'check_in_time' =>
                    $validated['check_in_time'],

                'check_out_time' =>
                    $validated['check_out_time'],

                'office_time' => $officeMinutes,

                'break' =>
                    array_key_exists('break', $validated) &&
                    $validated['break'] !== null
                        ? (int) $validated['break']
                        : null,

                'location_required' =>
                    $validated['location_required'],

                'status' => $employeeStatus,
                'office_id' => $targetOfficeId,

                'team_leader_id' =>
                    $validated['team_leader_id'] ?? null,

                'leave_authority_id' =>
                    $validated['leave_authority_id'] ?? null,

                'adhar_number' =>
                    $validated['adhar_number'] ?? null,

                'pan_number' =>
                    $validated['pan_number'] ?? null,

                'account_holder_name' =>
                    $validated['account_holder_name'] ?? null,

                'bank_name' =>
                    $validated['bank_name'] ?? null,

                'bank_branch' =>
                    $validated['bank_branch'] ?? null,

                'account_number' =>
                    $validated['account_number'] ?? null,

                'ifsc_code' =>
                    $validated['ifsc_code'] ?? null,

                'account_type' =>
                    $validated['account_type'] ?? null,

                'upi_id' =>
                    $validated['upi_id'] ?? null,

                'uan_number' =>
                    $validated['uan_number'] ?? null,

                'esic_number' =>
                    $validated['esic_number'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 7. Replace files safely
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

                if (!$uploadedFile || !$uploadedFile->isValid()) {
                    throw new \RuntimeException(
                        "The {$inputName} upload failed."
                    );
                }

                $newFilePath = $uploadedFile->store(
                    $fileConfig['directory'],
                    'public'
                );

                if (!$newFilePath) {
                    throw new \RuntimeException(
                        "Unable to upload {$inputName}."
                    );
                }

                $oldFilePath =
                    $employee->getOriginal($fileConfig['column']);

                $newUploadedFiles[] = $newFilePath;

                if (
                    !empty($oldFilePath) &&
                    $oldFilePath !== $newFilePath
                ) {
                    $oldFilesToDelete[] = $oldFilePath;
                }

                $employee->{$fileConfig['column']} = $newFilePath;
            }

            $employee->save();

            /*
            |--------------------------------------------------------------------------
            | 8. Structured address, marital/spouse and nominee details
            |--------------------------------------------------------------------------
            */

            $this->saveEmployeeExtraProfile(
                $employee,
                $validated
            );

            /*
            |--------------------------------------------------------------------------
            | 9. Update Family Members
            |--------------------------------------------------------------------------
            |
            | Family members do not contain files, therefore replacing the rows
            | inside the same DB transaction is simple and consistent:
            | - removed row disappears
            | - edited row is recreated with current submitted values
            | - newly added row is created
            |
            */

            EmployeeFamilyMember::query()
                ->where('user_id', $employee->id)
                ->delete();

            foreach (($validated['family_members'] ?? []) as $familyMember) {
                EmployeeFamilyMember::query()->create([
                    'user_id' => $employee->id,
                    'name' => trim((string) $familyMember['name']),
                    'relation' => trim((string) $familyMember['relation']),
                    'occupation' => $this->cleanNullableString(
                        $familyMember['occupation'] ?? null
                    ),
                    'age' => filled($familyMember['age'] ?? null)
                        ? (int) $familyMember['age']
                        : null,
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 10. Update Educational Qualifications
            |--------------------------------------------------------------------------
            |
            | Existing qualification:
            | - update same row
            | - keep old document if no new file uploaded
            | - replace old document safely if a new file is uploaded
            |
            | New qualification:
            | - create new row
            |
            | Removed qualification:
            | - delete DB row in transaction
            | - delete document only after successful commit
            |
            */

            $submittedQualifications = $request->input(
                'qualifications',
                []
            );

            foreach ($submittedQualifications as $index => $qualificationInput) {

                $qualificationId = !empty($qualificationInput['id'])
                    ? (int) $qualificationInput['id']
                    : null;

                $qualificationName = $this->cleanNullableString(
                    $qualificationInput['qualification'] ?? null
                );

                $courseName = $this->cleanNullableString(
                    $qualificationInput['course_name'] ?? null
                );

                $boardUniversity = $this->cleanNullableString(
                    $qualificationInput['board_university'] ?? null
                );

                $instituteName = $this->cleanNullableString(
                    $qualificationInput['institute_name'] ?? null
                );

                $passingYear = $qualificationInput['passing_year'] ?? null;

                $result = $this->cleanNullableString(
                    $qualificationInput['result'] ?? null
                );

                $documentType = $this->cleanNullableString(
                    $qualificationInput['document_type'] ?? null
                );

                $documentInputName = "qualifications.$index.document";

                $hasNewDocument = $request->hasFile(
                    $documentInputName
                );

                /*
                |--------------------------------------------------------------------------
                | Completely empty new row can be ignored
                |--------------------------------------------------------------------------
                */

                $hasAnyQualificationValue =
                    $qualificationId !== null ||
                    $qualificationName !== null ||
                    $courseName !== null ||
                    $boardUniversity !== null ||
                    $instituteName !== null ||
                    filled($passingYear) ||
                    $result !== null ||
                    $documentType !== null ||
                    $hasNewDocument;

                if (!$hasAnyQualificationValue) {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | qualification column is required for every non-empty row
                |--------------------------------------------------------------------------
                */

                if ($qualificationName === null) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "qualifications.$index.qualification" =>
                            'Please select qualification.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Find existing row or prepare new row
                |--------------------------------------------------------------------------
                */

                if ($qualificationId !== null) {
                    $qualificationModel =
                        EmployeeEducationalQualification::query()
                            ->where('user_id', $employee->id)
                            ->whereKey($qualificationId)
                            ->firstOrFail();
                } else {
                    $qualificationModel =
                        new EmployeeEducationalQualification();

                    $qualificationModel->user_id =
                        $employee->id;
                }

                /*
                |--------------------------------------------------------------------------
                | Replace Marksheet / Degree document safely
                |--------------------------------------------------------------------------
                */

                if ($hasNewDocument) {

                    $uploadedQualificationDocument =
                        $request->file(
                            $documentInputName
                        );

                    if (
                        !$uploadedQualificationDocument ||
                        !$uploadedQualificationDocument->isValid()
                    ) {
                        throw new \RuntimeException(
                            "Qualification document upload failed for row " .
                            ($index + 1) . '.'
                        );
                    }

                    $newQualificationDocumentPath =
                        $uploadedQualificationDocument->store(
                            "employee_qualifications/{$employee->id}",
                            'public'
                        );

                    if (!$newQualificationDocumentPath) {
                        throw new \RuntimeException(
                            "Unable to upload qualification document for row " .
                            ($index + 1) . '.'
                        );
                    }

                    /*
                    * Track the new file so it can be removed if transaction fails.
                    */
                    $newUploadedFiles[] =
                        $newQualificationDocumentPath;

                    /*
                    * Existing document is not deleted now.
                    * It is deleted only after DB commit succeeds.
                    */
                    if (
                        $qualificationModel->exists &&
                        !empty($qualificationModel->document_path) &&
                        $qualificationModel->document_path !==
                            $newQualificationDocumentPath
                    ) {
                        $oldFilesToDelete[] =
                            $qualificationModel->document_path;
                    }

                    $qualificationModel->document_path =
                        $newQualificationDocumentPath;
                }

                /*
                |--------------------------------------------------------------------------
                | Update qualification fields
                |--------------------------------------------------------------------------
                */

                $qualificationModel->qualification =
                    $qualificationName;

                $qualificationModel->course_name =
                    $courseName;

                $qualificationModel->board_university =
                    $boardUniversity;

                $qualificationModel->institute_name =
                    $instituteName;

                $qualificationModel->passing_year =
                    filled($passingYear)
                        ? (int) $passingYear
                        : null;

                $qualificationModel->result =
                    $result;

                $qualificationModel->document_type =
                    $documentType;

                $qualificationModel->save();
            }

            /*
            |--------------------------------------------------------------------------
            | Delete removed educational qualifications
            |--------------------------------------------------------------------------
            */

            $deletedQualificationIds = collect(
                $request->input(
                    'deleted_qualification_ids',
                    []
                )
            )
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            if ($deletedQualificationIds->isNotEmpty()) {

                $qualificationsToDelete =
                    EmployeeEducationalQualification::query()
                        ->where('user_id', $employee->id)
                        ->whereIn('id', $deletedQualificationIds)
                        ->get();

                foreach ($qualificationsToDelete as $qualificationToDelete) {

                    if (
                        !empty(
                            $qualificationToDelete->document_path
                        )
                    ) {
                        $oldFilesToDelete[] =
                            $qualificationToDelete->document_path;
                    }

                    $qualificationToDelete->delete();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 11. Update Spatie role
            |--------------------------------------------------------------------------
            */

            $employee->syncRoles([
                $validated['role'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | 12. Update salary structure
            |--------------------------------------------------------------------------
            */

            $basicSalary =
                (float) ($validated['basic_salary'] ?? 0);

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

            /*
            |--------------------------------------------------------------------------
            | 13. Delete replaced old files after successful commit
            |--------------------------------------------------------------------------
            */

            foreach (array_unique($oldFilesToDelete) as $oldFilePath) {
                try {
                    if (
                        Storage::disk('public')->exists($oldFilePath)
                    ) {
                        Storage::disk('public')->delete($oldFilePath);
                    }
                } catch (\Throwable $fileDeleteException) {
                    report($fileDeleteException);
                }
            }

            return redirect()
                ->route('employee.index')
                ->with(
                    'success',
                    'Employee record updated successfully.'
                );
        } catch (\Throwable $exception) {
            DB::rollBack();

            /*
            * Only files uploaded during this failed request are removed.
            * Existing employee files remain untouched.
            */
            foreach (array_unique($newUploadedFiles) as $newUploadedFile) {
                try {
                    if (
                        Storage::disk('public')->exists($newUploadedFile)
                    ) {
                        Storage::disk('public')->delete($newUploadedFile);
                    }
                } catch (\Throwable $fileDeleteException) {
                    report($fileDeleteException);
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



    private function employeeIdPrefix(Office $office): string
    {
        $prefix = trim((string) $office->employee_prefix);

        if ($prefix === '') {
            $prefix = 'OFF' . $office->id;
        }

        return strtoupper($prefix);
    }


    private function employeeIdForSequence(
        Office $office,
        int $sequence
    ): string {
        return $this->employeeIdPrefix($office)
            . '-'
            . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }


    private function highestExistingEmployeeSequence(Office $office): int
    {
        $prefix = $this->employeeIdPrefix($office);
        $pattern = '/^'
            . preg_quote($prefix, '/')
            . '-(\d+)$/i';

        return User::query()
            ->where('office_id', $office->id)
            ->whereNotNull('employee_id')
            ->where('employee_id', 'like', $prefix . '-%')
            ->pluck('employee_id')
            ->reduce(function (int $highest, $employeeId) use ($pattern) {
                if (
                    preg_match(
                        $pattern,
                        trim((string) $employeeId),
                        $matches
                    ) === 1
                ) {
                    return max($highest, (int) $matches[1]);
                }

                return $highest;
            }, 0);
    }


    private function nextEmployeeSequence(Office $office): int
    {
        return max(
            0,
            (int) $office->employee_sequence,
            $this->highestExistingEmployeeSequence($office)
        ) + 1;
    }


    private function generateEmployeeId(Office $office): string
    {
        $sequence = $this->nextEmployeeSequence($office);
        $employeeId = $this->employeeIdForSequence($office, $sequence);

        while (
            User::query()
                ->where('employee_id', $employeeId)
                ->exists()
        ) {
            $sequence++;
            $employeeId = $this->employeeIdForSequence($office, $sequence);
        }

        return $employeeId;
    }


    private function allocateEmployeeId(int $officeId): string
    {
        $office = Office::query()
            ->lockForUpdate()
            ->findOrFail($officeId);

        $sequence = $this->nextEmployeeSequence($office);
        $employeeId = $this->employeeIdForSequence($office, $sequence);

        while (
            User::query()
                ->where('employee_id', $employeeId)
                ->exists()
        ) {
            $sequence++;
            $employeeId = $this->employeeIdForSequence($office, $sequence);
        }

        $office->forceFill([
            'employee_sequence' => $sequence,
        ])->save();

        return $employeeId;
    }


    public function nextEmployeeId(
        Request $request,
        Office $office
    ) {
        $loggedInUser = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Check office permission
        |--------------------------------------------------------------------------
        */

        if ($loggedInUser->hasRole('super_admin')) {
            // All offices allowed
        } elseif ($loggedInUser->hasRole('owner')) {

            if (
                (int) $office->owner_id !==
                (int) $loggedInUser->id
            ) {
                abort(403);
            }

        } else {

            $activeOfficeId = (int)
                $loggedInUser->activeOfficeId();

            if (
                (int) $office->id !==
                $activeOfficeId
            ) {
                abort(403);
            }
        }

        return response()->json([
            'success' => true,
            'employee_id' => $this->generateEmployeeId(
                $office
            ),
        ]);
    }

}
