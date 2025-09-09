@extends('layouts.admin')

@section('title', 'View Role: ' . $role->name)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Role Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Roles</a></li>
        <li class="breadcrumb-item active">{{ $role->name }}</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-tag me-1"></i>
            Role Information
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Role Details</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Name:</th>
                            <td>{{ $role->name }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $role->description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $role->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $role->updated_at->format('M d, Y h:i A') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Assigned Permissions</h5>
                    @if($role->permissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($role->permissions as $permission)
                                        <tr>
                                            <td>{{ $permission->name }}</td>
                                            <td>{{ $permission->description ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No permissions assigned to this role.</div>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Role
                </a>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Roles
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
