<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@hrms.local'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        // Assign super admin role with debug logging
        try {
            if (!$admin->hasRole('super_admin')) {
                $admin->assignRole('super_admin');
                \Log::info('Assigned super_admin role to admin user', [
                    'user_id' => $admin->id,
                    'assigned_roles' => $admin->getRoleNames()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to assign super_admin role', [
                'error' => $e->getMessage(),
                'available_roles' => \Spatie\Permission\Models\Role::all()->pluck('name')
            ]);
        }

        // Create or update HR manager
        $hrManager = User::updateOrCreate(
            ['email' => 'hr@hrms.local'],
            [
                'name' => 'HR Manager',
                'password' => Hash::make('password'),
                'is_admin' => false,
            ]
        );

        // Assign HR manager role with debug logging
        try {
            if (!$hrManager->hasRole('hr_manager')) {
                $hrManager->assignRole('hr_manager');
                \Log::info('Assigned hr_manager role to HR user', [
                    'user_id' => $hrManager->id,
                    'assigned_roles' => $hrManager->getRoleNames()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to assign hr_manager role', [
                'error' => $e->getMessage()
            ]);
        }

        // Create a demo employee
        $employee = User::create([
            'name' => 'John Doe',
            'email' => 'employee@hrms.local',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

// Assign employee role with debug logging
        try {
            $employee->assignRole('employee');
            \Log::info('Assigned employee role to user', [
                'user_id' => $employee->id,
                'assigned_roles' => $employee->getRoleNames()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to assign employee role', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
