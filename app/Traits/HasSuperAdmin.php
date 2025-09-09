<?php

namespace App\Traits;

trait HasSuperAdmin
{
    /**
     * Check if the current user is the super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->email === config('superadmin.email');
    }

    /**
     * Check if the given user is the super admin
     */
    public static function isSuperAdminUser($user): bool
    {
        return $user && $user->email === config('superadmin.email');
    }
}
