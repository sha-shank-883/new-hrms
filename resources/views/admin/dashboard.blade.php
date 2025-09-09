@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">

        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Total Users</h6>
                            <h1 class="display-4">{{ $totalUsers ?? 0 }}</h1>
                        </div>
                        <i class="bi bi-people display-4"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="#" class="text-white">View Details</a>
                    <i class="bi bi-arrow-right text-white"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Total Roles</h6>
                            <h1 class="display-4">{{ $totalRoles ?? 0 }}</h1>
                        </div>
                        <i class="bi bi-person-badge display-4"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.roles.index') }}" class="text-white">View Details</a>
                    <i class="bi bi-arrow-right text-white"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Total Permissions</h6>
                            <h1 class="display-4">{{ \App\Models\Permission::count() }}</h1>
                        </div>
                        <i class="bi bi-shield-lock display-4"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.permissions.index') }}" class="text-white">View Details</a>
                    <i class="bi bi-arrow-right text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\User::latest()->take(5)->get() as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-info">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $user->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No users found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
