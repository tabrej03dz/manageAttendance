<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\LunchBreak;
use App\Models\Off;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{

    function currentMonth($startOfMont, $endOfMonth){

        $data['sundays'] = 0;
        $data['days'] = 0;
        $data['offs'] = 0;
        $data['records'] = 0;

        for ($date = $startOfMont; $date->lte($endOfMonth); $date->addDay()) {
            if ($date->isSunday()) {
                $data['sundays'] += 1;
            }
            $data['days'] += 1;
            if (auth()->user()->hasRole('owner')){
                    $off = Off::whereDate('date', $date)->where('office_id', auth()->user()->offices->first()?->id)->first();
            }else{
                $off = Off::whereDate('date', $date)->where('office_id', auth()->user()->office->id)->first();
            }
            if ($off){
                $data['offs'] += 1;
            }
            $record = AttendanceRecord::whereDate('check_in', $date)->where('user_id', auth()->user()->id)->first();
            if($record){
                $data['records'] += 1;
            }

        }
        return $data;
    }

    public function dashboard(){

        $user = User::where('name', 'Super Admin')->first();
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $teamLeader = Role::firstOrCreate(['name' => 'team_leader']);
        $employee = Role::firstOrCreate(['name' => 'employee']);
        $owner = Role::firstOrCreate(['name' => 'owner']);


        Permission::create(['name' => 'show dashboard']);
        Permission::create(['name' => 'show records of employees']);
        Permission::create(['name' => 'show owners']);
        Permission::create(['name' => 'create owners']);
        Permission::create(['name' => 'edit owners']);
        Permission::create(['name' => 'show owner\'s plans']);
        Permission::create(['name' => 'edit plan']);
        Permission::create(['name' => 'delete plan']);
        Permission::create(['name' => 'create plan']);
        Permission::create(['name' => 'show attendance']);
        Permission::create(['name' => 'approve late message']);
        Permission::create(['name' => 'reject late message']);
        Permission::create(['name' => 'approve before going message']);
        Permission::create(['name' => 'reject before going message']);
        Permission::create(['name' => 'add note']);
        Permission::create(['name' => 'show employees']);
        Permission::create(['name' => 'show all employees']);
        Permission::create(['name' => 'create employee']);
        Permission::create(['name' => 'edit employee']);
        Permission::create(['name' => 'delete employee']);
        Permission::create(['name' => 'show profile of employee']);
        Permission::create(['name' => 'change status of employee']);
        Permission::create(['name' => 'show leaves']);
        Permission::create(['name' => 'request for leave']);
        Permission::create(['name' => 'approve leave']);
        Permission::create(['name' => 'reject leave']);
        Permission::create(['name' => 'show offices']);
        Permission::create(['name' => 'create office']);
        Permission::create(['name' => 'edit office']);
        Permission::create(['name' => 'delete office']);
        Permission::create(['name' => 'show office office details']);
        Permission::create(['name' => 'manage offs']);
        Permission::create(['name' => 'create off']);
        Permission::create(['name' => 'edit off']);
        Permission::create(['name' => 'delete off']);
        Permission::create(['name' => 'show polices']);
        Permission::create(['name' => 'make policy']);
        Permission::create(['name' => 'edit policy']);
        Permission::create(['name' => 'delete policy']);
        Permission::create(['name' => 'show reports']);
        Permission::create(['name' => 'download reports']);
        Permission::create(['name' => 'filter report']);
        Permission::create(['name' => 'mark attendance of employees']);
        Permission::create(['name' => 'check-in attendance of employee']);
        Permission::create(['name' => 'check-out attendance of employee']);
        Permission::create(['name' => 'show payments']);
        Permission::create(['name' => 'add payment']);
        Permission::create(['name' => 'show salaries']);
        Permission::create(['name' => 'pay salaries']);
        Permission::create(['name' => 'show visits']);
        Permission::create(['name' => 'create visit']);
        Permission::create(['name' => 'approve visit']);
        Permission::create(['name' => 'reject visit']);
        Permission::create(['name' => 'visit mark as paid']);
        Permission::create(['name' => 'show recycles']);
        Permission::create(['name' => 'restore employee']);
        Permission::create(['name' => 'permanent delete employee']);
        Permission::create(['name' => 'restore all employee']);
        Permission::create(['name' => 'permanent delete all employee']);
        Permission::create(['name' => 'show breaks']);
        Permission::create(['name' => 'manual attendance entry']);
        Permission::create(['name' => 'advance salary']);
        Permission::create(['name' => 'remove permission of employee']);
        Permission::create(['name' => 'show permissions']);
        Permission::create(['name' => 'make advance payment']);
        Permission::create(['name' => 'show roles']);
        Permission::create(['name' => 'create permission']);
        Permission::create(['name' => 'give permission to role']);
        Permission::create(['name' => 'give permission to user']);
        Permission::create(['name' => 'give permission']);
        Permission::create(['name' => 'delete role']);
        Permission::create(['name' => 'show permissions of role']);
        Permission::create(['name' => 'delete role\'s permission']);
        Permission::create(['name' => 'pay visit expense']);

        $showPermissions = Permission::create(['name' => 'show permissions']);


        $ownerPermissions = [
            'check-in', 'check-out', 'show dashboard',
            'show records',
            'show records of employees',
            'show attendance',
            'approve late message',
            'reject late message',
            'approve before going message',
            'reject before going message',
            'add note',
            'show employees',
            'create employee',
            'edit employee',
            'delete employee',
            'show profile of employee',
            'change status of employee',
            'show leaves',
            'request for leave',
            'approve leave',
            'reject leave',
            'show offices',
            'create office',
            'edit office',
            'delete office',
            'show office office details',
            'manage offs',
            'create off',
            'edit off',
            'delete off',
            'show polices',
            'make policy',
            'edit policy',
            'delete policy',
            'show reports',
            'download reports',
            'filter report',
            'mark attendance of employees',
            'check-in attendance of employee',
            'check-out attendance of employee',
            'show salaries',
            'pay salary',
            'show visits',
            'create visit',
            'approve visit',
            'reject visit',
            'visit mark as paid',
            'show breaks',
            'manual attendance entry',
            'show permissions',
        ];
        $adminPermissions = [
            'check-in', 'check-out', 'show dashboard',
            'show records',
            'show records of employees',
            'show attendance',
            'approve late message',
            'reject late message',
            'approve before going message',
            'reject before going message',
            'add note',
            'show employees',
            'create employee',
            'edit employee',
            'delete employee',
            'show profile of employee',
            'change status of employee',
            'show leaves',
            'request for leave',
            'approve leave',
            'reject leave',
            'manage offs',
            'create off',
            'edit off',
            'delete off',
            'show polices',
            'make policy',
            'edit policy',
            'delete policy',
            'show reports',
            'download reports',
            'filter report',
            'mark attendance of employees',
            'check-in attendance of employee',
            'check-out attendance of employee',
            'show payments',
            'add payment',
            'show salaries',
            'pay salaries',
            'show visits',
            'create visit',
            'approve visit',
            'reject visit',
            'visit mark as paid',
            'show breaks',
            'manual attendance entry',
        ];



        $teamLeaderPermissions = [
            'check-in', 'check-out', 'show dashboard',
            'show records',
            'show records of employees',
            'show attendance',
            'approve late message',
            'reject late message',
            'approve before going message',
            'reject before going message',
            'add note',
            'show employees',
            'create employee',
            'show leaves',
            'request for leave',
            'approve leave',
            'reject leave',
            'show reports',
            'mark attendance of employees',
            'check-in attendance of employee',
            'check-out attendance of employee',
            'show breaks',
            'manual attendance entry',
        ];

        $employeePermissions = [
            'check-in', 'check-out', 'show dashboard',
            'show records',
            'show attendance',
            'show leaves',
            'request for leave',
            'filter report',
            'show breaks',

        ];

        $user->assignRole('super_admin');
        $superAdmin->givePermissionTo($showPermissions);
        $superAdmin->givePermissionTo(Permission::all());
        $owner->givePermissionTo(Permission::whereIn('name', $ownerPermissions)->get());
        $admin->givePermissionTo(Permission::whereIn('name', $adminPermissions)->get());
        $teamLeader->givePermissionTo(Permission::whereIn('name', $teamLeaderPermissions)->get());
        $employee->givePermissionTo(Permission::whereIn('name', $employeePermissions)->get());

        dd('permission');
        $user = auth()->user();
//        $halfDayRecords = AttendanceRecord::where('check_in', null)->orWhere('check_out', null)->get();
//        foreach ($halfDayRecords as $record){
//            $record->update(['day_type' => 'half day', 'duration' => $record->user->office_time / 2]);
//        }
        $employees = User::all();
        if (auth()->user()->hasRole('owner')){
            $offices = $user->offices;
        }else{
            $offices = Office::all();
        }

        $todayAttendanceRecord = AttendanceRecord::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->first();
        if ($todayAttendanceRecord){
            $break = LunchBreak::where('attendance_record_id', $todayAttendanceRecord->id)
                ->orderBy('created_at', 'desc')
                ->first();
        }else{
            $break = null;
        }

        $data = $this->currentMonth(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());

        return view('dashboard.dashboard', compact('offices', 'data', 'todayAttendanceRecord', 'break'))->with('employees', $employees);
    }


}
