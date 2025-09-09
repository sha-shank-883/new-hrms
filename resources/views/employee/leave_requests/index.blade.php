@extends('layouts.employee')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>My Leave Requests</span>
                    <a href="{{ route('employee.leave_requests.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Request Leave
                    </a>
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
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Annual Leave</h5>
                                        <p class="card-text display-6">{{ $leaveBalance['annual'] ?? 0 }}</p>
                                        <p class="card-text">days remaining</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Sick Leave</h5>
                                        <p class="card-text display-6">{{ $leaveBalance['sick'] ?? 0 }}</p>
                                        <p class="card-text">days remaining</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Pending Requests</h5>
                                        <p class="card-text display-6">{{ $pendingCount }}</p>
                                        <p class="card-text">awaiting approval</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5 class="card-title">Approved</h5>
                                        <p class="card-text display-6">{{ $approvedCount }}</p>
                                        <p class="card-text">upcoming leaves</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Days</th>
                                    <th>Status</th>
                                    <th>Requested On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($leaveRequests as $request)
                                    <tr>
                                        <td>
                                            <span class="badge" style="background-color: {{ $request->leaveType->color }}">
                                                {{ $request->leaveType->name }}
                                            </span>
                                        </td>
                                        <td>{{ $request->start_date->format('Y-m-d') }}</td>
                                        <td>{{ $request->end_date->format('Y-m-d') }}</td>
                                        <td>{{ $request->days }}</td>
                                        <td>
                                            @if($request->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($request->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($request->status == 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @elseif($request->status == 'cancelled')
                                                <span class="badge bg-secondary">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>{{ $request->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('employee.leave_requests.show', $request->id) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                @if($request->status == 'pending')
                                                    <form action="{{ route('employee.leave_requests.cancel', $request->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this leave request?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No leave requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $leaveRequests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
