<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index(): View
    {
        $loggedInUser = auth()->user();

        abort_unless($loggedInUser, 401);

        $users = HomeController::employeeList();

        if ($loggedInUser->hasRole('super_admin')) {
            $roles = Role::query()
                ->select(['id', 'name', 'guard_name'])
                ->orderBy('name')
                ->get();

            $permissions = Permission::query()
                ->select(['id', 'name', 'guard_name'])
                ->orderBy('name')
                ->get();
        } else {
            $roles = Role::query()
                ->select(['id', 'name', 'guard_name', 'created_by'])
                ->where('created_by', $loggedInUser->id)
                ->orderBy('name')
                ->get();

            /*
             * getAllPermissions() की जगह direct relation query।
             */
            $permissions = $loggedInUser
                ->getAllPermissions()
                ->sortBy('name')
                ->values();
        }

        return view('dashboard.permission.index', compact(
            'roles',
            'permissions',
            'users'
        ));
    }

    public function givePermission(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id'      => ['nullable', 'required_without:role_id', 'integer'],
            'role_id'      => ['nullable', 'required_without:user_id', 'integer'],
            'permissions'  => ['required', 'array', 'min:1'],
            'permissions.*' => ['required'],
        ]);

        if (!empty($validated['user_id'])) {
            $user = User::query()->findOrFail($validated['user_id']);

            /*
             * Duplicate permissions हटाकर assign करें।
             */
            $permissions = collect($validated['permissions'])
                ->filter()
                ->unique()
                ->values()
                ->all();

            $user->givePermissionTo($permissions);
        }

        if (!empty($validated['role_id'])) {
            $role = Role::findById(
                $validated['role_id'],
                auth()->getDefaultDriver()
            );

            $permissions = collect($validated['permissions'])
                ->filter()
                ->unique()
                ->values()
                ->all();

            $role->givePermissionTo($permissions);
        }

        app(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();

        return back()->with(
            'success',
            'Permissions given successfully.'
        );
    }

    public function create(): View
    {
        return view('dashboard.permission.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'permission_name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name',
            ],
        ]);

        Permission::query()->create([
            'name'       => trim($validated['permission_name']),
            'guard_name' => 'web',
        ]);

        app(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();

        return redirect()
            ->route('permission.index')
            ->with('success', 'Permission created successfully.');
    }
}