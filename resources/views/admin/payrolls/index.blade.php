@extends('layouts.admin')

@section('title', 'Payroll Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Payroll Management</h1>
        <div>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#generatePayrollModal">
                <i class="fas fa-calculator fa-sm text-white-50 mr-1"></i> Generate Payroll
            </button>
            <a href="{{ route('admin.payrolls.create') }}" class="btn btn-success">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Add New Payroll
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
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
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payrolls.index') }}" method="GET" class="row">
                <div class="col-md-2 mb-3">
                    <label for="month">Month</label>
                    <select class="form-control" id="month" name="month">
                        <option value="">All Months</option>
                        @foreach($months as $month)
                            <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="year">Year</label>
                    <select class="form-control" id="year" name="year">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="department_id">Department</label>
                    <select class="form-control" id="department_id" name="department_id">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="search">Search</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Employee name or ID" value="{{ request('search') }}">
                </div>
                <div class="col-md-1 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payroll Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Payroll Records</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Month/Year</th>
                            <th>Basic Salary</th>
                            <th>Allowances</th>
                            <th>Deductions</th>
                            <th>Net Salary</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payrolls as $payroll)
                            <tr>
                                <td>{{ $payroll->employee->employee_id }}</td>
                                <td>{{ $payroll->employee->user->name }}</td>
                                <td>{{ $payroll->employee->department->name }}</td>
                                <td>{{ $payroll->month }}/{{ $payroll->year }}</td>
                                <td>${{ number_format($payroll->basic_salary, 2) }}</td>
                                <td>${{ number_format($payroll->allowances, 2) }}</td>
                                <td>${{ number_format($payroll->deductions, 2) }}</td>
                                <td>${{ number_format($payroll->net_salary, 2) }}</td>
                                <td>
                                    @if($payroll->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($payroll->status == 'processed')
                                        <span class="badge badge-info">Processed</span>
                                    @elseif($payroll->status == 'paid')
                                        <span class="badge badge-success">Paid</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.payrolls.show', $payroll->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.payrolls.edit', $payroll->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.payrolls.destroy', $payroll->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this payroll record?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No payroll records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-3">
                {{ $payrolls->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Generate Payroll Modal -->
<div class="modal fade" id="generatePayrollModal" tabindex="-1" role="dialog" aria-labelledby="generatePayrollModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.payrolls.generate') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="generatePayrollModalLabel">Generate Payroll for All Employees</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="month">Month</label>
                        <select class="form-control" id="month" name="month" required>
                            @foreach($months as $month)
                                <option value="{{ $month }}" {{ date('F') == $month ? 'selected' : '' }}>{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="year">Year</label>
                        <select class="form-control" id="year" name="year" required>
                            @php $currentYear = date('Y'); @endphp
                            @for($i = $currentYear - 2; $i <= $currentYear + 1; $i++)
                                <option value="{{ $i }}" {{ $currentYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i> This will generate payroll records for all active employees using their current salary as the basic salary. You can edit individual records afterward.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Payroll</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection