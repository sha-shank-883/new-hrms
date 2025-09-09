<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/check-admin-role', function () {
    $user = User::where('email', 'admin@hrms.local')->first();
    
    if (!$user) {
        return 'Admin user not found';
    }
    
    return [
        'user' => $user->name,
        'email' => $user->email,
        'roles' => $user->getRoleNames(),
        'permissions' => $user->getAllPermissions()->pluck('name')
    ];
});
