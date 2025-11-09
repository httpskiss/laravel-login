<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin role with all permissions
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web'
        ]);
        $superAdminRole->syncPermissions(Permission::all());

        // Create admin user with correct fields
        $admin = User::firstOrCreate([
            'email' => 'admin@university.edu'
        ], [
            'last_name' => 'Administrator',
            'password' => bcrypt('securePassword123'),
            'employee_id' => 'ADMIN001',
            'department' => 'Administration'
        ]);

        $admin->assignRole($superAdminRole);
    }
}