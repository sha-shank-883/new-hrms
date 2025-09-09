@extends('layouts.employee')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Attendance Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('employee.attendances.index') }}">My Attendance</a></li>
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
                <div class="card-header">
                    <i class="fas fa-clock me-1"></i>
                    Attendance Record Details
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5>Check In</h5>
                                <div class="d-flex align-items-center">
                                    <div class="display-4 me-3">
                                        {{ $attendance->check_in ? $attendance->check_in->format('H:i') : '--:--' }}
                                    </div>
                                    <div>
                                        @if($attendance->check_in)
                                            <div>{{ $attendance->check_in->format('d M Y') }}</div>
                                            @if($attendance->is_late)
                                                <span class="badge bg-warning">Late</span>
                                            @else
                                                <span class="badge bg-success">On Time</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h5>Check Out</h5>
                                <div class="d-flex align-items-center">
                                    <div class="display-4 me-3">
                                        {{ $attendance->check_out ? $attendance->check_out->format('H:i') : '--:--' }}
                                    </div>
                                    <div>
                                        @if($attendance->check_out)
                                            <div>{{ $attendance->check_out->format('d M Y') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert {{ $attendance->status == 'present' ? 'alert-success' : ($attendance->status == 'late' ? 'alert-warning' : ($attendance->status == 'half_day' ? 'alert-info' : 'alert-danger')) }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="alert-heading">Status: 
                                            @if($attendance->status == 'present')
                                                Present
                                            @elseif($attendance->status == 'absent')
                                                Absent
                                            @elseif($attendance->status == 'late')
                                                Late
                                            @elseif($attendance->status == 'half_day')
                                                Half Day
                                            @endif
                                        </h5>
                                        <div>Date: {{ $attendance->date->format('l, d F Y') }}</div>
                                    </div>
                                    <div>
                                        @if($attendance->check_in && $attendance->check_out)
                                            <h4>{{ $attendance->working_hours }} hrs</h4>
                                            <div class="text-center">Working Hours</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="card-title">Additional Information</h5>
                            <table class="table table-bordered">
                                @if($attendance->notes)
                                <tr>
                                    <th style="width: 20%">Notes</th>
                                    <td>{{ $attendance->notes }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th style="width: 20%">Created At</th>
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
                        <a href="{{ route('employee.attendances.index') }}" class="btn btn-secondary">
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
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-calendar-alt me-1"></i>
                    Attendance Timeline
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @if($attendance->check_in)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Check In</h6>
                                <p>{{ $attendance->check_in->format('h:i A') }}</p>
                                @if($attendance->is_late)
                                    <span class="badge bg-warning">Late</span>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        @if($attendance->check_out)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Check Out</h6>
                                <p>{{ $attendance->check_out->format('h:i A') }}</p>
                                @if($attendance->check_in && $attendance->check_out)
                                    <span class="badge bg-info">{{ $attendance->working_hours }} hrs</span>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        <div class="timeline-item">
                            <div class="timeline-marker {{ $attendance->status == 'present' ? 'bg-success' : ($attendance->status == 'late' ? 'bg-warning' : ($attendance->status == 'half_day' ? 'bg-info' : 'bg-danger')) }}"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Status Updated</h6>
                                <p>
                                    @if($attendance->status == 'present')
                                        Marked as Present
                                    @elseif($attendance->status == 'absent')
                                        Marked as Absent
                                    @elseif($attendance->status == 'late')
                                        Marked as Late
                                    @elseif($attendance->status == 'half_day')
                                        Marked as Half Day
                                    @endif
                                </p>
                                <small class="text-muted">{{ $attendance->updated_at->format('d M Y, h:i A') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding: 20px 0;
    }
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        left: 18px;
        height: 100%;
        width: 2px;
        background: #e9ecef;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }
    .timeline-marker {
        position: absolute;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        left: 11px;
        top: 6px;
        border: 2px solid #fff;
    }
    .timeline-content {
        margin-left: 40px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e9ecef;
    }
    .timeline-title {
        margin-top: 0;
        margin-bottom: 5px;
    }
</style>
@endsection