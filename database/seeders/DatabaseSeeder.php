<?php

namespace Database\Seeders;

use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create an office
        $office = Office::create([
            'name' => 'Main Office',
            'latitude' => 26.494535801545734,
            'longitude' => 80.27970222977625,
            'radius' => 100
        ]);

        // Create a Super Admin user
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('password'),
            'office_id' => $office->id,
            'check_in_time' => '10:00:00',
            'check_out_time' => '19:00:00'
        ]);

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $teamLeader = Role::firstOrCreate(['name' => 'team_leader']);
        $employee = Role::firstOrCreate(['name' => 'employee']);
        $owner = Role::firstOrCreate(['name' => 'owner']);

        // Create permissions
        $permissions = [
            'check-in',
            'check-out',
            'show dashboard',
            'show records',
            'show records of employees',
            'show attendance',
            'approve late message',
            'reject late message',
            'approve before going message',
            'reject before going message',
            'add note',
            'show employees',
            'show all employees',
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
            'show office details',
            'manage offs',
            'create off',
            'edit off',
            'delete off',
            'show policies',
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
            'show recycles',
            'restore employee',
            'permanent delete employee',
            'restore all employees',
            'permanent delete all employees',
            'show breaks',
            'manual attendance entry',
            'show permissions',
            'advance salary',
            'remove permission of employee',
            'make advance payment',
            'show roles',
            'create permission',
            'give permission to role',
            'give permission to user',
            'delete role',
            'show permissions of role',
            'delete role\'s permission',
            'pay visit expense',
            'show owners',
            'create owners',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $ownerPermissions = [
            'check-in', 'check-out', 'show dashboard',
            'show records', 'show records of employees',
            'show attendance', 'approve late message',
            'reject late message', 'approve before going message',
            'reject before going message', 'add note',
            'show employees', 'create employee', 'edit employee',
            'delete employee', 'show profile of employee',
            'change status of employee', 'show leaves',
            'request for leave', 'approve leave', 'reject leave',
            'show offices', 'create office', 'edit office',
            'delete office', 'show office details', 'manage offs',
            'create off', 'edit off', 'delete off', 'show policies',
            'make policy', 'edit policy', 'delete policy',
            'show reports', 'download reports', 'filter report',
            'mark attendance of employees', 'check-in attendance of employee',
            'check-out attendance of employee', 'show payments',
            'add payment', 'show salaries', 'pay salaries',
            'show visits', 'create visit', 'approve visit',
            'reject visit', 'visit mark as paid', 'show breaks',
            'manual attendance entry',
        ];

        $adminPermissions = $ownerPermissions;

        $teamLeaderPermissions = [
            'check-in', 'check-out', 'show dashboard',
            'show records', 'show records of employees',
            'show attendance', 'approve late message',
            'reject late message', 'approve before going message',
            'reject before going message', 'add note',
            'show employees', 'create employee', 'show leaves',
            'request for leave', 'approve leave', 'reject leave',
            'show reports', 'mark attendance of employees',
            'check-in attendance of employee',
            'check-out attendance of employee', 'show breaks',
            'manual attendance entry',
        ];

        $employeePermissions = [
            'check-in', 'check-out', 'show dashboard',
            'show records', 'show attendance', 'show leaves',
            'request for leave', 'filter report', 'show breaks',
            'manual attendance entry',
        ];

        // Assign roles and permissions
        $user->assignRole($superAdmin);
        $superAdmin->givePermissionTo(Permission::all());
        $owner->givePermissionTo($ownerPermissions);
        $admin->givePermissionTo($adminPermissions);
        $teamLeader->givePermissionTo($teamLeaderPermissions);
        $employee->givePermissionTo($employeePermissions);
    }
}
