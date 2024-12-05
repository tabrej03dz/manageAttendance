<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class HomeController extends Controller
{
    public static function employeeList($user, $date)
    {

        if ($user->hasRole('super_admin|admin')) {
            if ($user->hasRole('super_admin')) {
                $employees = User::with(['latestAttendance' => function ($query) use ($date) {
                    $query->whereDate('created_at', $date);
                }])->get();
            } else {
                $office = $user->office;
                $employees = $office->users()->with(['latestAttendance' => function ($query) use ($date) {
                    $query->whereDate('created_at', $date);
                }])->get();
            }
        } elseif ($user->hasRole('owner')) {
            $officeIds = Office::where('owner_id', $user->id)->pluck('id');
            $employees = User::whereIn('office_id', $officeIds)
                ->with(['latestAttendance' => function ($query) use ($date) {
                    $query->whereDate('created_at', $date);
                }])->get();
        } else {
            if ($user->hasRole('team_leader')) {
                $employees = $user->members()->with(['latestAttendance' => function ($query) use ($date) {
                    $query->whereDate('created_at', $date);
                }])->get();
                $record = User::with(['latestAttendance' => function ($query) use ($date) {
                    $query->whereDate('created_at', $date);
                }])->find($user->id);
                $employees->push($record);
            } else {
                $employees = User::where('id', $user->id)
                    ->with(['latestAttendance' => function ($query) use ($date) {
                        $query->whereDate('created_at', $date);
                    }])->get();
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
