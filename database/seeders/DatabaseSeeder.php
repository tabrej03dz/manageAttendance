<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
        $user = User::create(['name' => 'Super Admin', 'email' => 'super@admin.com', 'password' => Hash::make('password')]);
        Role::create(['name' => 'super_admin']);
        $user->assignRole('super_admin');

        Office::create(['name' => 'Main Office', 'latitude' => 26.494535801545734, 'longitude' => 80.27970222977625, 'radius' => 100]);
    }
}
