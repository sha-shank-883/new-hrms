@extends('layouts.manager')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css">
<style>
    .fc-day-today {
        background-color: rgba(0, 0, 0, 0.05) !important;
    }
    .fc-day-present {
        background-color: rgba(40, 167, 69, 0.2) !important;
    }
    .fc-day-absent {
        background-color: rgba(220, 53, 69, 0.2) !important;
    }
    .fc-day-late {
        background-color: rgba(255, 193, 7, 0.2) !important;
    }
    .fc-day-half_day {
        background-color: rgba(23, 162, 184, 0.2) !important;
    }
    .fc-day-weekend {
        background-color: rgba(0, 0, 0, 0.03) !important;
    }
    .fc-daygrid-day-events {
        min-height: 0 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Attendance Report</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('manager.attendances.index') }}">Attendance</a></li>
        <li class="breadcrumb-item active">Report</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filter Options
        </div>
        <div class="card-body">
            <form action="{{ route('manager.attendances.report') }}" method="GET" class="row g-3">
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
                    <label for="month" class="form-label">Month</label>
                    <select class="form-select" id="month" name="month">
                        @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}" {{ (request('month', date('m')) == $month) ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="year" class="form-label">Year</label>
                    <select class="form-select" id="year" name="year">
                        @foreach(range(date('Y')-2, date('Y')) as $year)
                            <option value="{{ $year }}" {{ (request('year', date('Y')) == $year) ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-1"></i> Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $summary['present'] }}</h5>
                            <div>Present Days</div>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">{{ number_format(($summary['present'] / $summary['workingDays']) * 100, 1) }}% of working days</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $summary['late'] }}</h5>
                            <div>Late Days</div>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">{{ number_format(($summary['late'] / $summary['workingDays']) * 100, 1) }}% of working days</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $summary['halfDay'] }}</h5>
                            <div>Half Days</div>
                        </div>
                        <div>
                            <i class="fas fa-adjust fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">{{ number_format(($summary['halfDay'] / $summary['workingDays']) * 100, 1) }}% of working days</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $summary['absent'] }}</h5>
                            <div>Absent Days</div>
                        </div>
                        <div>
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">{{ number_format(($summary['absent'] / $summary['workingDays']) * 100, 1) }}% of working days</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Daily Attendance Trend
                </div>
                <div class="card-body">
                    <canvas id="attendanceTrendChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Attendance Distribution
                </div>
                <div class="card-body">
                    <canvas id="attendanceDistributionChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Attendance Summary
            </div>
            <div>
                <a href="{{ route('manager.attendances.report.export', request()->all()) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Export to Excel
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="attendanceSummaryTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Present Days</th>
                            <th>Late Days</th>
                            <th>Half Days</th>
                            <th>Absent Days</th>
                            <th>Working Hours</th>
                            <th>Avg. Working Hours</th>
                            <th>Attendance %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employeeSummary as $empSummary)
                        <tr>
                            <td>{{ $empSummary['name'] }}</td>
                            <td>{{ $empSummary['present'] }}</td>
                            <td>{{ $empSummary['late'] }}</td>
                            <td>{{ $empSummary['halfDay'] }}</td>
                            <td>{{ $empSummary['absent'] }}</td>
                            <td>{{ $empSummary['workingHours'] }}h</td>
                            <td>{{ $empSummary['avgWorkingHours'] }}h</td>
                            <td>
                                <div class="progress">
                                    @php
                                        $percentage = ($empSummary['present'] / $summary['workingDays']) * 100;
                                        $bgClass = $percentage > 90 ? 'bg-success' : ($percentage > 75 ? 'bg-info' : ($percentage > 60 ? 'bg-warning' : 'bg-danger'));
                                    @endphp
                                    <div class="progress-bar {{ $bgClass }}" role="progressbar" 
                                        style="width: {{ $percentage }}%" 
                                        aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    @if(request('employee_id'))
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-calendar-alt me-1"></i>
            Monthly Calendar View
        </div>
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script>
    $(document).ready(function() {
        // Attendance Trend Chart
        var ctx = document.getElementById("attendanceTrendChart");
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dailyLabels) !!},
                datasets: [{
                    label: "Present",
                    lineTension: 0.3,
                    backgroundColor: "rgba(40, 167, 69, 0.2)",
                    borderColor: "rgba(40, 167, 69, 1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(40, 167, 69, 1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(40, 167, 69, 1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: {!! json_encode($dailyPresent) !!},
                },
                {
                    label: "Absent",
                    lineTension: 0.3,
                    backgroundColor: "rgba(220, 53, 69, 0.2)",
                    borderColor: "rgba(220, 53, 69, 1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(220, 53, 69, 1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(220, 53, 69, 1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: {!! json_encode($dailyAbsent) !!},
                },
                {
                    label: "Late",
                    lineTension: 0.3,
                    backgroundColor: "rgba(255, 193, 7, 0.2)",
                    borderColor: "rgba(255, 193, 7, 1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(255, 193, 7, 1)",
                    pointBorderColor: "rgba(255,255,255,0.8)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(255, 193, 7, 1)",
                    pointHitRadius: 50,
                    pointBorderWidth: 2,
                    data: {!! json_encode($dailyLate) !!},
                }],
            },
            options: {
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        ticks: {
                            min: 0,
                            max: {!! $summary['totalEmployees'] !!},
                            maxTicksLimit: 5
                        },
                        grid: {
                            color: "rgba(0, 0, 0, .125)",
                        }
                    },
                },
                plugins: {
                    legend: {
                        display: true
                    }
                }
            }
        });

        // Attendance Distribution Chart
        var pieCtx = document.getElementById("attendanceDistributionChart");
        var myPieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ["Present", "Late", "Half Day", "Absent"],
                datasets: [{
                    data: [
                        {{ $summary['present'] }}, 
                        {{ $summary['late'] }}, 
                        {{ $summary['halfDay'] }}, 
                        {{ $summary['absent'] }}
                    ],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(23, 162, 184, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                cutout: '70%',
            }
        });

        // DataTable
        $('#attendanceSummaryTable').DataTable({
            paging: false,
            info: false,
        });

        @if(request('employee_id'))
        // Calendar
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            initialDate: '{{ request('year', date('Y')) }}-{{ request('month', date('m')) }}-01',
            headerToolbar: {
                left: 'title',
                center: '',
                right: 'prev,next today'
            },
            events: {!! json_encode($calendarEvents) !!},
            eventContent: function(arg) {
                return { html: '' };
            },
            dayCellClassNames: function(arg) {
                var classes = [];
                var dateStr = arg.date.toISOString().split('T')[0];
                
                if (arg.date.getDay() === 0 || arg.date.getDay() === 6) {
                    classes.push('fc-day-weekend');
                }
                
                @foreach($calendarData as $date => $status)
                if (dateStr === '{{ $date }}') {
                    classes.push('fc-day-{{ $status }}');
                }
                @endforeach
                
                return classes;
            }
        });
        calendar.render();
        @endif
    });
</script>
@endsection