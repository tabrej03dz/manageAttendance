<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index(){
        $users = HomeController::employeeList();
        $roles = Role::all();
        $permissions = auth()->user()->getAllPermissions();
        return view('dashboard.permission.index', compact('permissions', 'users', 'roles'));
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
}
