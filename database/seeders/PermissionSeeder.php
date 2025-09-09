<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Dashboard
            'view_dashboard',
            
            // User Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_user_profiles',
            'edit_user_profiles',
            
            // Role Management
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'assign_roles',
            
            // Department Management
            'view_departments',
            'create_departments',
            'edit_departments',
            'delete_departments',
            
            // Task Management
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'delete_tasks',
            'assign_tasks',
            'complete_tasks',
            
            // Leave Management
            'view_leave_requests',
            'create_leave_requests',
            'edit_leave_requests',
            'delete_leave_requests',
            'approve_leave_requests',
            'reject_leave_requests',
            'manage_own_leave',
            'manage_team_leave',
            
            // Attendance Management
            'view_attendance',
            'create_attendance',
            'edit_attendance',
            'delete_attendance',
            'view_attendance_reports',
            'export_attendance_reports',
            'manage_team_attendance',
            
            // Payroll Management
            'view_payroll',
            'create_payroll',
            'edit_payroll',
            'delete_payroll',
            'process_payroll',
            'view_salary_details',
            
            // Settings
            'manage_settings',
            'view_audit_logs',
            'backup_database',
            'restore_database'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign created permissions
        
        // Super Admin - gets all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - gets most permissions except some sensitive ones
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'view_dashboard',
            'view_users', 'create_users', 'edit_users', 'delete_users',
            'view_user_profiles', 'edit_user_profiles',
            'view_roles', 'assign_roles',
            'view_departments', 'create_departments', 'edit_departments', 'delete_departments',
            'view_leave_requests', 'approve_leave_requests', 'reject_leave_requests',
            'view_attendance', 'view_attendance_reports', 'export_attendance_reports',
            'view_payroll', 'process_payroll', 'view_salary_details',
            'manage_settings', 'view_audit_logs'
        ]);

        // Manager - gets permissions to manage their team
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->givePermissionTo([
            'view_dashboard',
            'view_user_profiles',
            'view_leave_requests', 'approve_leave_requests', 'reject_leave_requests', 'manage_team_leave',
            'view_attendance', 'view_attendance_reports', 'export_attendance_reports', 'manage_team_attendance',
            'view_payroll', 'view_salary_details'
        ]);

        // Employee - basic permissions
        $employee = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $employee->givePermissionTo([
            'view_dashboard',
            'view_user_profiles', 'edit_user_profiles',
            'view_leave_requests', 'create_leave_requests', 'edit_leave_requests', 'delete_leave_requests', 'manage_own_leave',
            'view_attendance', 'create_attendance',
            'view_payroll'
        ]);
    }
}
