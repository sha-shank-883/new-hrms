<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    // Super admin credentials from config
    private const SUPERADMIN_EMAIL = 'superadmin@admin.com'; // Default, overridden by config
    private const SUPERADMIN_PASSWORD = 'Admin@12345';

    public function run()
    {
        // Create or get the super admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        
        // Get all permissions if they exist
        try {
            $permissions = Permission::all();
            // Sync all permissions to super admin role if permissions exist
            if ($permissions->isNotEmpty()) {
                $superAdminRole->syncPermissions($permissions);
            }
        } catch (\Exception $e) {
            // If permissions table doesn't exist yet, we'll continue without syncing permissions
            $this->command->warn('Could not sync permissions: ' . $e->getMessage());
        }
        
        // Create or update super admin user
        $superAdmin = User::updateOrCreate(
            ['email' => self::SUPERADMIN_EMAIL],
            [
                'name' => 'Super Admin',
                'password' => Hash::make(self::SUPERADMIN_PASSWORD),
                'email_verified_at' => now(),
            ]
        );
        
        // Assign super admin role
        $superAdmin->syncRoles([$superAdminRole]);
        
        $this->command->info('Super Admin credentials:');
        $this->command->info('Email: ' . self::SUPERADMIN_EMAIL);
        $this->command->info('Password: ' . self::SUPERADMIN_PASSWORD);
        $this->command->info('Role: super_admin');
    }
}
