@extends('layouts.manager')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Department Attendance</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
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
    
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['totalEmployees'] }}</h5>
                            <div>Total Employees</div>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">In Department</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['presentToday'] }}</h5>
                            <div>Present Today</div>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">{{ now()->format('d M Y') }}</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['lateToday'] }}</h5>
                            <div>Late Today</div>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">{{ now()->format('d M Y') }}</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['absentToday'] }}</h5>
                            <div>Absent Today</div>
                        </div>
                        <div>
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('manager.attendances.report') }}">View Report</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Attendance Records
            </div>
            <div>
                <a href="{{ route('manager.attendances.report') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-chart-bar me-1"></i> View Report
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <form action="{{ route('manager.attendances.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="employee_id" class="form-label">Employee</label>
                        <select class="form-select" id="employee_id" name="employee_id">
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                            <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                            <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                            <option value="half_day" {{ request('status') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
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
                            <th>ID</th>
                            <th>Employee</th>
                            <th>Date</th>
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
                            <td>{{ $attendance->id }}</td>
                            <td>{{ $attendance->user->name }}</td>
                            <td>{{ $attendance->date->format('Y-m-d') }}</td>
                            <td>
                                @if($attendance->check_in)
                                    {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($attendance->check_out)
                                    {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($attendance->check_in && $attendance->check_out)
                                    @php
                                        $checkIn = \Carbon\Carbon::parse($attendance->check_in);
                                        $checkOut = \Carbon\Carbon::parse($attendance->check_out);
                                        $diffInHours = $checkIn->diffInHours($checkOut);
                                        $diffInMinutes = $checkIn->diffInMinutes($checkOut) % 60;
                                        echo $diffInHours . 'h ' . $diffInMinutes . 'm';
                                    @endphp
                                @else
                                    -
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
                                <a href="{{ route('manager.attendances.show', $attendance->id) }}" class="btn btn-info btn-sm">
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