<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DepartmentHeadsSeeder extends Seeder
{
    public function run()
    {
        $departmentHeadRole = Role::firstOrCreate(['name' => 'Department Head']);

        $departmentHeads = [
            [
                'first_name' => 'Maria',
                'middle_name' => 'Santos',
                'last_name' => 'Cruz',
                'email' => 'stcs.head@bipsu.edu.ph',
                'department' => 'STCS',
                'position' => 'Department Head',
                'designation' => 'Head, School of Technology and Computer Studies',
                'employee_id' => 'STCS-HEAD-001',
                'gender' => 'female',
            ],
            [
                'first_name' => 'Juan',
                'middle_name' => 'Reyes',
                'last_name' => 'Dela Cruz',
                'email' => 'soe.head@bipsu.edu.ph',
                'department' => 'SOE',
                'position' => 'Department Head',
                'designation' => 'Head, School of Engineering',
                'employee_id' => 'SOE-HEAD-001',
                'gender' => 'male',
            ],
            [
                'first_name' => 'Ana',
                'middle_name' => 'Gonzales',
                'last_name' => 'Reyes',
                'email' => 'scje.head@bipsu.edu.ph',
                'department' => 'SCJE',
                'position' => 'Department Head',
                'designation' => 'Head, School of Criminal Justice Education',
                'employee_id' => 'SCJE-HEAD-001',
                'gender' => 'female',
            ],
            [
                'first_name' => 'Pedro',
                'middle_name' => 'Martinez',
                'last_name' => 'Santos',
                'email' => 'snhs.head@bipsu.edu.ph',
                'department' => 'SNHS',
                'position' => 'Department Head',
                'designation' => 'Head, School of Nursing and Health Sciences',
                'employee_id' => 'SNHS-HEAD-001',
                'gender' => 'male',
            ],
            [
                'first_name' => 'Lorna',
                'middle_name' => 'Diaz',
                'last_name' => 'Fernandez',
                'email' => 'sme.head@bipsu.edu.ph',
                'department' => 'SME',
                'position' => 'Department Head',
                'designation' => 'Head, School of Management and Entrepreneurship',
                'employee_id' => 'SME-HEAD-001',
                'gender' => 'female',
            ],
            [
                'first_name' => 'Carlos',
                'middle_name' => 'Lim',
                'last_name' => 'Tan',
                'email' => 'sas.head@bipsu.edu.ph',
                'department' => 'SAS',
                'position' => 'Department Head',
                'designation' => 'Head, School of Arts and Sciences',
                'employee_id' => 'SAS-HEAD-001',
                'gender' => 'male',
            ],
            [
                'first_name' => 'Elena',
                'middle_name' => 'Torres',
                'last_name' => 'Rodriguez',
                'email' => 'sted.head@bipsu.edu.ph',
                'department' => 'STED',
                'position' => 'Department Head',
                'designation' => 'Head, School of Teacher Education',
                'employee_id' => 'STED-HEAD-001',
                'gender' => 'female',
            ],
        ];

        foreach ($departmentHeads as $head) {
            $user = User::updateOrCreate(
                ['email' => $head['email']],
                array_merge($head, [
                    'password' => Hash::make('password123'),
                    'employee_type' => 'full_time',
                    'employment_type' => 'permanent employee',
                    'user_status' => 'active',
                    'civil_status' => 'married',
                    'dob' => '1975-01-01', // Adjust as needed
                    'hire_date' => '2010-06-01',
                    'phone' => '+639123456789',
                    'address' => 'BiPSU Main Campus, Naval, Biliran',
                    'highest_educational_attainment' => 'Doctorate',
                    'work_schedule' => 'regular',
                ])
            );

            $user->assignRole($departmentHeadRole);

            $this->command->info("Created Department Head for {$head['department']}: {$head['email']}");
        }

        $this->command->info('All department heads created successfully!');
        $this->command->info('Default password for all: password123');
    }
}