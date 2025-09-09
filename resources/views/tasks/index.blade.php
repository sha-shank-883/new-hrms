@extends('layouts.admin')

@section('title', 'Tasks Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Tasks</h3>
                    @can('create', \App\Models\Task::class)
                    <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Create Task
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Assigned To</th>
                                    <th>Due Date</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tasks as $task)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $task->title }}</td>
                                    <td>{{ $task->assignedTo->name }}</td>
                                    <td>{{ $task->due_date->format('M d, Y') }}</td>
                                    <td>
                                        @php
                                            $priorityClasses = [
                                                'low' => 'bg-info',
                                                'medium' => 'bg-warning',
                                                'high' => 'bg-danger'
                                            ];
                                        @endphp
                                        <span class="badge {{ $priorityClasses[$task->priority] ?? 'bg-secondary' }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusClasses = [
                                                'pending' => 'bg-secondary',
                                                'in_progress' => 'bg-primary',
                                                'completed' => 'bg-success'
                                            ];
                                        @endphp
                                        <span class="badge {{ $statusClasses[$task->status] ?? 'bg-secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.tasks.show', $task) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @can('update', $task)
                                            <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @endcan
                                            @can('delete', $task)
                                            <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this task?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No tasks found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $tasks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
