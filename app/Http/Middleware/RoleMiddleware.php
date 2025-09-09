<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            Log::warning('User not authenticated, redirecting to login');
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        $user = Auth::user();

        // Get required roles from the middleware parameter
        $requiredRoles = explode('|', $role);

        // Check if user has any of the required roles using Spatie's hasRole
        foreach ($requiredRoles as $requiredRole) {
            $roleName = trim($requiredRole);
            if ($user->hasRole($roleName)) {
                return $next($request);
            }
        }

        Log::warning('Unauthorized access attempt', [
            'user_id' => $user->id,
            'required_roles' => $requiredRoles,
            'user_roles' => $user->getRoleNames()->toArray()
        ]);

        return response()->view('errors.403', [], 403);
    }
}
