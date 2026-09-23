<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DesignationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Active Office ID
    |--------------------------------------------------------------------------
    |
    | Priority:
    |
    | 1. Session active_office_id
    | 2. Logged-in user's office_id
    |
    */

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


    /*
    |--------------------------------------------------------------------------
    | Switchable Office IDs
    |--------------------------------------------------------------------------
    */

    private function switchableOfficeIds(Request $request): array
    {
        $user = $request->user();

        if (!$user) {
            return [];
        }

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('super_admin')) {
            return Office::query()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        }

        /*
        |--------------------------------------------------------------------------
        | Owner
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('owner')) {
            return Office::query()
                ->where('owner_id', $user->id)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->toArray();
        }

        /*
        |--------------------------------------------------------------------------
        | User Having Switch Office Permission
        |--------------------------------------------------------------------------
        */

        if (
            $user->can('switch offices')
            || $user->can('switch office')
        ) {
            $currentOffice = $user->office;

            if ($currentOffice && $currentOffice->owner_id) {
                return Office::query()
                    ->where(
                        'owner_id',
                        $currentOffice->owner_id
                    )
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->toArray();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Normal User
        |--------------------------------------------------------------------------
        */

        return $user->office_id && (int) $user->office_id > 0
            ? [(int) $user->office_id]
            : [];
    }


    /*
    |--------------------------------------------------------------------------
    | Allowed Office IDs For Current Screen
    |--------------------------------------------------------------------------
    |
    | Agar active office selected hai to sirf wahi office.
    | Otherwise user ke accessible offices.
    |
    */

    private function allowedOfficeIds(Request $request): array
    {
        $switchableOfficeIds = $this->switchableOfficeIds($request);

        $activeOfficeId = $this->activeOfficeId($request);

        if ($activeOfficeId) {
            return in_array(
                (int) $activeOfficeId,
                $switchableOfficeIds,
                true
            )
                ? [(int) $activeOfficeId]
                : [];
        }

        return $switchableOfficeIds;
    }


    /*
    |--------------------------------------------------------------------------
    | Resolve Target Office For Store
    |--------------------------------------------------------------------------
    */

    private function resolveTargetOfficeId(Request $request): int
    {
        $loggedInUser = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        |
        | Super admin office manually select karega.
        |
        */

        if ($loggedInUser->hasRole('super_admin')) {

            $targetOfficeId = (int) $request->input('office_id');

            if (
                !$targetOfficeId
                || !Office::query()
                    ->whereKey($targetOfficeId)
                    ->exists()
            ) {
                abort(
                    422,
                    'Please select a valid office.'
                );
            }

            return $targetOfficeId;
        }

        /*
        |--------------------------------------------------------------------------
        | Owner
        |--------------------------------------------------------------------------
        */

        if ($loggedInUser->hasRole('owner')) {

            $ownerOfficeIds = Office::query()
                ->where('owner_id', $loggedInUser->id)
                ->pluck('id')
                ->map(fn ($id) => (int) $id);

            if ($ownerOfficeIds->isEmpty()) {
                abort(
                    403,
                    'No office found for this owner.'
                );
            }

            $targetOfficeId = (int) (
                $request->input('office_id')
                ?: $this->activeOfficeId($request)
            );

            if (
                !$targetOfficeId
                || !$ownerOfficeIds->contains($targetOfficeId)
            ) {
                abort(
                    403,
                    'Invalid office selected.'
                );
            }

            return $targetOfficeId;
        }

        /*
        |--------------------------------------------------------------------------
        | Admin / Manager / Team Leader / Employee
        |--------------------------------------------------------------------------
        */

        $targetOfficeId = (int) $this->activeOfficeId($request);

        if (!$targetOfficeId) {
            abort(
                403,
                'Please select an office first.'
            );
        }

        $allowedOfficeIds = $this->switchableOfficeIds($request);

        if (
            !in_array(
                $targetOfficeId,
                $allowedOfficeIds,
                true
            )
        ) {
            abort(
                403,
                'You do not have access to the selected office.'
            );
        }

        return $targetOfficeId;
    }


    /*
    |--------------------------------------------------------------------------
    | Check Designation Access
    |--------------------------------------------------------------------------
    |
    | Edit / Update / Delete ke time designation current user ke accessible
    | office ki hi honi chahiye.
    |
    */

    private function authorizeDesignation(
        Request $request,
        Designation $designation
    ): void {
        $loggedInUser = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        if ($loggedInUser->hasRole('super_admin')) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Owner
        |--------------------------------------------------------------------------
        */

        if ($loggedInUser->hasRole('owner')) {

            $hasAccess = Office::query()
                ->whereKey($designation->office_id)
                ->where('owner_id', $loggedInUser->id)
                ->exists();

            if (!$hasAccess) {
                abort(
                    403,
                    'This designation does not belong to your office.'
                );
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Other Users
        |--------------------------------------------------------------------------
        */

        $activeOfficeId = $this->activeOfficeId($request);

        if (!$activeOfficeId) {
            abort(
                403,
                'Please select an office first.'
            );
        }

        if (
            (int) $designation->office_id
            !== (int) $activeOfficeId
        ) {
            abort(
                403,
                'This designation does not belong to the selected office.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $allowedOfficeIds = $this->allowedOfficeIds($request);

        /*
        |--------------------------------------------------------------------------
        | Query
        |--------------------------------------------------------------------------
        */

        $query = Designation::query()
            ->with([
                'office:id,name',
                'department:id,name',
                'creator:id,name',
            ])
            ->when(
                !empty($allowedOfficeIds),
                function ($query) use ($allowedOfficeIds) {
                    $query->whereIn(
                        'office_id',
                        $allowedOfficeIds
                    );
                },
                function ($query) {
                    $query->whereRaw('1 = 0');
                }
            );

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('q')) {
            $search = trim(
                (string) $request->input('q')
            );

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->where(
                        'name',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'code',
                        'like',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'description',
                        'like',
                        '%' . $search . '%'
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Department Filter
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
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('is_active')) {
            $query->where(
                'is_active',
                (int) $request->input('is_active')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Office Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('office_id')) {

            $requestedOfficeId = (int)
                $request->input('office_id');

            if (
                in_array(
                    $requestedOfficeId,
                    $allowedOfficeIds,
                    true
                )
            ) {
                $query->where(
                    'office_id',
                    $requestedOfficeId
                );
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $perPage = (int) $request->input(
            'per_page',
            25
        );

        if (!in_array(
            $perPage,
            [10, 25, 50, 100],
            true
        )) {
            $perPage = 25;
        }

        $designations = $query
            ->orderBy('sort_order')
            ->orderBy('level')
            ->orderBy('name')
            ->paginate($perPage)
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
                function ($query) use ($allowedOfficeIds) {
                    $query->whereIn(
                        'id',
                        $allowedOfficeIds
                    );
                },
                function ($query) {
                    $query->whereRaw('1 = 0');
                }
            )
            ->orderBy('name')
            ->get();

        return view(
            'dashboard.designation.index',
            compact(
                'designations',
                'departments',
                'offices'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(Request $request)
    {
        $loggedInUser = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Offices
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

        } elseif ($loggedInUser->hasRole('owner')) {

            $offices = Office::query()
                ->select([
                    'id',
                    'name',
                    'owner_id',
                ])
                ->where(
                    'owner_id',
                    $loggedInUser->id
                )
                ->orderBy('name')
                ->get();

            if ($offices->isEmpty()) {
                return back()->with(
                    'error',
                    'No office found for this owner.'
                );
            }

        } else {

            $activeOfficeId =
                $this->activeOfficeId($request);

            if (!$activeOfficeId) {
                return back()->with(
                    'error',
                    'Please select an office first.'
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

            if ($offices->isEmpty()) {
                return back()->with(
                    'error',
                    'Selected office was not found.'
                );
            }
        }

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
        | Default Office
        |--------------------------------------------------------------------------
        */

        $selectedOfficeId = (int) old(
            'office_id',
            $this->activeOfficeId($request)
        );

        if (
            !$selectedOfficeId
            && $offices->count() === 1
        ) {
            $selectedOfficeId =
                (int) $offices->first()->id;
        }

        return view(
            'dashboard.designation.create',
            compact(
                'offices',
                'departments',
                'selectedOfficeId'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Resolve Office
        |--------------------------------------------------------------------------
        */

        $targetOfficeId =
            $this->resolveTargetOfficeId($request);

        /*
        |--------------------------------------------------------------------------
        | Normalize
        |--------------------------------------------------------------------------
        */

        $request->merge([
            'name' => trim(
                (string) $request->input('name')
            ),

            'code' => $request->filled('code')
                ? strtoupper(
                    trim(
                        (string) $request->input('code')
                    )
                )
                : null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'department_id' => [
                'nullable',
                'integer',
                'exists:departments,id',
            ],

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique(
                    'designations',
                    'name'
                )
                    ->where(
                        'office_id',
                        $targetOfficeId
                    )
                    ->where(
                        'department_id',
                        $request->input('department_id')
                    ),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'level' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        $designation = new Designation();

        $designation->forceFill([
            'office_id' =>
                $targetOfficeId,

            'department_id' =>
                !empty($validated['department_id'])
                    ? (int) $validated['department_id']
                    : null,

            'name' =>
                $validated['name'],

            'code' =>
                $validated['code'] ?? null,

            'description' =>
                $validated['description'] ?? null,

            'level' =>
                !empty($validated['level'])
                    ? (int) $validated['level']
                    : null,

            'sort_order' =>
                isset($validated['sort_order'])
                    ? (int) $validated['sort_order']
                    : 0,

            'is_active' =>
                $request->has('is_active')
                    ? $request->boolean('is_active')
                    : true,

            'created_by' =>
                $request->user()->id,
        ]);

        $designation->save();

        return redirect()
            ->route('designations.index')
            ->with(
                'success',
                'Designation created successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        Designation $designation
    ) {
        $this->authorizeDesignation(
            $request,
            $designation
        );

        $designation->load([
            'office:id,name',
            'department:id,name',
            'creator:id,name',
        ]);

        return view(
            'dashboard.designation.show',
            compact('designation')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        Request $request,
        Designation $designation
    ) {
        /*
        |--------------------------------------------------------------------------
        | Check Access
        |--------------------------------------------------------------------------
        */

        $this->authorizeDesignation(
            $request,
            $designation
        );

        $loggedInUser = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Offices
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

        } elseif ($loggedInUser->hasRole('owner')) {

            $offices = Office::query()
                ->select([
                    'id',
                    'name',
                    'owner_id',
                ])
                ->where(
                    'owner_id',
                    $loggedInUser->id
                )
                ->orderBy('name')
                ->get();

        } else {

            $activeOfficeId =
                $this->activeOfficeId($request);

            $offices = Office::query()
                ->select([
                    'id',
                    'name',
                    'owner_id',
                ])
                ->whereKey($activeOfficeId)
                ->get();
        }

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

        return view(
            'dashboard.designation.edit',
            compact(
                'designation',
                'offices',
                'departments'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Designation $designation
    ) {
        /*
        |--------------------------------------------------------------------------
        | Current Designation Access
        |--------------------------------------------------------------------------
        */

        $this->authorizeDesignation(
            $request,
            $designation
        );

        $loggedInUser = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Resolve Target Office
        |--------------------------------------------------------------------------
        */

        if ($loggedInUser->hasRole('super_admin')) {

            $targetOfficeId = (int) (
                $request->input('office_id')
                ?: $designation->office_id
            );

            if (
                !$targetOfficeId
                || !Office::query()
                    ->whereKey($targetOfficeId)
                    ->exists()
            ) {
                return back()
                    ->withErrors([
                        'office_id' =>
                            'Please select a valid office.',
                    ])
                    ->withInput();
            }

        } elseif ($loggedInUser->hasRole('owner')) {

            $ownerOfficeIds = Office::query()
                ->where(
                    'owner_id',
                    $loggedInUser->id
                )
                ->pluck('id')
                ->map(fn ($id) => (int) $id);

            $targetOfficeId = (int) (
                $request->input('office_id')
                ?: $designation->office_id
            );

            if (
                !$targetOfficeId
                || !$ownerOfficeIds->contains(
                    $targetOfficeId
                )
            ) {
                return back()
                    ->withErrors([
                        'office_id' =>
                            'Invalid office selected.',
                    ])
                    ->withInput();
            }

        } else {

            $targetOfficeId =
                $this->activeOfficeId($request);

            if (!$targetOfficeId) {
                return back()
                    ->with(
                        'error',
                        'Please select an office first.'
                    )
                    ->withInput();
            }

            if (
                (int) $designation->office_id
                !== (int) $targetOfficeId
            ) {
                abort(
                    403,
                    'This designation does not belong to the selected office.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize
        |--------------------------------------------------------------------------
        */

        $request->merge([
            'name' => trim(
                (string) $request->input('name')
            ),

            'code' => $request->filled('code')
                ? strtoupper(
                    trim(
                        (string) $request->input('code')
                    )
                )
                : null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'department_id' => [
                'nullable',
                'integer',
                'exists:departments,id',
            ],

            'name' => [
                'required',
                'string',
                'max:150',

                Rule::unique(
                    'designations',
                    'name'
                )
                    ->ignore($designation->id)
                    ->where(
                        'office_id',
                        $targetOfficeId
                    )
                    ->where(
                        'department_id',
                        $request->input('department_id')
                    ),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'level' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $designation->forceFill([
            'office_id' =>
                $targetOfficeId,

            'department_id' =>
                !empty($validated['department_id'])
                    ? (int) $validated['department_id']
                    : null,

            'name' =>
                $validated['name'],

            'code' =>
                $validated['code'] ?? null,

            'description' =>
                $validated['description'] ?? null,

            'level' =>
                !empty($validated['level'])
                    ? (int) $validated['level']
                    : null,

            'sort_order' =>
                isset($validated['sort_order'])
                    ? (int) $validated['sort_order']
                    : 0,

            'is_active' =>
                $request->has('is_active')
                    ? $request->boolean('is_active')
                    : false,
        ]);

        $designation->save();

        return redirect()
            ->route('designations.index')
            ->with(
                'success',
                'Designation updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Request $request,
        Designation $designation
    ) {
        /*
        |--------------------------------------------------------------------------
        | Check Access
        |--------------------------------------------------------------------------
        */

        $this->authorizeDesignation(
            $request,
            $designation
        );

        /*
        |--------------------------------------------------------------------------
        | Soft Delete
        |--------------------------------------------------------------------------
        */

        $designation->delete();

        return redirect()
            ->route('designations.index')
            ->with(
                'success',
                'Designation deleted successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Toggle Status
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(
        Request $request,
        Designation $designation
    ) {
        /*
        |--------------------------------------------------------------------------
        | Check Access
        |--------------------------------------------------------------------------
        */

        $this->authorizeDesignation(
            $request,
            $designation
        );

        /*
        |--------------------------------------------------------------------------
        | Toggle
        |--------------------------------------------------------------------------
        */

        $designation->is_active =
            !$designation->is_active;

        $designation->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,

                'message' =>
                    'Designation status updated successfully.',

                'is_active' =>
                    (bool) $designation->is_active,
            ]);
        }

        return back()->with(
            'success',
            'Designation status updated successfully.'
        );
    }
}