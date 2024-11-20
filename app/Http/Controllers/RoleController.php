<?php

namespace App\Http\Controllers;

use App\Models\Off;
use App\Models\Office;
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
        if (auth()->user()->hasRole('super_admin')){
            $offices = Office::all();
        }else{
            $offices = auth()->user()->offices;
        }
        return view('dashboard.role.create', compact('offices'));
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|unique:roles,name',
            'office' => 'required',
        ]);
        if (Role::where('name', $request->office.'__'.$request->name)->exists()){
            return back()->with('error', 'This role is already exists');
        }
        $status = Role::create(['name' => $request->office.'__'.$request->name, 'created_by'=> auth()->user()->id]);
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
