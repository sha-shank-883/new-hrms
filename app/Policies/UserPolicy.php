<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view_users', 'manage_users']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Users can view their own profile
        if ($user->id === $model->id) {
            return true;
        }
        
        // Managers can view their team members
        if ($user->hasRole('manager') && $model->department_id === $user->department_id) {
            return true;
        }
        
        // Admins can view all users
        return $user->hasAnyPermission(['view_users', 'manage_users']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyPermission(['create_users', 'manage_users']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Users can update their own profile
        if ($user->id === $model->id) {
            return true;
        }
        
        // Managers can update their team members
        if ($user->hasRole('manager') && $model->department_id === $user->department_id) {
            return true;
        }
        
        return $user->hasAnyPermission(['edit_users', 'manage_users']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Prevent users from deleting themselves
        if ($user->id === $model->id) {
            return false;
        }
        
        // Managers can't delete users
        if ($user->hasRole('manager')) {
            return false;
        }
        
        return $user->hasAnyPermission(['delete_users', 'manage_users']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasAnyPermission(['restore_users', 'manage_users']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $user->hasAnyPermission(['force_delete_users', 'manage_users']);
    }
}
