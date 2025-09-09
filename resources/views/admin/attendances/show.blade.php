@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Attendance Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.attendances.index') }}">Attendance</a></li>
        <li class="breadcrumb-item active">View Details</li>
    </ol>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-clock me-1"></i>
                        Attendance Record Details
                    </div>
                    <div>
                        <a href="{{ route('admin.attendances.edit', $attendance->id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('admin.attendances.destroy', $attendance->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this attendance record?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="card-title">Employee Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%">Employee Name</th>
                                    <td>{{ $attendance->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Employee ID</th>
                                    <td>{{ $attendance->user->id }}</td>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <td>{{ $attendance->user->department->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Position</th>
                                    <td>{{ $attendance->user->position ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="card-title">Attendance Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 40%">Date</th>
                                    <td>{{ $attendance->date->format('Y-m-d') }} ({{ $attendance->date->format('l') }})</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($attendance->status == 'present')
                                            <span class="badge bg-success">Present</span>
                                        @elseif($attendance->status == 'absent')
                                            <span class="badge bg-danger">Absent</span>
                                        @elseif($attendance->status == 'late')
                                            <span class="badge bg-warning">Late</span>
                                        @elseif($attendance->status == 'half_day')
                                            <span class="badge bg-info">Half Day</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Check In</th>
                                    <td>
                                        @if($attendance->check_in)
                                            {{ $attendance->check_in->format('H:i:s') }}
                                            @if($attendance->is_late)
                                                <span class="badge bg-warning ms-2">Late</span>
                                            @endif
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Check Out</th>
                                    <td>
                                        @if($attendance->check_out)
                                            {{ $attendance->check_out->format('H:i:s') }}
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="card-title">Additional Information</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 20%">Working Hours</th>
                                    <td>
                                        @if($attendance->check_in && $attendance->check_out)
                                            {{ $attendance->working_hours }} hours
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td>{{ $attendance->notes ?? 'No notes available' }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $attendance->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $attendance->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Monthly Attendance Summary
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>{{ $attendance->date->format('F Y') }} Summary</h6>
                        <div class="progress mb-2" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $monthlyStats['presentPercentage'] }}%" aria-valuenow="{{ $monthlyStats['presentPercentage'] }}" aria-valuemin="0" aria-valuemax="100">Present ({{ $monthlyStats['presentCount'] }})</div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $monthlyStats['latePercentage'] }}%" aria-valuenow="{{ $monthlyStats['latePercentage'] }}" aria-valuemin="0" aria-valuemax="100">Late ({{ $monthlyStats['lateCount'] }})</div>
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $monthlyStats['halfDayPercentage'] }}%" aria-valuenow="{{ $monthlyStats['halfDayPercentage'] }}" aria-valuemin="0" aria-valuemax="100">Half Day ({{ $monthlyStats['halfDayCount'] }})</div>
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $monthlyStats['absentPercentage'] }}%" aria-valuenow="{{ $monthlyStats['absentPercentage'] }}" aria-valuemin="0" aria-valuemax="100">Absent ({{ $monthlyStats['absentCount'] }})</div>
                        </div>
                        <small class="text-muted">Total Working Days: {{ $monthlyStats['totalDays'] }}</small>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Count</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-success">Present</span></td>
                                    <td>{{ $monthlyStats['presentCount'] }}</td>
                                    <td>{{ number_format($monthlyStats['presentPercentage'], 1) }}%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning">Late</span></td>
                                    <td>{{ $monthlyStats['lateCount'] }}</td>
                                    <td>{{ number_format($monthlyStats['latePercentage'], 1) }}%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info">Half Day</span></td>
                                    <td>{{ $monthlyStats['halfDayCount'] }}</td>
                                    <td>{{ number_format($monthlyStats['halfDayPercentage'], 1) }}%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">Absent</span></td>
                                    <td>{{ $monthlyStats['absentCount'] }}</td>
                                    <td>{{ number_format($monthlyStats['absentPercentage'], 1) }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.attendances.report', ['user_id' => $attendance->user_id]) }}" class="btn btn-info btn-sm w-100">
                            <i class="fas fa-chart-line me-1"></i> View Full Attendance Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection