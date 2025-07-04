<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // HR Manager Role
        $hrManager = Role::firstOrCreate(['name' => 'HR Manager', 'guard_name' => 'web']);
        $hrManager->syncPermissions([
            'user-list', 'user-create', 'user-edit',
            'employee-list', 'employee-create', 'employee-edit',
            'department-list',
            'attendance-list', 'attendance-approve',
            'leave-list', 'leave-approve',
            'payroll-list',
            'performance-list',
            'training-list', 'training-create',
            'report-generate'
        ]);

        // Department Head Role
        $deptHead = Role::firstOrCreate(['name' => 'Department Head', 'guard_name' => 'web']);
        $deptHead->syncPermissions([
            'employee-list',
            'attendance-list',
            'leave-list', 'leave-approve',
            'performance-list', 'performance-review',
            'training-list'
        ]);

        // Faculty/Staff Role
        $employee = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
        $employee->syncPermissions([
            'user-view-profile',
            'attendance-create',
            'leave-create',
            'training-enroll'
        ]);

        // Finance Role
        $finance = Role::firstOrCreate(['name' => 'Finance Officer', 'guard_name' => 'web']);
        $finance->syncPermissions([
            'payroll-list', 'payroll-create', 'payroll-edit',
            'report-generate', 'report-export'
        ]);
    }
}