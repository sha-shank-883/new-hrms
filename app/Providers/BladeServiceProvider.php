<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // @permission directive
        Blade::if('permission', function ($expression) {
            $permissions = is_array($expression) ? $expression : explode('|', $expression);
            return auth()->check() && auth()->user()->hasAnyPermission($permissions);
        });

        // @role directive (extended to handle multiple roles)
        Blade::if('role', function ($expression) {
            $roles = is_array($expression) ? $expression : explode('|', $expression);
            return auth()->check() && auth()->user()->hasAnyRole($roles);
        });

        // @allpermissions directive (check for all permissions)
        Blade::if('allpermissions', function ($expression) {
            $permissions = is_array($expression) ? $expression : explode('|', $expression);
            return auth()->check() && auth()->user()->hasAllPermissions($permissions);
        });
    }
}
