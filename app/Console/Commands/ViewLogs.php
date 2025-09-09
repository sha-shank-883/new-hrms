<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ViewLogs extends Command
{
    protected $signature = 'app:view-logs {lines=20 : Number of lines to display}';
    protected $description = 'View the latest log entries';

    public function handle()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!File::exists($logFile)) {
            $this->error('Log file not found: ' . $logFile);
            return 1;
        }
        
        $lines = (int) $this->argument('lines');
        $logContent = File::tail($logFile, $lines);
        
        $this->line($logContent);
        
        return 0;
    }
}
