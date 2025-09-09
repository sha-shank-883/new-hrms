@extends('layouts.manager')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Attendance Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('manager.attendances.index') }}">Attendance</a></li>
        <li class="breadcrumb-item active">Attendance #{{ $attendance->id }}</li>
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
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-clipboard-list me-1"></i>
                        Attendance Information
                    </div>
                    <div>
                        @if($attendance->status == 'present')
                            <span class="badge bg-success">Present</span>
                        @elseif($attendance->status == 'absent')
                            <span class="badge bg-danger">Absent</span>
                        @elseif($attendance->status == 'late')
                            <span class="badge bg-warning">Late</span>
                        @elseif($attendance->status == 'half_day')
                            <span class="badge bg-info">Half Day</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Employee Information</h5>
                            <p><strong>Name:</strong> {{ $attendance->user->name }}</p>
                            <p><strong>Email:</strong> {{ $attendance->user->email }}</p>
                            <p><strong>Department:</strong> {{ $attendance->user->department->name ?? 'N/A' }}</p>
                            <p><strong>Position:</strong> {{ $attendance->user->position ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Attendance Details</h5>
                            <p><strong>Date:</strong> {{ $attendance->date->format('Y-m-d') }} ({{ $attendance->date->format('l') }})</p>
                            <p>
                                <strong>Check In:</strong> 
                                @if($attendance->check_in)
                                    {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') }}
                                    @if($attendance->is_late)
                                        <span class="badge bg-warning">Late</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </p>
                            <p>
                                <strong>Check Out:</strong> 
                                @if($attendance->check_out)
                                    {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') }}
                                @else
                                    -
                                @endif
                            </p>
                            <p>
                                <strong>Working Hours:</strong> 
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
                            </p>
                        </div>
                    </div>
                    
                    @if($attendance->notes)
                    <div class="mb-3">
                        <h5>Notes</h5>
                        <div class="p-3 bg-light rounded">
                            {{ $attendance->notes }}
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <h5>Timeline</h5>
                        <ul class="list-group">
                            @if($attendance->check_in)
                            <li class="list-group-item">
                                <i class="fas fa-sign-in-alt text-primary me-2"></i>
                                <strong>Check In:</strong> {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') }}
                                @if($attendance->is_late)
                                    <span class="badge bg-warning">Late</span>
                                @endif
                            </li>
                            @endif
                            
                            @if($attendance->check_out)
                            <li class="list-group-item">
                                <i class="fas fa-sign-out-alt text-success me-2"></i>
                                <strong>Check Out:</strong> {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') }}
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('manager.attendances.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Monthly Summary
                </div>
                <div class="card-body">
                    <h5>{{ $attendance->date->format('F Y') }} Summary</h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>Present</span>
                            <span>{{ $monthlySummary['present'] }} days</span>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" role="progressbar" 
                                style="width: {{ ($monthlySummary['present'] / $monthlySummary['workingDays']) * 100 }}%" 
                                aria-valuenow="{{ $monthlySummary['present'] }}" aria-valuemin="0" aria-valuemax="{{ $monthlySummary['workingDays'] }}">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>Late</span>
                            <span>{{ $monthlySummary['late'] }} days</span>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-warning" role="progressbar" 
                                style="width: {{ ($monthlySummary['late'] / $monthlySummary['workingDays']) * 100 }}%" 
                                aria-valuenow="{{ $monthlySummary['late'] }}" aria-valuemin="0" aria-valuemax="{{ $monthlySummary['workingDays'] }}">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>Half Day</span>
                            <span>{{ $monthlySummary['halfDay'] }} days</span>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-info" role="progressbar" 
                                style="width: {{ ($monthlySummary['halfDay'] / $monthlySummary['workingDays']) * 100 }}%" 
                                aria-valuenow="{{ $monthlySummary['halfDay'] }}" aria-valuemin="0" aria-valuemax="{{ $monthlySummary['workingDays'] }}">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span>Absent</span>
                            <span>{{ $monthlySummary['absent'] }} days</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" role="progressbar" 
                                style="width: {{ ($monthlySummary['absent'] / $monthlySummary['workingDays']) * 100 }}%" 
                                aria-valuenow="{{ $monthlySummary['absent'] }}" aria-valuemin="0" aria-valuemax="{{ $monthlySummary['workingDays'] }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('manager.attendances.report', ['employee_id' => $attendance->user_id, 'month' => $attendance->date->format('m'), 'year' => $attendance->date->format('Y')]) }}" class="btn btn-primary">
                            <i class="fas fa-chart-bar me-1"></i> View Full Report
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Recent Attendance
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($recentAttendances as $record)
                            <a href="{{ route('manager.attendances.show', $record->id) }}" class="list-group-item list-group-item-action {{ $record->id == $attendance->id ? 'active' : '' }}">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $record->date->format('d M Y') }}</h6>
                                    <small>
                                        @if($record->status == 'present')
                                            <span class="badge {{ $record->id == $attendance->id ? 'bg-light text-dark' : 'bg-success' }}">Present</span>
                                        @elseif($record->status == 'absent')
                                            <span class="badge {{ $record->id == $attendance->id ? 'bg-light text-dark' : 'bg-danger' }}">Absent</span>
                                        @elseif($record->status == 'late')
                                            <span class="badge {{ $record->id == $attendance->id ? 'bg-light text-dark' : 'bg-warning' }}">Late</span>
                                        @elseif($record->status == 'half_day')
                                            <span class="badge {{ $record->id == $attendance->id ? 'bg-light text-dark' : 'bg-info' }}">Half Day</span>
                                        @endif
                                    </small>
                                </div>
                                @if($record->check_in)
                                    <p class="mb-1">Check In: {{ \Carbon\Carbon::parse($record->check_in)->format('H:i') }}</p>
                                @endif
                                @if($record->check_out)
                                    <small>Check Out: {{ \Carbon\Carbon::parse($record->check_out)->format('H:i') }}</small>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('manager.attendances.index', ['employee_id' => $attendance->user_id]) }}" class="btn btn-sm btn-primary w-100">
                        View All Records
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection