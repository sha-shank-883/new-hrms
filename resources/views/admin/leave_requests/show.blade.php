@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Leave Request Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.leave-requests.index') }}">Leave Requests</a></li>
        <li class="breadcrumb-item active">Details</li>
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
                        <i class="fas fa-info-circle me-1"></i>
                        Leave Request Information
                    </div>
                    <div>
                        @if($leaveRequest->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($leaveRequest->status == 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($leaveRequest->status == 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @elseif($leaveRequest->status == 'cancelled')
                            <span class="badge bg-secondary">Cancelled</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Employee Information</h5>
                            <p><strong>Name:</strong> {{ $leaveRequest->user->name }}</p>
                            <p><strong>Email:</strong> {{ $leaveRequest->user->email }}</p>
                            <p><strong>Department:</strong> {{ $leaveRequest->user->department->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>Leave Information</h5>
                            <p>
                                <strong>Leave Type:</strong>
                                <span class="badge" style="background-color: {{ $leaveRequest->leaveType->color }}">
                                    {{ $leaveRequest->leaveType->name }}
                                </span>
                            </p>
                            <p><strong>Start Date:</strong> {{ $leaveRequest->start_date->format('Y-m-d') }}</p>
                            <p><strong>End Date:</strong> {{ $leaveRequest->end_date->format('Y-m-d') }}</p>
                            <p><strong>Days:</strong> {{ $leaveRequest->days }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h5>Reason for Leave</h5>
                        <div class="p-3 bg-light rounded">
                            {{ $leaveRequest->reason }}
                        </div>
                    </div>

                    @if($leaveRequest->status == 'rejected' && $leaveRequest->rejection_reason)
                        <div class="mb-3">
                            <h5>Rejection Reason</h5>
                            <div class="p-3 bg-light rounded">
                                {{ $leaveRequest->rejection_reason }}
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <h5>Timeline</h5>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <i class="fas fa-plus-circle text-primary me-2"></i>
                                <strong>Created:</strong> {{ $leaveRequest->created_at->format('Y-m-d H:i') }}
                            </li>

                            @if($leaveRequest->status != 'pending')
                                <li class="list-group-item">
                                    @if($leaveRequest->status == 'approved')
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <strong>Approved:</strong>
                                    @elseif($leaveRequest->status == 'rejected')
                                        <i class="fas fa-times-circle text-danger me-2"></i>
                                        <strong>Rejected:</strong>
                                    @elseif($leaveRequest->status == 'cancelled')
                                        <i class="fas fa-ban text-secondary me-2"></i>
                                        <strong>Cancelled:</strong>
                                    @endif
                                    {{ $leaveRequest->updated_at->format('Y-m-d H:i') }}
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-tasks me-1"></i>
                    Actions
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>

                        @if($leaveRequest->status == 'pending')
                            <form action="{{ route('admin.leave_requests.approve', $leaveRequest->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check me-1"></i> Approve Request
                                </button>
                            </form>

                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fas fa-times me-1"></i> Reject Request
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Leave Balance
                </div>
                <div class="card-body">
                    <p><strong>Annual Leave:</strong> {{ $leaveBalance['annual'] ?? 0 }} days remaining</p>
                    <p><strong>Sick Leave:</strong> {{ $leaveBalance['sick'] ?? 0 }} days remaining</p>
                    <p><strong>Other Leave:</strong> {{ $leaveBalance['other'] ?? 0 }} days remaining</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.leave_requests.reject', $leaveRequest->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Leave Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                        <div class="form-text">Please provide a reason for rejecting this leave request.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
