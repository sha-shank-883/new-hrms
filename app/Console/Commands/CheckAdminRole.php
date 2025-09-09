<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckAdminRole extends Command
{
    protected $signature = 'app:check-admin-role';
    protected $description = 'Check if admin user has the correct role';

    public function handle()
    {
        $user = User::where('email', 'admin@hrms.local')->first();
        
        if (!$user) {
            $this->error('Admin user not found');
            return 1;
        }
        
        $this->info('User: ' . $user->name);
        $this->info('Email: ' . $user->email);
        $this->info('Roles: ' . $user->getRoleNames()->implode(', '));
        $this->info('Permissions: ' . $user->getAllPermissions()->pluck('name')->implode(', '));
        
        return 0;
    }
}
