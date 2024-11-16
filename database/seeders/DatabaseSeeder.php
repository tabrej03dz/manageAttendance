<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $office = Office::create(['name' => 'Main Office', 'latitude' => 26.494535801545734, 'longitude' => 80.27970222977625, 'radius' => 100]);

        $user = User::create(['name' => 'Super Admin', 'email' => 'super@admin.com', 'password' => Hash::make('password'), 'office_id' => $office->id, 'check_in_time' => '10:00:00', 'check_out_time' => '07:00:00']);
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $teamLeader = Role::firstOrCreate(['name' => 'team_leader']);
        $employee = Role::create(['name' => 'employee']);
        $owner = Role::firstOrCreate(['name' => 'owner']);

        Permission::create(['name' => 'check-in']);
        Permission::create(['name' => 'check-out']);
        Permission::create(['name' => 'show dashboard']);
        Permission::create(['name' => 'show records']);
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
        Permission::create(['name' => 'show permissions']);
        Permission::create(['name' => 'advance salary']);

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
            'manual attendance entry',
        ];

        $user->assignRole('super_admin');
        $superAdmin->givePermissionTo(Permission::all());
        $owner->givePermissionTo(Permission::whereIn('name', $ownerPermissions)->get());
        $admin->givePermissionTo(Permission::whereIn('name', $adminPermissions)->get());
        $teamLeader->givePermissionTo(Permission::whereIn('name', $teamLeaderPermissions)->get());
        $employee->givePermissionTo(Permission::whereIn('name', $employeePermissions)->get());


    }
}
