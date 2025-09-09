@extends('layouts.admin')

@section('title', 'Task: ' . $task->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ $task->title }}</h3>
                    <div>
                        <span class="badge me-2 
                            @if($task->priority === 'high') bg-danger
                            @elseif($task->priority === 'medium') bg-warning
                            @else bg-info @endif">
                            {{ ucfirst($task->priority) }} Priority
                        </span>
                        <span class="badge 
                            @if($task->status === 'completed') bg-success
                            @elseif($task->status === 'in_progress') bg-primary
                            @else bg-secondary @endif">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h5>Description</h5>
                        <div class="border p-3 rounded bg-light">
                            {!! nl2br(e($task->description)) !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Task Details</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Assigned To:</th>
                                            <td>{{ $task->assignedTo->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Assigned By:</th>
                                            <td>{{ $task->assignedBy->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Due Date:</th>
                                            <td>
                                                {{ $task->due_date->format('M d, Y') }}
                                                @if($task->due_date->isPast() && $task->status !== 'completed')
                                                    <span class="badge bg-danger ms-2">Overdue</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created:</th>
                                            <td>{{ $task->created_at->diffForHumans() }}</td>
                                        </tr>
                                        @if($task->completed_at)
                                        <tr>
                                            <th>Completed:</th>
                                            <td>{{ $task->completed_at->diffForHumans() }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Update Status</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.tasks.status.update', $task) }}" method="POST" class="mb-3">
                                        @csrf
                                        <div class="input-group">
                                            <select name="status" class="form-select" onchange="this.form.submit()">
                                                <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </form>
                                    
                                    <div class="progress mb-3" style="height: 25px;">
                                        @php
                                            $progress = [
                                                'pending' => 0,
                                                'in_progress' => 50,
                                                'completed' => 100
                                            ][$task->status];
                                        @endphp
                                        <div class="progress-bar {{ $task->status === 'completed' ? 'bg-success' : '' }}" 
                                             role="progressbar" 
                                             style="width: {{ $progress }}%" 
                                             aria-valuenow="{{ $progress }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">Created: {{ $task->created_at->format('M d, Y') }}</small>
                                        <small class="text-muted">Due: {{ $task->due_date->format('M d, Y') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <div>
                        @can('update', $task)
                        <a href="{{ route('admin.tasks.edit', $task) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit Task
                        </a>
                        @endcan
                    </div>
                    <div>
                        <a href="{{ route('admin.tasks.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Tasks
                        </a>
                        @can('delete', $task)
                        <form action="{{ route('admin.tasks.destroy', $task) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this task?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card-header {
        background-color: #f8f9fa;
    }
    .progress {
        background-color: #e9ecef;
    }
</style>
@endpush
@endsection
