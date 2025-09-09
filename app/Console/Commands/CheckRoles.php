<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CheckRoles extends Command
{
    protected $signature = 'app:check-roles';
    protected $description = 'List all available roles in the system';

    public function handle()
    {
        $roles = Role::all();
        
        if ($roles->isEmpty()) {
            $this->error('No roles found in the system.');
            return 1;
        }
        
        $this->info('Available roles:');
        foreach ($roles as $role) {
            $this->line("- {$role->name} (ID: {$role->id})");
        }
        
        return 0;
    }
}
