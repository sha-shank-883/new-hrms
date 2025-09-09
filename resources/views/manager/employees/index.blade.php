@extends('layouts.manager')

@section('title', 'Employees')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Employees</h1>
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

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('manager.employees.index') }}" method="GET" class="form-inline">
                <div class="form-group mb-2 mr-2">
                    <label for="department_id" class="sr-only">Department</label>
                    <select class="form-control" id="department_id" name="department_id">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group mb-2 mr-2">
                    <label for="status" class="sr-only">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>
                
                <div class="form-group mb-2 mr-2">
                    <label for="search" class="sr-only">Search</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Search by name or ID" value="{{ request('search') }}">
                </div>
                
                <button type="submit" class="btn btn-primary mb-2">Apply Filters</button>
                <a href="{{ route('manager.employees.index') }}" class="btn btn-secondary mb-2 ml-2">Reset</a>
            </form>
        </div>
    </div>

    <!-- Employees List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Employees List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
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
                                <a href="{{ route('manager.employees.show', $employee->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('manager.leave-requests.index', ['employee_id' => $employee->id]) }}" class="btn btn-primary btn-sm" title="View Leave Requests">
                                    <i class="fas fa-calendar-alt"></i>
                                </a>
                                <a href="{{ route('manager.attendances.index', ['employee_id' => $employee->id]) }}" class="btn btn-success btn-sm" title="View Attendance">
                                    <i class="fas fa-clock"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">No employees found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $employees->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize datatable without pagination (we're using Laravel's pagination)
        $('#dataTable').DataTable({
            "paging": false,
            "ordering": true,
            "info": false,
            "searching": false
        });
    });
</script>
@endsection