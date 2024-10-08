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
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'team_leader']);
        Role::create(['name' => 'employee']);

        Permission::create(['name' => 'show records']);
        Permission::create(['name' => 'check-in']);
        Permission::create(['name' => 'check-out']);
        Permission::create(['name' => 'show employee']);
        $user->assignRole('super_admin');
    }
}
