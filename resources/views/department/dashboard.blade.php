@extends('layouts.admin')

@section('title', 'Department Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h3 class="card-title mb-2 mb-md-0">Department Dashboard</h3>
                        <form method="GET" action="{{ route('department.dashboard') }}" class="d-flex align-items-center ms-md-3">
                            <label for="department" class="me-2 mb-0">Select Department:</label>
                            <select name="department_id" id="department" class="form-select form-select-sm" style="min-width: 200px;" onchange="this.form.submit()">
                                @if($departments->isEmpty())
                                    <option value="">No departments available</option>
                                @else
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ $selectedDepartmentId == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                            @if($dept->manager)
                                                (Manager: {{ $dept->manager->name }})
                                            @endif
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </form>
                    </div>
                    @if(isset($department))
                        <div class="mt-2">
                            <span class="badge bg-primary">
                                <i class="bi bi-people me-1"></i>
                                {{ $stats['team_members'] ?? 0 }} Team Members
                            </span>
                            @if($department->manager)
                                <span class="badge bg-info ms-2">
                                    <i class="bi bi-person-badge me-1"></i>
                                    Manager: {{ $department->manager->name }}
                                </span>
                            @endif
                        </div>
                    @endif
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
                                    <span class="info-box-text">Team Members</span>
                                    <span class="info-box-number">{{ $stats['team_members'] ?? 0 }}</span>
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
                                    <span class="info-box-text">Pending Approvals</span>
                                    <span class="info-box-number">{{ $stats['pending_approvals'] ?? 0 }}</span>
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

                    <!-- Team Activities -->
                    <div class="row mt-4">
                        <!-- Team Leave Requests -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title mb-0">Team Leave Requests</h3>
                                    <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="card-body p-0">
                                    @php
                                        $teamLeaveRequests = $teamLeaveRequests ?? collect();
                                    @endphp
                                    @if($teamLeaveRequests->isEmpty())
                                        <div class="p-3 text-center text-muted">
                                            <i class="bi bi-inbox fs-1"></i>
                                            <p class="mb-0">No leave requests found</p>
                                        </div>
                                    @else
                                        <ul class="list-group list-group-flush">
                                            @foreach($teamLeaveRequests as $request)
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3">
                                                                <span class="avatar avatar-sm rounded-circle bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'pending' ? 'warning' : 'danger') }} text-white d-flex align-items-center justify-content-center">
                                                                    {{ substr($request->employee->user->name ?? '?', 0, 1) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- End Team Leave Requests -->
                        <!-- Team Attendance -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h3 class="card-title mb-0">Team Attendance</h3>
                                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="card-body p-0">
                                    @php
                                        $teamAttendance = $teamAttendance ?? collect();
                                    @endphp
                                    @if($teamAttendance->isEmpty())
                                        <div class="p-3 text-center text-muted">
                                            <i class="bi bi-people fs-1"></i>
                                            <p class="mb-0">No attendance records found for today</p>
                                        </div>
                                    @else
                                        <ul class="list-group list-group-flush">
                                            @foreach($teamAttendance as $attendance)
                                                <li class="list-group-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex align-items-center">
                                                            <div class="me-3">
                                                                <span class="avatar avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
                                                                    {{ substr($attendance->employee->user->name ?? '?', 0, 1) }}
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">{{ $attendance->employee->user->name ?? 'Unknown' }}</h6>
                                                                <small class="text-muted">
                                                                    {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : 'Not checked in' }}
                                                                    @if($attendance->check_out)
                                                                        - {{ \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') }}
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <span class="badge bg-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'late' ? 'warning' : ($attendance->status === 'half_day' ? 'info' : 'secondary')) }} text-uppercase">
                                                            {{ ucfirst(str_replace('_', ' ', $attendance->status)) }}
                                                        </span>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- End Team Attendance -->
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
