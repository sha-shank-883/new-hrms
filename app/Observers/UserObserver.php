<?php

namespace App\Observers;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserObserver
{
    protected function isSuperAdmin(User $user): bool
    {
        return $user->email === config('superadmin.email') || $user->hasRole('super_admin');
    }

    public function deleting(User $user)
    {
        if ($this->isSuperAdmin($user)) {
            throw new \RuntimeException('Super admin user cannot be deleted.');
        }
    }

    public function updating(User $user)
    {
        $originalUser = $user->getOriginal();
        
        // Prevent email change for super admin
        if ($this->isSuperAdmin($user) && $user->isDirty('email')) {
            throw new \RuntimeException('Super admin email cannot be changed.');
        }

        // If this was the super admin, prevent role changes
        if (isset($originalUser['email']) && 
            $originalUser['email'] === config('superadmin.email') && 
            $user->isDirty('roles')) {
            throw new \RuntimeException('Super admin roles cannot be modified.');
        }
    }
}
