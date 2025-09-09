<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends \App\Http\Controllers\Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view_tasks');
        
        $tasks = Task::with(['assignedTo', 'assignedBy'])
            ->latest()
            ->paginate(10);
            
        return view('admin.tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create_tasks');
        
        $users = User::where('id', '!=', Auth::id())
            ->pluck('name', 'id');
            
        return view('admin.tasks.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create_tasks');
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date|after:now',
            'priority' => 'required|in:low,medium,high',
        ]);
        
        $validated['assigned_by'] = Auth::id();
        $validated['status'] = 'pending';
        
        Task::create($validated);
        
        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        
        $task->load(['assignedTo', 'assignedBy', 'comments.user']);
        
        return view('admin.tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        
        $users = User::where('id', '!=', Auth::id())
            ->pluck('name', 'id');
            
        return view('admin.tasks.edit', compact('task', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date|after:now',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
        ]);
        
        $task->update($validated);
        
        return redirect()->route('admin.tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete_tasks');
        
        $task->delete();
        
        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task deleted successfully.');
    }
    
    /**
     * Mark task as complete.
     */
    public function markComplete(Task $task)
    {
        $this->authorize('update', $task);
        
        $task->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        return back()->with('success', 'Task marked as completed.');
    }
    
    /**
     * Assign task to user.
     */
    public function assign(Request $request, Task $task)
    {
        $this->authorize('assign_tasks');
        
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);
        
        $task->update([
            'assigned_to' => $validated['assigned_to'],
            'status' => 'pending',
        ]);
        
        return back()->with('success', 'Task assigned successfully.');
    }
    
    /**
     * Get tasks assigned to current user.
     */
    public function myTasks()
    {
        $tasks = Task::where('assigned_to', Auth::id())
            ->with('assignedBy')
            ->latest()
            ->paginate(10);
            
        return view('tasks.my-tasks', compact('tasks'));
    }
}
