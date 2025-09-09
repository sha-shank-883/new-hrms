@extends('layouts.manager')

@section('title', 'Leave Request Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Leave Request Details</h1>
        <a href="{{ route('manager.leave-requests.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Leave Requests
        </a>
    </div>

    <!-- Alert Messages -->
    @include('common.alert')

    <div class="row">
        <!-- Leave Request Details Card -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Leave Request Information</h6>
                    <div>
                        @if($leaveRequest->status == 'pending')
                            <a href="{{ route('manager.leave-requests.approve', $leaveRequest->id) }}" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this leave request?')">
                                <i class="fas fa-check"></i> Approve
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Employee:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">{{ $leaveRequest->employee->first_name }} {{ $leaveRequest->employee->last_name }}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Department:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">{{ $leaveRequest->employee->department->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Leave Type:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        <span class="badge" style="background-color: {{ $leaveRequest->leaveType->color }}">
                                            {{ $leaveRequest->leaveType->name }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Status:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        @if($leaveRequest->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($leaveRequest->status == 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($leaveRequest->status == 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @elseif($leaveRequest->status == 'canceled')
                                            <span class="badge badge-secondary">Canceled</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Start Date:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">{{ date('F d, Y', strtotime($leaveRequest->start_date)) }}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">End Date:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">{{ date('F d, Y', strtotime($leaveRequest->end_date)) }}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Days:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">{{ $leaveRequest->days }}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label font-weight-bold">Applied On:</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">{{ date('F d, Y', strtotime($leaveRequest->created_at)) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Reason:</label>
                        <p class="form-control-plaintext border p-2 rounded bg-light">{{ $leaveRequest->reason }}</p>
                    </div>

                    @if($leaveRequest->status == 'rejected' && $leaveRequest->rejection_reason)
                        <div class="form-group">
                            <label class="font-weight-bold">Rejection Reason:</label>
                            <p class="form-control-plaintext border p-2 rounded bg-light text-danger">{{ $leaveRequest->rejection_reason }}</p>
                        </div>
                    @endif

                    @if($leaveRequest->status == 'approved')
                        <div class="form-group">
                            <label class="font-weight-bold">Approved By:</label>
                            <p class="form-control-plaintext">{{ $leaveRequest->approvedBy->name ?? 'System' }}</p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Approved On:</label>
                            <p class="form-control-plaintext">{{ date('F d, Y', strtotime($leaveRequest->approved_at)) }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Leave Balance Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Leave Balance</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Leave Type</th>
                                    <th>Available</th>
                                    <th>Used</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaveBalances as $balance)
                                    <tr>
                                        <td>
                                            <span class="badge" style="background-color: {{ $balance->leaveType->color }}">
                                                {{ $balance->leaveType->name }}
                                            </span>
                                        </td>
                                        <td>{{ $balance->available_days }}</td>
                                        <td>{{ $balance->used_days }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Leave Requests Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Leave Requests</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Dates</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLeaveRequests as $request)
                                    <tr>
                                        <td>
                                            <span class="badge" style="background-color: {{ $request->leaveType->color }}">
                                                {{ $request->leaveType->name }}
                                            </span>
                                        </td>
                                        <td>{{ date('M d', strtotime($request->start_date)) }} - {{ date('M d', strtotime($request->end_date)) }}</td>
                                        <td>
                                            @if($request->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($request->status == 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($request->status == 'rejected')
                                                <span class="badge badge-danger">Rejected</span>
                                            @elseif($request->status == 'canceled')
                                                <span class="badge badge-secondary">Canceled</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('manager.leave-requests.reject', $leaveRequest->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Leave Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Rejection Reason</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                        <small class="form-text text-muted">Please provide a reason for rejecting this leave request.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection