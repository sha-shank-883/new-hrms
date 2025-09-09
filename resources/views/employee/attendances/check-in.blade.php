@extends('layouts.employee')

@section('title', 'Attendance Check-In/Out')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Attendance Check-In/Out</h1>
        <a href="{{ route('employee.attendances.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to My Attendance
        </a>
    </div>

    <!-- Alert Messages -->
    @include('common.alert')

    <!-- Current Date and Time -->
    <div class="row">
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Current Date and Time
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="current-time">
                                {{ now()->format('F d, Y - h:i:s A') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Check-In/Out Card -->
    <div class="row">
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Today's Attendance</h6>
                </div>
                <div class="card-body">
                    @if($todayAttendance)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Date:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-plaintext">{{ date('F d, Y', strtotime($todayAttendance->date)) }}</p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Status:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-plaintext">
                                            @if($todayAttendance->status == 'present')
                                                <span class="badge badge-success">Present</span>
                                            @elseif($todayAttendance->status == 'absent')
                                                <span class="badge badge-danger">Absent</span>
                                            @elseif($todayAttendance->status == 'late')
                                                <span class="badge badge-warning">Late</span>
                                            @elseif($todayAttendance->status == 'half_day')
                                                <span class="badge badge-info">Half Day</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Check-In:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-plaintext">
                                            @if($todayAttendance->check_in)
                                                {{ date('h:i:s A', strtotime($todayAttendance->check_in)) }}
                                                @if($todayAttendance->is_late)
                                                    <span class="badge badge-warning">Late</span>
                                                @endif
                                            @else
                                                Not checked in yet
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-4 col-form-label font-weight-bold">Check-Out:</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-plaintext">
                                            @if($todayAttendance->check_out)
                                                {{ date('h:i:s A', strtotime($todayAttendance->check_out)) }}
                                            @else
                                                Not checked out yet
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-center">
                                @if(!$todayAttendance->check_in)
                                    <form action="{{ route('employee.attendances.check-in') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="fas fa-sign-in-alt"></i> Check In
                                        </button>
                                    </form>
                                @elseif(!$todayAttendance->check_out)
                                    <form action="{{ route('employee.attendances.check-out') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-lg">
                                            <i class="fas fa-sign-out-alt"></i> Check Out
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> You have completed your attendance for today.
                                    </div>
                                    <div class="text-center">
                                        <h5>Working Hours: {{ $todayAttendance->working_hours ?? 'N/A' }}</h5>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> No attendance record found for today.
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <form action="{{ route('employee.attendances.check-in') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-sign-in-alt"></i> Check In
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Attendance Records -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Attendance Records</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Working Hours</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAttendances as $attendance)
                            <tr>
                                <td>{{ date('M d, Y', strtotime($attendance->date)) }}</td>
                                <td>{{ date('l', strtotime($attendance->date)) }}</td>
                                <td>
                                    @if($attendance->check_in)
                                        {{ date('h:i A', strtotime($attendance->check_in)) }}
                                        @if($attendance->is_late)
                                            <span class="badge badge-warning">Late</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->check_out)
                                        {{ date('h:i A', strtotime($attendance->check_out)) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
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
                                <td>{{ $attendance->working_hours ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Update current time every second
        setInterval(function() {
            var now = new Date();
            var options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: true
            };
            $('#current-time').text(now.toLocaleDateString('en-US', options).replace(',', ' -'));
        }, 1000);
    });
</script>
@endsection