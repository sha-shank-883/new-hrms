<?php

namespace App\Traits;

use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Exceptions\UnauthorizedException;

/**
 * Trait HasPermissionsTrait
 * 
 * Provides additional permission checking methods for models
 */
trait HasPermissionsTrait
{
    use HasRoles;

    /**
     * Check if user has any of the given permissions
     *
     * @param array|string $permissions
     * @param string $guardName
     * @return bool
     */
    public function hasAnyPermission($permissions, $guardName = 'web')
    {
        if (is_string($permissions)) {
            $permissions = explode('|', $permissions);
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermissionTo($permission, $guardName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all of the given permissions
     *
     * @param array|string $permissions
     * @param string $guardName
     * @return bool
     */
    public function hasAllPermissions($permissions, $guardName = 'web')
    {
        if (is_string($permissions)) {
            $permissions = explode('|', $permissions);
        }

        foreach ($permissions as $permission) {
            if (!$this->hasPermissionTo($permission, $guardName)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Authorize a given action for the user
     *
     * @param string|array $permission
     * @param string $guardName
     * @throws UnauthorizedException
     */
    public function authorize($permission, $guardName = 'web')
    {
        if (!$this->hasAnyPermission($permission, $guardName)) {
            throw UnauthorizedException::forPermissions((array) $permission);
        }
    }
}
