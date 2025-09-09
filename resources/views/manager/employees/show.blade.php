@extends('layouts.manager')

@section('title', 'Employee Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Employee Details</h1>
        <a href="{{ route('manager.employees.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
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

    <div class="row">
        <!-- Employee Information -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img class="img-profile rounded-circle" src="https://source.unsplash.com/QAB-WJcbgJk/60x60" width="100">
                        <h4 class="mt-3">{{ $employee->user->name }}</h4>
                        <p class="text-muted">{{ $employee->position }}</p>
                        <div>
                            @if($employee->employment_status == 'active')
                            <span class="badge badge-success">Active</span>
                            @elseif($employee->employment_status == 'on_leave')
                            <span class="badge badge-warning">On Leave</span>
                            @elseif($employee->employment_status == 'terminated')
                            <span class="badge badge-danger">Terminated</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Employee ID:</div>
                        <div class="col-7">{{ $employee->employee_id }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Department:</div>
                        <div class="col-7">{{ $employee->department->name }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Email:</div>
                        <div class="col-7">{{ $employee->user->email }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Phone:</div>
                        <div class="col-7">{{ $employee->phone }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Joining Date:</div>
                        <div class="col-7">{{ $employee->joining_date->format('M d, Y') }}</div>
                    </div>
                    
                    @if($employee->employment_status == 'terminated' && $employee->termination_date)
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Termination Date:</div>
                        <div class="col-7">{{ $employee->termination_date->format('M d, Y') }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Attendance Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $employee->attendances->where('status', 'present')->count() }} / {{ $employee->attendances->count() ?: 1 }}
                                ({{ $employee->attendances->count() > 0 ? round(($employee->attendances->where('status', 'present')->count() / $employee->attendances->count()) * 100) : 0 }}%)
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Leave Requests</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $employee->leaveRequests->count() }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Approved Leaves</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $employee->leaveRequests->where('status', 'approved')->count() }}
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Employment Duration</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $employee->joining_date->diffInMonths(now()) }} months
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('manager.leave-requests.index', ['employee_id' => $employee->id]) }}" class="btn btn-sm btn-info mr-2">
                            <i class="fas fa-calendar-alt fa-sm"></i> View Leave Requests
                        </a>
                        <a href="{{ route('manager.attendances.index', ['employee_id' => $employee->id]) }}" class="btn btn-sm btn-success">
                            <i class="fas fa-clock fa-sm"></i> View Attendance
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Leave Balance -->
        <div class="col-xl-4 col-md-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Leave Balance</h6>
                </div>
                <div class="card-body">
                    @forelse($leaveBalances as $balance)
                    <div class="row mb-3">
                        <div class="col-7">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">{{ $balance->leaveType->name }}</div>
                        </div>
                        <div class="col-5">
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $balance->remaining_days }} / {{ $balance->total_days }}
                            </div>
                        </div>
                    </div>
                    <div class="progress mb-4">
                        <div class="progress-bar" role="progressbar" style="width: {{ ($balance->remaining_days / $balance->total_days) * 100 }}%" 
                            aria-valuenow="{{ $balance->remaining_days }}" aria-valuemin="0" aria-valuemax="{{ $balance->total_days }}"></div>
                    </div>
                    @empty
                    <p class="text-center">No leave balance information available.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Recent Attendance -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Attendance</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Working Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->attendances->sortByDesc('date')->take(5) as $attendance)
                                <tr>
                                    <td>{{ $attendance->date->format('M d, Y') }}</td>
                                    <td>
                                        @if($attendance->status == 'present')
                                        <span class="badge badge-success">Present</span>
                                        @elseif($attendance->status == 'absent')
                                        <span class="badge badge-danger">Absent</span>
                                        @elseif($attendance->status == 'late')
                                        <span class="badge badge-warning">Late</span>
                                        @elseif($attendance->status == 'half_day')
                                        <span class="badge badge-info">Half Day</span>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->check_in ? $attendance->check_in->format('h:i A') : 'N/A' }}</td>
                                    <td>{{ $attendance->check_out ? $attendance->check_out->format('h:i A') : 'N/A' }}</td>
                                    <td>{{ $attendance->working_hours ?? 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No attendance records found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Leave Requests -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Leave Requests</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Days</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->leaveRequests->sortByDesc('created_at')->take(5) as $leaveRequest)
                                <tr>
                                    <td>{{ $leaveRequest->leaveType->name }}</td>
                                    <td>{{ $leaveRequest->start_date->format('M d, Y') }}</td>
                                    <td>{{ $leaveRequest->end_date->format('M d, Y') }}</td>
                                    <td>{{ $leaveRequest->days }}</td>
                                    <td>
                                        @if($leaveRequest->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                        @elseif($leaveRequest->status == 'approved')
                                        <span class="badge badge-success">Approved</span>
                                        @elseif($leaveRequest->status == 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                        @elseif($leaveRequest->status == 'cancelled')
                                        <span class="badge badge-secondary">Cancelled</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No leave requests found.</td>
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