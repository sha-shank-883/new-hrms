@extends('layouts.admin')

@section('title', 'Employee Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Employee Management</h1>
        <a href="{{ route('admin.employees.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Employee
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Employees</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.employees.index') }}" method="GET" class="form-inline">
                <div class="form-group mb-2 mr-2">
                    <label for="department_id" class="sr-only">Department</label>
                    <select name="department_id" id="department_id" class="form-control">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2 mr-2">
                    <label for="status" class="sr-only">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
                <div class="form-group mb-2 mr-2">
                    <label for="search" class="sr-only">Search</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search by name or ID" value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-primary mb-2">Filter</button>
                <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary mb-2 ml-2">Reset</a>
            </form>
        </div>
    </div>

    <!-- Employee List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Employee List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Joining Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td>{{ $employee->employee_id }}</td>
                            <td>{{ $employee->user->name }}</td>
                            <td>{{ $employee->user->email }}</td>
                            <td>{{ $employee->department->name }}</td>
                            <td>{{ $employee->position }}</td>
                            <td>
                                @if($employee->employment_status == 'active')
                                <span class="badge badge-success">Active</span>
                                @elseif($employee->employment_status == 'on_leave')
                                <span class="badge badge-warning">On Leave</span>
                                @elseif($employee->employment_status == 'terminated')
                                <span class="badge badge-danger">Terminated</span>
                                @endif
                            </td>
                            <td>{{ $employee->joining_date->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('admin.employees.show', $employee->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.employees.destroy', $employee->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this employee?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No employees found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-end">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "paging": false,
            "ordering": true,
            "info": false,
            "searching": false
        });
    });
</script>
@endsection