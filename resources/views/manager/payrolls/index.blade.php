@extends('layouts.manager')

@section('title', 'Payroll Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Payroll Management</h1>
    </div>

    <!-- Alerts -->
    @include('layouts.alerts')

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Payrolls</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('manager.payrolls.index') }}" method="GET" class="form-inline">
                <div class="form-group mb-2 mr-2">
                    <label for="month" class="mr-2">Month:</label>
                    <select name="month" id="month" class="form-control">
                        <option value="">All Months</option>
                        <option value="January" {{ request('month') == 'January' ? 'selected' : '' }}>January</option>
                        <option value="February" {{ request('month') == 'February' ? 'selected' : '' }}>February</option>
                        <option value="March" {{ request('month') == 'March' ? 'selected' : '' }}>March</option>
                        <option value="April" {{ request('month') == 'April' ? 'selected' : '' }}>April</option>
                        <option value="May" {{ request('month') == 'May' ? 'selected' : '' }}>May</option>
                        <option value="June" {{ request('month') == 'June' ? 'selected' : '' }}>June</option>
                        <option value="July" {{ request('month') == 'July' ? 'selected' : '' }}>July</option>
                        <option value="August" {{ request('month') == 'August' ? 'selected' : '' }}>August</option>
                        <option value="September" {{ request('month') == 'September' ? 'selected' : '' }}>September</option>
                        <option value="October" {{ request('month') == 'October' ? 'selected' : '' }}>October</option>
                        <option value="November" {{ request('month') == 'November' ? 'selected' : '' }}>November</option>
                        <option value="December" {{ request('month') == 'December' ? 'selected' : '' }}>December</option>
                    </select>
                </div>
                <div class="form-group mb-2 mr-2">
                    <label for="year" class="mr-2">Year:</label>
                    <select name="year" id="year" class="form-control">
                        <option value="">All Years</option>
                        @php
                            $currentYear = date('Y');
                            $startYear = $currentYear - 5;
                        @endphp
                        @for($year = $currentYear; $year >= $startYear; $year--)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group mb-2 mr-2">
                    <label for="department" class="mr-2">Department:</label>
                    <select name="department" id="department" class="form-control">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2 mr-2">
                    <label for="status" class="mr-2">Status:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Processed</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="form-group mb-2 mr-2">
                    <label for="search" class="mr-2">Search:</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Employee name or ID" value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-primary mb-2">Apply Filters</button>
                <a href="{{ route('manager.payrolls.index') }}" class="btn btn-secondary mb-2 ml-2">Reset</a>
            </form>
        </div>
    </div>

    <!-- Payroll List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Payroll Records</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
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
                        @forelse($payrolls as $payroll)
                            <tr>
                                <td>{{ $payroll->employee->employee_id }}</td>
                                <td>{{ $payroll->employee->user->name }}</td>
                                <td>{{ $payroll->employee->department->name }}</td>
                                <td>{{ $payroll->month }} {{ $payroll->year }}</td>
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
                                    <a href="{{ route('manager.payrolls.show', $payroll->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No payroll records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                {{ $payrolls->appends(request()->except('page'))->links() }}
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