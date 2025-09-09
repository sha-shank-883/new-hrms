<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('queue.listener', function () {
            return new \Illuminate\Queue\Listener(
                $this->app->basePath()
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the UserObserver
        if (class_exists(\App\Models\User::class)) {
            \App\Models\User::observe(\App\Observers\UserObserver::class);
        }
    }
}
