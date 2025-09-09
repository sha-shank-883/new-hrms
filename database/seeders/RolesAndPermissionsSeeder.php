<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions (use only 'name' + 'guard_name')
        $permissions = [
            // User Management
            'view_users',
            'manage_users',
            
            // Role Management
            'view_roles',
            'manage_roles',
            
            // Permission Management
            'view_permissions',
            'manage_permissions',
            
            // Employee Management
            'manage_employees',
            'view_employees',
            
            // Attendance
            'manage_attendance',
            'view_attendance',
            
            // Leave Management
            'manage_leave',
            'approve_leave',
            'view_leave_requests',
            
            // Payroll
            'manage_payroll',
            'view_payroll',
            'process_payroll',
            
            // Settings
            'manage_settings',
            'view_settings',
            
            // Documents
            'manage_documents',
            'view_documents',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $hrManager  = Role::firstOrCreate(['name' => 'hr_manager', 'guard_name' => 'web']);
        $deptManager = Role::firstOrCreate(['name' => 'department_manager', 'guard_name' => 'web']);
        $employee   = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        // Assign all permissions to super admin
        $superAdmin->givePermissionTo(Permission::all());

        // HR Manager permissions
        $hrManager->givePermissionTo([
            'view_users', 'manage_users',
            'view_roles', 'manage_roles',
            'view_permissions', 'manage_permissions',
            'view_employees', 'manage_employees',
            'view_attendance', 'manage_attendance',
            'view_leave_requests', 'manage_leave', 'approve_leave',
            'view_payroll', 'manage_payroll', 'process_payroll',
            'view_documents', 'manage_documents',
            'view_settings',
        ]);

        // Department Manager permissions
        $deptManager->givePermissionTo([
            'view_employees',
            'view_attendance', 'manage_attendance',
            'view_leave_requests', 'approve_leave',
            'view_documents',
        ]);

        // Employee permissions
        $employee->givePermissionTo([
            'view_attendance',
            'view_leave_requests',
            'view_documents',
        ]);

        // Optionally assign super_admin role to User ID 1
        $user = User::find(1);
        if ($user) {
            $user->assignRole('super_admin');
        }
    }
}
