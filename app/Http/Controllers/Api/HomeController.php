<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class HomeController extends Controller
{
    public static function employeeList($user)
    {

        if ($user->hasRole('super_admin|admin')) {
            if ($user->hasRole('super_admin')) {
                $employees = User::all();
            } else {
                $office = $user->office;
                $employees = $office->users;
            }
        } elseif ($user->hasRole('owner')) {
            $officeIds = Office::where('owner_id', $user->id)->pluck('id');
            $employees = User::whereIn('office_id', $officeIds)->get();
        } else {
            if ($user->hasRole('team_leader')) {
                $employees = $user->members;
                $record = User::find($user->id);
                $employees->push($record);
            } else {
                $employees = User::where('id', $user->id)->get();
            }
        }

        return $employees;
    }



    public static function getTime($totalMinutes){
        $hours = (int)($totalMinutes/60);
        $minutes = $totalMinutes % 60;

        return "$hours h, $minutes m";
    }
}
