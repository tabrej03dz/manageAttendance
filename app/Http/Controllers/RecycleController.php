<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RecycleController extends Controller
{
    public function index(){
        $users = User::onlyTrashed()->get();
        return view('dashboard.recycle.index', compact('users'));
    }

    public function userDelete($id){
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete(); // Permanently delete the user
        return back()->with('success', 'User permanently deleted.');
    }

    public function userRestore($id){
       $user = User::onlyTrashed()->findOrFail($id);
        if ($user->trashed()) {
            // Restore the user
            $user->restore();
        }
        return back()->with('success', 'User restored successfully');
    }
}
