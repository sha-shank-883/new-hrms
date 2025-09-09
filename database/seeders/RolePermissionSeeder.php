<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Department permissions
            'view_departments',
            'create_departments',
            'edit_departments',
            'delete_departments',
            'manage_departments',
            
            // User permissions
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_users',
            
            // Role permissions
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'manage_roles',
            
            // Other permissions
            'view_dashboard',
            'manage_settings',
            'view_reports',
            'export_data',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'view_departments',
            'create_departments',
            'edit_departments',
            'delete_departments',
            'view_users',
            'create_users',
            'edit_users',
            'view_roles',
            'view_dashboard',
            'manage_settings',
            'view_reports',
        ]);

        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->givePermissionTo([
            'view_departments',
            'view_users',
            'view_dashboard',
            'view_reports',
        ]);

        $departmentManager = Role::firstOrCreate(['name' => 'department_manager', 'guard_name' => 'web']);
        $departmentManager->givePermissionTo([
            'view_departments',
            'edit_departments',
            'view_users',
            'view_dashboard',
        ]);

        $employee = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $employee->givePermissionTo([
            'view_dashboard',
        ]);

        // Create a super admin user if not exists
        $superAdminUser = \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        
        if (!$superAdminUser->hasRole('super_admin')) {
            $superAdminUser->assignRole('super_admin');
        }
    }
}
