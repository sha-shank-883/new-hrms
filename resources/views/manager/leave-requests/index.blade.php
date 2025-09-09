@extends('layouts.manager')

@section('title', 'Department Leave Requests')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Department Leave Requests</h1>
    </div>

    <!-- Alert Messages -->
    @include('common.alert')

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Leave Requests</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('manager.leave-requests.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="employee_id">Employee</label>
                        <select class="form-control" id="employee_id" name="employee_id">
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="leave_type_id">Leave Type</label>
                        <select class="form-control" id="leave_type_id" name="leave_type_id">
                            <option value="">All Types</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('manager.leave-requests.index') }}" class="btn btn-secondary">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Leave Requests Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Leave Requests</h6>
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
                                <td>{{ $request->employee->first_name }} {{ $request->employee->last_name }}</td>
                                <td>
                                    <span class="badge" style="background-color: {{ $request->leaveType->color }}">
                                        {{ $request->leaveType->name }}
                                    </span>
                                </td>
                                <td>{{ date('d M Y', strtotime($request->start_date)) }}</td>
                                <td>{{ date('d M Y', strtotime($request->end_date)) }}</td>
                                <td>{{ $request->days }}</td>
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
                                <td>
                                    <a href="{{ route('manager.leave-requests.show', $request->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($request->status == 'pending')
                                        <a href="{{ route('manager.leave-requests.approve', $request->id) }}" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this leave request?')">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $request->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        
                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel{{ $request->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="{{ route('manager.leave-requests.reject', $request->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="rejectModalLabel{{ $request->id }}">Reject Leave Request</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label for="rejection_reason">Rejection Reason</label>
                                                                <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
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
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="d-flex justify-content-center">
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
            "ordering": true,
            "info": false,
            "searching": false
        });
    });
</script>
@endsection