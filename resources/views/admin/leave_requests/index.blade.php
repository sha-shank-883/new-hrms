@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Leave Requests</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Leave Requests</li>
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

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-calendar-alt me-1"></i>
                Leave Requests
            </div>
            <div>
                <a href="{{ route('admin.leave-requests.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Create Leave Request
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <form action="{{ route('admin.leave-requests.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="leave_type" class="form-label">Leave Type</label>
                        <select class="form-select" id="leave_type" name="leave_type_id">
                            <option value="">All Types</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </form>
            </div>

            <table id="leaveRequestsTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveRequests as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td>{{ $request->user->name }}</td>
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
                                <a href="{{ route('admin.leave-requests.show', $request->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($request->status == 'pending')
                                    <form action="{{ route('admin.leave-requests.approve', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.leave-requests.reject', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $leaveRequests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#leaveRequestsTable').DataTable({
            paging: false,
            info: false,
        });
    });
</script>
@endsection
