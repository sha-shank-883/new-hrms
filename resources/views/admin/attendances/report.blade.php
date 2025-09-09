@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Attendance Report</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.attendances.index') }}">Attendance</a></li>
        <li class="breadcrumb-item active">Report</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-chart-bar me-1"></i>
            Generate Attendance Report
        </div>
        <div class="card-body">
            <form action="{{ route('admin.attendances.report') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="user_id" class="form-label">Employee</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">All Employees</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select class="form-select" id="department_id" name="department_id">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="month" class="form-label">Month</label>
                    <select class="form-select" id="month" name="month">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('month', date('n')) == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="year" class="form-label">Year</label>
                    <select class="form-select" id="year" name="year">
                        @for($i = date('Y'); $i >= date('Y')-5; $i--)
                            <option value="{{ $i }}" {{ request('year', date('Y')) == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Generate
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Attendance Summary
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>{{ date('F Y', mktime(0, 0, 0, request('month', date('n')), 1, request('year', date('Y')))) }} Summary</h6>
                        <div class="progress mb-2" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $summary['presentPercentage'] }}%" aria-valuenow="{{ $summary['presentPercentage'] }}" aria-valuemin="0" aria-valuemax="100">Present ({{ $summary['presentCount'] }})</div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $summary['latePercentage'] }}%" aria-valuenow="{{ $summary['latePercentage'] }}" aria-valuemin="0" aria-valuemax="100">Late ({{ $summary['lateCount'] }})</div>
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $summary['halfDayPercentage'] }}%" aria-valuenow="{{ $summary['halfDayPercentage'] }}" aria-valuemin="0" aria-valuemax="100">Half Day ({{ $summary['halfDayCount'] }})</div>
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $summary['absentPercentage'] }}%" aria-valuenow="{{ $summary['absentPercentage'] }}" aria-valuemin="0" aria-valuemax="100">Absent ({{ $summary['absentCount'] }})</div>
                        </div>
                        <small class="text-muted">Total Working Days: {{ $summary['totalDays'] }}</small>
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
                                    <td>{{ $summary['presentCount'] }}</td>
                                    <td>{{ number_format($summary['presentPercentage'], 1) }}%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning">Late</span></td>
                                    <td>{{ $summary['lateCount'] }}</td>
                                    <td>{{ number_format($summary['latePercentage'], 1) }}%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info">Half Day</span></td>
                                    <td>{{ $summary['halfDayCount'] }}</td>
                                    <td>{{ number_format($summary['halfDayPercentage'], 1) }}%</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-danger">Absent</span></td>
                                    <td>{{ $summary['absentCount'] }}</td>
                                    <td>{{ number_format($summary['absentPercentage'], 1) }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('admin.attendances.report.export', request()->all()) }}" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-file-excel me-1"></i> Export to Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-line me-1"></i>
                    Daily Attendance Trend
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Detailed Attendance Report
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="attendanceReportTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Present</th>
                            <th>Late</th>
                            <th>Half Day</th>
                            <th>Absent</th>
                            <th>Working Hours</th>
                            <th>Attendance %</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employeeStats as $stat)
                        <tr>
                            <td>{{ $stat['name'] }}</td>
                            <td>{{ $stat['department'] }}</td>
                            <td>{{ $stat['presentCount'] }}</td>
                            <td>{{ $stat['lateCount'] }}</td>
                            <td>{{ $stat['halfDayCount'] }}</td>
                            <td>{{ $stat['absentCount'] }}</td>
                            <td>{{ number_format($stat['workingHours'], 1) }} hrs</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar {{ $stat['attendancePercentage'] < 70 ? 'bg-danger' : ($stat['attendancePercentage'] < 90 ? 'bg-warning' : 'bg-success') }}" role="progressbar" style="width: {{ $stat['attendancePercentage'] }}%" aria-valuenow="{{ $stat['attendancePercentage'] }}" aria-valuemin="0" aria-valuemax="100">{{ number_format($stat['attendancePercentage'], 1) }}%</div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.attendances.report', ['user_id' => $stat['id'], 'month' => request('month', date('n')), 'year' => request('year', date('Y'))]) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    @if(request('user_id'))
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-calendar-alt me-1"></i>
            Monthly Calendar View
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered calendar-table">
                    <thead>
                        <tr>
                            <th>Mon</th>
                            <th>Tue</th>
                            <th>Wed</th>
                            <th>Thu</th>
                            <th>Fri</th>
                            <th>Sat</th>
                            <th>Sun</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($calendarData as $week)
                        <tr>
                            @foreach($week as $day)
                            <td class="{{ !$day['isCurrentMonth'] ? 'text-muted bg-light' : '' }} {{ $day['isWeekend'] ? 'bg-light' : '' }}">
                                @if($day['day'] !== null)
                                <div class="date-header d-flex justify-content-between">
                                    <span>{{ $day['day'] }}</span>
                                    @if($day['status'])
                                        @if($day['status'] == 'present')
                                            <span class="badge bg-success">P</span>
                                        @elseif($day['status'] == 'absent')
                                            <span class="badge bg-danger">A</span>
                                        @elseif($day['status'] == 'late')
                                            <span class="badge bg-warning">L</span>
                                        @elseif($day['status'] == 'half_day')
                                            <span class="badge bg-info">H</span>
                                        @endif
                                    @endif
                                </div>
                                @if($day['checkIn'])
                                <div class="small text-success">In: {{ $day['checkIn'] }}</div>
                                @endif
                                @if($day['checkOut'])
                                <div class="small text-danger">Out: {{ $day['checkOut'] }}</div>
                                @endif
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-2 small">
                <span class="badge bg-success">P</span> Present &nbsp;
                <span class="badge bg-danger">A</span> Absent &nbsp;
                <span class="badge bg-warning">L</span> Late &nbsp;
                <span class="badge bg-info">H</span> Half Day
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $('#attendanceReportTable').DataTable({
            paging: true,
            ordering: true,
            info: true,
            searching: true,
        });
        
        // Chart data
        var ctx = document.getElementById('attendanceChart');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['dates']) !!},
                datasets: [
                    {
                        label: 'Present',
                        data: {!! json_encode($chartData['present']) !!},
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1,
                        tension: 0.1
                    },
                    {
                        label: 'Late',
                        data: {!! json_encode($chartData['late']) !!},
                        backgroundColor: 'rgba(255, 193, 7, 0.2)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1,
                        tension: 0.1
                    },
                    {
                        label: 'Absent',
                        data: {!! json_encode($chartData['absent']) !!},
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>

<style>
    .calendar-table th, .calendar-table td {
        width: 14.28%;
        height: 80px;
        vertical-align: top;
        padding: 5px;
    }
    .date-header {
        font-weight: bold;
        margin-bottom: 5px;
    }
</style>
@endsection