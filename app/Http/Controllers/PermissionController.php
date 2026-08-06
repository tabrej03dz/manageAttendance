<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index(){
        $users = HomeController::employeeList();

        if(auth()->user()->hasRole('super_admin')){
            $roles = Role::all();
            $permissions = Permission::all();
        }else{
            $roles = Role::where('created_by', auth()->user()->id)->get();
            $permissions = auth()->user()->getAllPermissions();
        }
        return view('dashboard.permission.index', compact('roles', 'permissions', 'users'));
    }

    public function givePermission(Request $request){
        $request->validate([
            'user_id' => 'required_without:role_id',
            'role_id' => 'required_without:user_id',
            'permissions.*' => 'required',
        ]);

        if ($request->user_id){
            $user = User::find($request->user_id);
            $user->givePermissionTo($request->permissions);
        }
        if ($request->role_id){
            $role = Role::findById($request->role_id);
            $role->givePermissionTo($request->permissions);
        }
        return back()->with('success','Permissions given successfully');
    }

    public function create(){
        return view('dashboard.permission.create');
    }

    public function store(Request $request){
        $request->validate([
            'permission_name' => 'required|string|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->permission_name]);
        return redirect('permission')->with('success', 'permission created successfully');
    }
}
