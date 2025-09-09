@extends('layouts.admin')

@section('title', 'Departments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Departments List</h3>
                    @can('create', \App\Models\Department::class)
                    <a href="{{ route('admin.departments.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Add Department
                    </a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Manager</th>
                                    <th>Employees</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments as $department)
                                <tr>
                                    <td>{{ $department->id }}</td>
                                    <td>{{ $department->name }}</td>
                                    <td>{{ $department->code }}</td>
                                    <td>
                                        @if($department->manager)
                                            {{ $department->manager->name }}
                                        @else
                                            <span class="text-muted">No manager</span>
                                        @endif
                                    </td>
                                    <td>{{ $department->employees_count ?? 0 }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @can('update', $department)
                                            <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @endcan
                                            @can('delete', $department)
                                            <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this department?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No departments found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $departments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
