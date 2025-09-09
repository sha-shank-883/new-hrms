<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Task $task)
    {
        return $user->id === $task->assigned_to || 
               $user->id === $task->assigned_by ||
               $user->hasRole(['admin', 'manager']);
    }

    public function create(User $user)
    {
        return $user->hasRole(['admin', 'manager']);
    }

    public function update(User $user, Task $task)
    {
        return $user->id === $task->assigned_by || $user->hasRole('admin');
    }

    public function updateStatus(User $user, Task $task)
    {
        return $user->id === $task->assigned_to || $user->hasRole('admin');
    }

    public function delete(User $user, Task $task)
    {
        return $user->id === $task->assigned_by || $user->hasRole('admin');
    }
}
