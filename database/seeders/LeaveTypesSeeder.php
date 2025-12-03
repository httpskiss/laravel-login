<?php

namespace Database\Seeders;

use App\Models\Leave;
use Illuminate\Database\Seeder;

class LeaveTypesSeeder extends Seeder
{
    public function run()
    {
        // Ensure leave types are standardized
        $types = Leave::getLeaveTypes();
        
        // You can also seed default permissions
        $permissions = [
            'apply_leave',
            'view_own_leave',
            'view_all_leave',
            'approve_leave',
            'manage_leave_settings',
            'generate_leave_reports',
        ];
        
        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }
}