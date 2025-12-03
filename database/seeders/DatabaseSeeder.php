<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionTableSeeder::class,
            RolePermissionSeeder::class,
            CreateAdminUserSeeder::class,
            DepartmentHeadsSeeder::class,
            LeaveTypesSeeder::class,
        ]);

        // Create test users for each role (optional)
        User::factory()->count(5)->create()->each(function ($user) {
            $user->assignRole('Employee');
        });
    }
}