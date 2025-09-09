<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckTables extends Command
{
    protected $signature = 'app:check-tables';
    protected $description = 'Check if permission tables exist';

    public function handle()
    {
        $tables = [
            'roles',
            'permissions',
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions'
        ];

        foreach ($tables as $table) {
            $exists = Schema::hasTable($table);
            $this->info("Table '{$table}': " . ($exists ? '✅ Exists' : '❌ Missing'));
            
            if ($exists) {
                $count = DB::table($table)->count();
                $this->line("  - Records: {$count}");
            }
        }
    }
}
