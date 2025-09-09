@extends('layouts.employee')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Leave Request Details</span>
                    <span>
                        @if($leaveRequest->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($leaveRequest->status == 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($leaveRequest->status == 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @elseif($leaveRequest->status == 'cancelled')
                            <span class="badge bg-secondary">Cancelled</span>
                        @endif
                    </span>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <h5>Leave Information</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Leave Type</th>
                                    <td>
                                        <span class="badge" style="background-color: {{ $leaveRequest->leaveType->color }}">
                                            {{ $leaveRequest->leaveType->name }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Start Date</th>
                                    <td>{{ $leaveRequest->start_date->format('Y-m-d') }} ({{ $leaveRequest->start_date->format('l') }})</td>
                                </tr>
                                <tr>
                                    <th>End Date</th>
                                    <td>{{ $leaveRequest->end_date->format('Y-m-d') }} ({{ $leaveRequest->end_date->format('l') }})</td>
                                </tr>
                                <tr>
                                    <th>Number of Days</th>
                                    <td>{{ $leaveRequest->days }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($leaveRequest->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($leaveRequest->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($leaveRequest->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($leaveRequest->status == 'cancelled')
                                            <span class="badge bg-secondary">Cancelled</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Requested On</th>
                                    <td>{{ $leaveRequest->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                @if($leaveRequest->status != 'pending')
                                <tr>
                                    <th>Last Updated</th>
                                    <td>{{ $leaveRequest->updated_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5>Reason for Leave</h5>
                        <div class="p-3 bg-light rounded">
                            {{ $leaveRequest->reason }}
                        </div>
                    </div>

                    @if($leaveRequest->status == 'rejected' && $leaveRequest->rejection_reason)
                        <div class="mb-4">
                            <h5>Rejection Reason</h5>
                            <div class="p-3 bg-light rounded text-danger">
                                {{ $leaveRequest->rejection_reason }}
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('employee.leave_requests.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>

                        @if($leaveRequest->status == 'pending')
                            <form action="{{ route('employee.leave_requests.cancel', $leaveRequest->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this leave request?')">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times me-1"></i> Cancel Request
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
