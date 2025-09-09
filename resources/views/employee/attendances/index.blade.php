@extends('layouts.employee')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">My Attendance</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Attendance</li>
    </ol>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['present_days'] }}</h5>
                            <div>Present Days</div>
                        </div>
                        <div>
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">This Month</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['late_days'] }}</h5>
                            <div>Late Days</div>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">This Month</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['working_hours'] ?? 0 }}</h5>
                            <div>Working Hours</div>
                        </div>
                        <div>
                            <i class="fas fa-business-time fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">This Month</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['absent_days'] }}</h5>
                            <div>Absent Days</div>
                        </div>
                        <div>
                            <i class="fas fa-calendar-times fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">This Month</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-clock me-1"></i>
                        Today's Attendance
                    </div>
                    <div>
                        @if(!$todayAttendance)
                            <a href="{{ route('employee.attendances.check-in') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-sign-in-alt me-1"></i> Check In
                            </a>
                        @elseif($todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out)
                            <a href="{{ route('employee.attendances.check-out') }}" class="btn btn-danger btn-sm">
                                <i class="fas fa-sign-out-alt me-1"></i> Check Out
                            </a>
                        @else
                            <span class="badge bg-info">Completed</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if($todayAttendance)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h5>Check In</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="display-4 me-3">
                                            {{ $todayAttendance->check_in ? $todayAttendance->check_in->format('H:i') : '--:--' }}
                                        </div>
                                        <div>
                                            @if($todayAttendance->check_in)
                                                <div>{{ $todayAttendance->check_in->format('d M Y') }}</div>
                                                @if($todayAttendance->is_late)
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
                                            {{ $todayAttendance->check_out ? $todayAttendance->check_out->format('H:i') : '--:--' }}
                                        </div>
                                        <div>
                                            @if($todayAttendance->check_out)
                                                <div>{{ $todayAttendance->check_out->format('d M Y') }}</div>
                                                <div>Working Hours: {{ $todayAttendance->working_hours ?? '--' }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="alert {{ $todayAttendance->status == 'present' ? 'alert-success' : ($todayAttendance->status == 'late' ? 'alert-warning' : 'alert-danger') }}">
                                <strong>Status:</strong> 
                                @if($todayAttendance->status == 'present')
                                    Present
                                @elseif($todayAttendance->status == 'absent')
                                    Absent
                                @elseif($todayAttendance->status == 'late')
                                    Late
                                @elseif($todayAttendance->status == 'half_day')
                                    Half Day
                                @endif
                                
                                @if($todayAttendance->notes)
                                    <div class="mt-2"><strong>Notes:</strong> {{ $todayAttendance->notes }}</div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-user-clock fa-4x text-muted"></i>
                            </div>
                            <h4>You haven't checked in today</h4>
                            <p class="text-muted">Click the Check In button to record your attendance for today.</p>
                            <a href="{{ route('employee.attendances.check-in') }}" class="btn btn-success">
                                <i class="fas fa-sign-in-alt me-1"></i> Check In Now
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Monthly Summary
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>{{ date('F Y') }} Summary</h6>
                        <div class="progress mb-2" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stats['attendance_percentage'] }}%" aria-valuenow="{{ $stats['attendance_percentage'] }}" aria-valuemin="0" aria-valuemax="100">Present ({{ $stats['present_days'] }})</div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $stats['late_percentage'] ?? 0 }}%" aria-valuenow="{{ $stats['late_percentage'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100">Late ({{ $stats['late_days'] }})</div>
                            <div class="progress-bar bg-info" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">Half (0)
                            </div>
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ 100 - $stats['attendance_percentage'] }}%" aria-valuenow="{{ 100 - $stats['attendance_percentage'] }}" aria-valuemin="0" aria-valuemax="100">Absent ({{ $stats['absent_days'] }})</div>
                        </div>
                        <small class="text-muted">Total Working Days: {{ $stats['total_days'] }}</small>
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
                                    <td>{{ $stats['present_days'] }}</td>
                                    <td>{{ number_format($stats['attendance_percentage'], 1) }}%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning">Late</span></td>
                                    <td>{{ $stats['late_days'] }}</td>
                                    <td>{{ number_format($stats['late_percentage'] ?? 0, 1) }}%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info">Half Day</span></td>
                                    <td>0</td>
                                    <td>0.0%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">Absent</span></td>
                                    <td>{{ $stats['absent_days'] }}</td>
                                    <td>{{ number_format(100 - $stats['attendance_percentage'], 1) }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-history me-1"></i>
            Recent Attendance History
        </div>
        <div class="card-body">
            <div class="mb-3">
                <form action="{{ route('employee.attendances.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="month" class="form-label">Month</label>
                        <select class="form-select" id="month" name="month">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ request('month', date('n')) == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-select" id="year" name="year">
                            @for($i = date('Y'); $i >= date('Y')-5; $i--)
                                <option value="{{ $i }}" {{ request('year', date('Y')) == $i ? 'selected' : '' }}>
                                    {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="d-grid gap-2 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
                <table id="attendanceTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Working Hours</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->date->format('Y-m-d') }}</td>
                            <td>{{ $attendance->date->format('l') }}</td>
                            <td>
                                @if($attendance->check_in)
                                    {{ $attendance->check_in->format('H:i:s') }}
                                    @if($attendance->is_late)
                                        <span class="badge bg-warning">Late</span>
                                    @endif
                                @else
                                    --
                                @endif
                            </td>
                            <td>
                                @if($attendance->check_out)
                                    {{ $attendance->check_out->format('H:i:s') }}
                                @else
                                    --
                                @endif
                            </td>
                            <td>
                                @if($attendance->check_in && $attendance->check_out)
                                    {{ $attendance->working_hours }} hrs
                                @else
                                    --
                                @endif
                            </td>
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
                            <td>
                                <a href="{{ route('employee.attendances.show', $attendance->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#attendanceTable').DataTable({
            paging: false,
            info: false,
        });
    });
</script>
@endsection