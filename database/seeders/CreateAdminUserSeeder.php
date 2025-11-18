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

        // Create admin user with ALL required fields
        $admin = User::firstOrCreate([
            'email' => 'admin@university.edu'
        ], [
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'password' => bcrypt('securePassword123'),
            'employee_id' => 'ADMIN001',
            'department' => 'Administration',
            'user_status' => 'Active',
            'hire_date' => now(),
            // Optional fields can be null
            'phone' => null,
            'address' => null,
            'gender' => null,
            'dob' => null,
            'profile_photo_path' => null,
        ]);

        $admin->assignRole($superAdminRole);
        
        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@university.edu');
        $this->command->info('Password: securePassword123');
    }
}