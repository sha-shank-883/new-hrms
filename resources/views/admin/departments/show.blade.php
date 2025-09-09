@extends('layouts.admin')

@section('title', 'Department: ' . $department->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Department Details: {{ $department->name }}</h3>
                    <div>
                        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                        @can('update', $department)
                        <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">ID</th>
                                    <td>{{ $department->id }}</td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $department->name }}</td>
                                </tr>
                                <tr>
                                    <th>Code</th>
                                    <td>{{ $department->code }}</td>
                                </tr>
                                <tr>
                                    <th>Manager</th>
                                    <td>
                                        @if($department->manager)
                                            {{ $department->manager->name }}
                                            <a href="mailto:{{ $department->manager->email }}" class="ms-2">
                                                <i class="bi bi-envelope"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">No manager assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Employees</th>
                                    <td>{{ $department->employees_count ?? 0 }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Description</h5>
                            <div class="border p-3 rounded bg-light">
                                {{ $department->description ?? 'No description available.' }}
                            </div>
                            
                            <div class="mt-4">
                                <h5>Quick Stats</h5>
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">Total Employees</h6>
                                                <h3 class="mb-0">{{ $department->employees_count ?? 0 }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h6 class="card-title">On Leave</h6>
                                                <h3 class="mb-0">0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Employees in this Department</h5>
                        @if($department->employees->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Position</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($department->employees as $employee)
                                        <tr>
                                            <td>{{ $employee->id }}</td>
                                            <td>{{ $employee->name }}</td>
                                            <td>{{ $employee->email }}</td>
                                            <td>{{ $employee->position ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $employee->is_active ? 'success' : 'secondary' }}">
                                                    {{ $employee->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">No employees found in this department.</div>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">
                                Created: {{ $department->created_at->format('M d, Y H:i') }}
                            </small>
                        </div>
                        <div>
                            <small class="text-muted">
                                Last Updated: {{ $department->updated_at->format('M d, Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
