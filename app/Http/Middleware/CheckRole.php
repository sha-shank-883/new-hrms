<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        \Log::info('CheckRole middleware triggered', [
            'path' => $request->path(),
            'required_roles' => $role
        ]);

        if (!Auth::check()) {
            \Log::warning('User not authenticated, redirecting to login');
            return redirect('login');
        }

        $user = Auth::user();
        \Log::debug('User info', [
            'user_id' => $user->id,
            'is_admin' => $user->is_admin,
            'roles' => $user->roles->pluck('name')
        ]);

        // Super admin bypass
        if ($user->is_admin) {
            \Log::debug('Admin access granted');
            return $next($request);
        }

        // Check if user has the required role
        $requiredRoles = explode('|', $role);
        $userRoles = $user->roles->pluck('slug')->toArray();

        // Check if any of the required roles match user's roles
        $hasRole = !empty(array_intersect($requiredRoles, $userRoles));

        \Log::debug('Role check', [
            'required_roles' => $requiredRoles,
            'user_roles' => $userRoles,
            'has_required_role' => $hasRole ? 'true' : 'false'
        ]);

        if (!$hasRole) {
            \Log::warning('Access denied - insufficient permissions', [
                'user_id' => $user->id,
                'required_roles' => $requiredRoles,
                'user_roles' => $userRoles
            ]);
            abort(403, 'You do not have the required role to access this page.');
        }

        return $next($request);
    }
}
