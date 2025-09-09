@extends('layouts.hr')

@section('title', 'HR Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">HR Dashboard</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Quick Stats -->
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="bi bi-people"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Employees</span>
                                    <span class="info-box-number">{{ $stats['total_employees'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="bi bi-calendar-check"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">On Leave Today</span>
                                    <span class="info-box-number">{{ $stats['on_leave'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="bi bi-hourglass-split"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Pending Leave Requests</span>
                                    <span class="info-box-number">{{ $stats['pending_leave_requests'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Attendance Issues</span>
                                    <span class="info-box-number">{{ $stats['attendance_issues'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Recent Leave Requests</h3>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @if(isset($recentLeaveRequests) && $recentLeaveRequests->count() > 0)
                                            @foreach($recentLeaveRequests as $request)
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between">
                                                        <div>
                                                            <strong>{{ $request->employee->user->name ?? 'Unknown User' }}</strong>
                                                            <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'pending' ? 'warning' : 'danger') }}">
                                                                {{ ucfirst($request->status) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            @php
                                                                $startDate = \Carbon\Carbon::parse($request->start_date);
                                                                $endDate = \Carbon\Carbon::parse($request->end_date);
                                                            @endphp
                                                            {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}
                                                        </div>
                                                    </div>
                                                    <div class="text-muted">
                                                        {{ optional($request->leaveType)->name ?? 'N/A' }}
                                                    </div>
                                                </li>
                                            @endforeach
                                        @else
                                            <li class="list-group-item text-center text-muted">No recent leave requests found</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Upcoming Holidays</h3>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @if(isset($upcomingHolidays) && $upcomingHolidays->count() > 0)
                                            @foreach($upcomingHolidays as $holiday)
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between">
                                                        <strong>{{ $holiday->name ?? 'Unnamed Holiday' }}</strong>
                                                        <span class="text-muted">
                                                            {{ \Carbon\Carbon::parse($holiday->date)->format('M d, Y') }}
                                                        </span>
                                                    </div>
                                                    @if(!empty($holiday->description))
                                                        <div class="text-muted small mt-1">
                                                            {{ $holiday->description }}
                                                        </div>
                                                    @endif
                                                </li>
                                            @endforeach
                                        @else
                                            <li class="list-group-item text-center text-muted">
                                                No upcoming holidays found
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .info-box {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        border-radius: .25rem;
        background: #fff;
        display: flex;
        margin-bottom: 1rem;
        min-height: 80px;
        padding: .5rem;
        position: relative;
    }
    .info-box .info-box-icon {
        border-radius: .25rem;
        -ms-flex-align: center;
        align-items: center;
        display: flex;
        font-size: 1.875rem;
        justify-content: center;
        text-align: center;
        width: 70px;
        color: #fff;
    }
    .info-box .info-box-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        line-height: 1.8;
        flex: 1;
        padding: 0 10px;
    }
    .info-box .info-box-text {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .info-box .info-box-number {
        display: block;
        font-weight: 700;
    }
</style>
@endpush
