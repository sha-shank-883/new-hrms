<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

class TestAdminAuth extends Command
{
    protected $signature = 'test:admin-auth';
    protected $description = 'Test admin dashboard with authentication';

    public function handle()
    {
        $user = User::where('email', 'admin@example.com')->first();
        
        if (!$user) {
            $this->error('Test admin user not found. Run create:test-admin first.');
            return 1;
        }
        
        // Log in the user
        Auth::login($user);
        $this->info('Logged in as: ' . $user->email);
        
        // Test the admin dashboard route
        $request = Request::create('/admin/dashboard', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        
        $response = app()->handle($request);
        
        $this->line('Status Code: ' . $response->getStatusCode());
        
        if ($response->isRedirection()) {
            $this->warn('Redirected to: ' . $response->headers->get('Location'));
        } else {
            $this->line('Response:');
            $this->line($response->getContent());
        }
        
        return 0;
    }
}
