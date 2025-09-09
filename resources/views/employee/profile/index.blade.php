@extends('layouts.employee')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
        <a href="{{ route('employee.profile.edit') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-edit fa-sm text-white-50"></i> Edit Profile
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
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Date of Birth:</div>
                        <div class="col-7">{{ $employee->date_of_birth->format('M d, Y') }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Gender:</div>
                        <div class="col-7">{{ ucfirst($employee->gender) }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Marital Status:</div>
                        <div class="col-7">{{ ucfirst($employee->marital_status) }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Address:</div>
                        <div class="col-7">{{ $employee->address }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Emergency Contact:</div>
                        <div class="col-7">{{ $employee->emergency_contact ?: 'Not provided' }}</div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-5 font-weight-bold">Relationship:</div>
                        <div class="col-7">{{ $employee->emergency_contact_relationship ?: 'Not provided' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="col-xl-4 col-md-12 mb-4">
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Payrolls</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $employee->payrolls->count() }}
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
                        <a href="{{ route('employee.leave_requests.index') }}" class="btn btn-sm btn-info mr-2">
                            <i class="fas fa-calendar-alt fa-sm"></i> My Leave Requests
                        </a>
                        <a href="{{ route('employee.attendances.index') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-clock fa-sm"></i> My Attendance
                        </a>
                    </div>
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
                                @forelse($recentAttendance as $attendance)
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
                                @forelse($recentLeaveRequests as $leaveRequest)
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
