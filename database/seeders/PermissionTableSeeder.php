<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Management
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'user-view-profile',
            'user-export',

            // Employee Management
            'employee-list',
            'employee-create',
            'employee-edit',
            'employee-delete',
            'employee-view-details',
            'employee-export',

            // Department Management
            'department-list',
            'department-create',
            'department-edit',
            'department-delete',

            // Position/Designation Management
            'position-list',
            'position-create',
            'position-edit',
            'position-delete',

            // Attendance Management
            'attendance-list',
            'attendance-create',
            'attendance-edit',
            'attendance-delete',
            'attendance-approve',
            'attendance-export',

            // Leave Management
            'leave-list',
            'leave-create',
            'leave-edit',
            'leave-delete',
            'leave-approve',
            'leave-report',

            // Payroll Management
            'payroll-list',
            'payroll-create',
            'payroll-edit',
            'payroll-delete',
            'payroll-approve',
            'payroll-export',

            // Performance Management
            'performance-list',
            'performance-create',
            'performance-edit',
            'performance-delete',
            'performance-review',

            // Training Management
            'training-list',
            'training-create',
            'training-edit',
            'training-delete',
            'training-enroll',

            // Reports
            'report-generate',
            'report-export',

            // System Settings
            'settings-general',
            'settings-email',
            'settings-permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }
}