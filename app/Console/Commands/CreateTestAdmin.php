<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestAdmin extends Command
{
    protected $signature = 'create:test-admin';
    protected $description = 'Create a test admin user';

    public function handle()
    {
        $email = 'admin@example.com';
        
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Test Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        
        if (!$user->hasRole('super_admin')) {
            $user->assignRole('super_admin');
            $this->info('Assigned super_admin role to test admin user');
        }
        
        $this->info('Test admin user created/updated successfully!');
        $this->line('Email: ' . $email);
        $this->line('Password: password');
        
        return 0;
    }
}
