<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        \App\Models\Department::class => \App\Policies\DepartmentPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for common permissions
        Gate::before(function (User $user, $ability) {
            // Super admin has all permissions
            if ($user->hasRole('super_admin')) {
                return true;
            }
        });

        // Define gates for common permissions
        $permissions = [
            'view_dashboard',
            'manage_settings',
            'view_audit_logs',
            'backup_database',
            'restore_database'
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, function (User $user) use ($permission) {
                return $user->hasPermissionTo($permission);
            });
        }
    }
}
