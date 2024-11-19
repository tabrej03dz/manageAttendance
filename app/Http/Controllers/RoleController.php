<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(){
        if (auth()->user()->hasRole('super_admin')){
            $roles = Role::all();
        }else{
            $roles = Role::where('created_by', auth()->user()->id)->get();
        }
        return view('dashboard.role.index', compact('roles'));
    }

    public function create(){
        return view('dashboard.role.create');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string',
        ]);

        $status = Role::create(['name' => $request->name, 'created_by'=> auth()->user()->id]);
        if ($status){
            request()->session()->flash('success', 'Role created successfully');
        }else{
            request()->session()->flash('error', 'Failed, Try again!');
        }
        return redirect('/');
    }

    public function delete(Role $role){
        $role->delete();
        return back()->with('success', 'Role deleted successfully');
    }

    public function permission(Role $role){
        $permissions = $role->permissions;
        return view('dashboard.role.permission', compact('permissions', 'role'));
    }

    public function permissionRemove(Permission $permission, Role $role){
        if ($role->hasPermissionTo($permission)) { // Check if the role has the permission
            $role->revokePermissionTo($permission); // Remove the permission from the role
            return back()->with('success', 'Permission removed from the role successfully.');
        }
        return back()->with('error', 'Permission not assigned to this role.');
    }



}
