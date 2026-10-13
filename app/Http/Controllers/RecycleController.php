<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmployeeLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecycleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Recycle Bin Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'employees');

        if (!in_array($activeTab, ['employees', 'letters'])) {
            $activeTab = 'employees';
        }

        // Deleted employees
        $users = User::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate(10, ['*'], 'users_page');

        // Deleted employee letters
        $employeeLetters = EmployeeLetter::onlyTrashed()
            ->with('user')
            ->orderByDesc('deleted_at')
            ->paginate(10, ['*'], 'letters_page');

        // Counters
        $deletedUsersCount = User::onlyTrashed()->count();

        $deletedLettersCount = EmployeeLetter::onlyTrashed()->count();

        return view('dashboard.recycle.index', compact(
            'users',
            'employeeLetters',
            'deletedUsersCount',
            'deletedLettersCount',
            'activeTab'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Permanently Delete Employee
    |--------------------------------------------------------------------------
    */

    public function userDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($user) {
            $user->forceDelete();
        });

        return redirect()
            ->route('recycle.index', ['tab' => 'employees'])
            ->with('success', 'Employee permanently deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore Employee
    |--------------------------------------------------------------------------
    */

    public function userRestore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($user) {
            $user->restore();
        });

        return redirect()
            ->route('recycle.index', ['tab' => 'employees'])
            ->with('success', 'Employee restored successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Permanently Delete Employee Letter
    |--------------------------------------------------------------------------
    */

    public function employeeLetterDelete($id)
    {
        $letter = EmployeeLetter::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($letter) {
            $letter->forceDelete();
        });

        return redirect()
            ->route('recycle.index', ['tab' => 'letters'])
            ->with('success', 'Employee letter permanently deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore Employee Letter
    |--------------------------------------------------------------------------
    */

    public function employeeLetterRestore($id)
    {
        $letter = EmployeeLetter::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($letter) {
            $letter->restore();
        });

        return redirect()
            ->route('recycle.index', ['tab' => 'letters'])
            ->with('success', 'Employee letter restored successfully.');
    }
}