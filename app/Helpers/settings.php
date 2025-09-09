<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

if (!function_exists('setting')) {
    /**
     * Get or set a setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        // Cache settings for 24 hours
        $settings = Cache::remember('app_settings', 1440, function () {
            return DB::table('settings')->pluck('value', 'key');
        });

        return $settings[$key] ?? $default;
    }
}
