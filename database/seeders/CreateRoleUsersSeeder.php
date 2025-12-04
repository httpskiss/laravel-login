<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CreateRoleUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'role' => 'Super Admin',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'superadmin@university.edu',
                'password' => 'password123',
                'employee_id' => 'ADMIN001',
                'department' => 'Administration',
            ],
            [
                'role' => 'HR Manager',
                'first_name' => 'John',
                'last_name' => 'Richardson',
                'email' => 'hr.manager@university.edu',
                'password' => 'password123',
                'employee_id' => 'HR001',
                'department' => 'Human Resources',
            ],
            [
                'role' => 'Department Head',
                'first_name' => 'Maria',
                'last_name' => 'Garcia',
                'email' => 'dept.head@university.edu',
                'password' => 'password123',
                'employee_id' => 'DH001',
                'department' => 'Academic Affairs',
            ],
            [
                'role' => 'Finance Officer',
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'finance.officer@university.edu',
                'password' => 'password123',
                'employee_id' => 'FIN001',
                'department' => 'Finance',
            ],
            [
                'role' => 'Accountant',
                'first_name' => 'Sarah',
                'last_name' => 'Williams',
                'email' => 'accountant@university.edu',
                'password' => 'password123',
                'employee_id' => 'ACC001',
                'department' => 'Finance',
            ],
            [
                'role' => 'University President',
                'first_name' => 'Dr. Robert',
                'last_name' => 'Martinez',
                'email' => 'president@university.edu',
                'password' => 'password123',
                'employee_id' => 'PRES001',
                'department' => 'Office of the President',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            $email = $userData['email'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $email],
                array_merge($userData, [
                    'password' => bcrypt($userData['password']),
                    'user_status' => 'Active',
                    'hire_date' => now(),
                    'phone' => null,
                    'address' => null,
                    'gender' => null,
                    'dob' => null,
                    'profile_photo_path' => null,
                ])
            );

            if (!$user->hasRole($role)) {
                $user->assignRole($role);
                $this->command->info("User created: " . $email . " with role: " . $role);
            } else {
                $this->command->info("User already exists: " . $email . " with role: " . $role);
            }
        }

        $this->command->info("\nAll role-based users processed successfully!");
        $this->command->line("\n=== User Accounts ===");
        $this->command->line("Super Admin          : superadmin@university.edu");
        $this->command->line("HR Manager           : hr.manager@university.edu");
        $this->command->line("Department Head      : dept.head@university.edu");
        $this->command->line("Finance Officer      : finance.officer@university.edu");
        $this->command->line("Accountant           : accountant@university.edu");
        $this->command->line("University President : president@university.edu");
        $this->command->line("\nPassword: password123");
    }
}
