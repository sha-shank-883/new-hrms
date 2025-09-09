<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Department;
use Illuminate\Auth\Access\Response;

class DepartmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin', 'hr_manager', 'department_manager']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Department $department): bool
    {
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return true;
        }
        
        // Department managers can only view their own department
        if ($user->hasRole('department_manager')) {
            return $user->department_id === $department->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['super_admin', 'admin', 'hr_manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Department $department): bool
    {
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return true;
        }
        
        // Department managers can only update their own department
        if ($user->hasRole('department_manager')) {
            return $user->department_id === $department->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Department $department): bool
    {
        // Only super_admin and admin can delete departments
        return $user->hasRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can manage department employees.
     */
    public function manageEmployees(User $user, Department $department): bool
    {
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return true;
        }
        
        // Department managers can manage employees in their own department
        if ($user->hasRole('department_manager')) {
            return $user->department_id === $department->id;
        }
        
        return false;
    }
}
