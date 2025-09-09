<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

class TestAdminRoute extends Command
{
    protected $signature = 'test:admin-route';
    protected $description = 'Test the admin dashboard route';

    public function handle()
    {
        $this->info('Testing admin dashboard route...');
        
        // Create a test request
        $request = Request::create('/admin/dashboard', 'GET');
        
        // Get the response
        $response = app()->handle($request);
        
        // Output the response
        $this->line('Status Code: ' . $response->getStatusCode());
        $this->line('Content:');
        $this->line($response->getContent());
        
        return 0;
    }
}
