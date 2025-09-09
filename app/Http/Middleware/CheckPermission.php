<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        // Super admin bypass
        if ($user->is_admin) {
            return $next($request);
        }
        
        // Check if user has the required permission
        $permissions = explode('|', $permission);
        
        foreach ($permissions as $perm) {
            if ($user->hasPermissionTo($perm)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action.');
    }
}