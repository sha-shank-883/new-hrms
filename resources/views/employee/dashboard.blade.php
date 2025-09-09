@extends('layouts.admin')

@section('title', 'My Dashboard')

@push('styles')
<style>
    .info-box {
        background: #fff;
        border-radius: 0.25rem;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    }
    .info-box-icon {
        display: block;
        float: left;
        height: 60px;
        width: 60px;
        text-align: center;
        font-size: 1.875rem;
        line-height: 60px;
        border-radius: 0.25rem;
        color: white;
    }
    .info-box-content {
        padding-left: 75px;
    }
    .info-box-text {
        display: block;
        font-size: 1rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #6c757d;
    }
    .card {
        margin-bottom: 1.5rem;
        border: 1px solid rgba(0,0,0,.125);
        border-radius: 0.25rem;
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,.125);
        padding: 0.75rem 1.25rem;
    }
    .card-title {
        margin-bottom: 0;
        font-size: 1.25rem;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title m-0">Welcome, {{ Auth::user()->name }}!</h3>
                    <span class="text-muted">{{ now()->format('l, F j, Y') }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Quick Actions -->
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="bi bi-calendar-plus"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Request Leave</span>
                                    <a href="{{ route('employee.leave_requests.create') }}" class="btn btn-sm btn-primary mt-2">
                                        Apply Now
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="bi bi-clock-history"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Attendance</span>
                                    @php
                                        // Initialize with default values if $currentAttendance is null
                                        $hasCheckedIn = isset($currentAttendance) && $currentAttendance && $currentAttendance->check_in;
                                        $hasCheckedOut = $hasCheckedIn && $currentAttendance->check_out;
                                    @endphp

                                    @if($hasCheckedIn && !$hasCheckedOut)
                                        <form action="{{ route('employee.attendances.check-out') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger mt-2">
                                                <i class="bi bi-box-arrow-right"></i> Check Out
                                            </button>
                                        </form>
                                    @elseif(!$hasCheckedIn)
                                        <form action="{{ route('employee.attendances.check-in') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success mt-2">
                                                <i class="bi bi-box-arrow-in-right"></i> Check In
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">
                                            @if(isset($currentAttendance) && $currentAttendance)
                                                Already checked out for today
                                            @else
                                                Not checked in today
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="bi bi-calendar-week"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Leave Balance</span>
                                    <span class="info-box-number">
                                        {{ $leaveBalances['available'] ?? 0 }} / {{ $leaveBalances['total'] ?? 0 }} days
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary">
                                    <i class="bi bi-file-earmark-text"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Payslips</span>
                                    <a href="{{ route('employee.payslips.index') }}" class="btn btn-sm btn-secondary mt-2">
                                        View All
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="row mt-4">
                        <!-- Recent Leave Requests -->
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="bi bi-calendar-check me-2"></i>My Leave Requests
                                    </h6>
                                    <a href="{{ route('employee.leave_requests.index') }}" class="btn btn-sm btn-outline-primary">
                                        View All
                                    </a>
                                </div>
                                <div class="card-body p-0">
                                    @if(isset($recentLeaveRequests) && $recentLeaveRequests->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th class="border-0">Type</th>
                                                        <th class="border-0">Date</th>
                                                        <th class="border-0">Status</th>
                                                        <th class="border-0 text-end">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($recentLeaveRequests as $request)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    @php
                                                                        $icon = [
                                                                            'annual' => 'sun',
                                                                            'sick' => 'bandaid',
                                                                            'casual' => 'cup-straw',
                                                                            'unpaid' => 'currency-dollar'
                                                                        ][strtolower($request->leaveType->name ?? '')] ?? 'calendar';
                                                                    @endphp
                                                                    <i class="bi bi-{{ $icon }} me-2 text-primary"></i>
                                                                    {{ $request->leaveType->name ?? 'N/A' }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="small text-muted">
                                                                    {{ $request->start_date->format('M d') }} - {{ $request->end_date->format('M d, Y') }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $statusClass = [
                                                                        'pending' => 'warning',
                                                                        'approved' => 'success',
                                                                        'rejected' => 'danger',
                                                                        'cancelled' => 'secondary'
                                                                    ][$request->status] ?? 'secondary';
                                                                @endphp
                                                                <span class="badge bg-soft-{{ $statusClass }} text-{{ $statusClass }} px-2 py-1">
                                                                    {{ ucfirst($request->status) }}
                                                                </span>
                                                            </td>
                                                            <td class="text-end">
                                                                <a href="{{ route('employee.leave_requests.show', $request->id) }}"
                                                                   class="btn btn-sm btn-outline-primary btn-icon"
                                                                   data-bs-toggle="tooltip"
                                                                   title="View Details">
                                                                    <i class="bi bi-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center p-5">
                                            <div class="mb-3">
                                                <i class="bi bi-inbox text-muted" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                            </div>
                                            <h5 class="text-muted mb-2">No Leave Requests</h5>
                                            <p class="text-muted mb-4">You haven't submitted any leave requests yet.</p>
                                            <a href="{{ route('employee.leave_requests.create') }}" class="btn btn-primary">
                                                <i class="bi bi-plus-circle me-1"></i> Request Leave
                                            </a>
                                        </div>
                                    @endif
                                    </div>
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
        font-size: 1.5rem;
        justify-content: center;
        text-align: center;
        width: 50px;
        color: #fff;
    }
    .info-box .info-box-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        line-height: 1.4;
        flex: 1;
        padding: 0 10px;
    }
    .info-box .info-box-text {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 0.9rem;
    }
    .info-box .info-box-number {
        display: block;
        font-weight: 700;
    }
    .attendance-calendar .table th {
        font-weight: 500;
        padding: 0.5rem;
    }
    .attendance-calendar .table td {
        height: 40px;
        padding: 0.25rem;
        position: relative;
    }
</style>
@endpush
