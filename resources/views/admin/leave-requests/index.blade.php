@extends('layouts.admin')

@section('title', 'Leave Requests')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Leave Requests</h1>
        <a href="{{ route('admin.leave-requests.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Create New Request
        </a>
    </div>

    <!-- Alert Messages -->
    @include('common.alert')

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.leave-requests.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="employee">Employee</label>
                            <select class="form-control" id="employee" name="employee">
                                <option value="">All Employees</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ request('employee') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="leave_type">Leave Type</label>
                            <select class="form-control" id="leave_type" name="leave_type">
                                <option value="">All Types</option>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('leave_type') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Leave Requests</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaveRequests as $request)
                        <tr>
                            <td>{{ $request->id }}</td>
                            <td>{{ $request->employee->user->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $request->leaveType->color ?? '#6c757d' }}; color: #fff;">
                                    {{ $request->leaveType->name }}
                                </span>
                            </td>
                            <td>{{ $request->start_date->format('M d, Y') }}</td>
                            <td>{{ $request->end_date->format('M d, Y') }}</td>
                            <td>{{ $request->days }}</td>
                            <td>
                                @if($request->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($request->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($request->status == 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.leave-requests.show', $request->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($request->status == 'pending')
                                        <form action="{{ route('admin.leave-requests.approve', $request->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this request?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $request->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    <form action="{{ route('admin.leave-requests.destroy', $request->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this request?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                
                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="rejectModalLabel">Reject Leave Request</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('admin.leave-requests.reject', $request->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="rejection_reason">Rejection Reason <span class="text-danger">*</span></label>
                                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-danger">Reject Request</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-end">
                    {{ $leaveRequests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "paging": false,
            "info": false,
        });
    });
</script>
@endsection