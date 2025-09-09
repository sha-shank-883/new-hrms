@extends('layouts.manager')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Department Leave Requests</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Dashboard</a></li>
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
    
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['totalRequests'] }}</h5>
                            <div>Total Requests</div>
                        </div>
                        <div>
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">All Time</div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['pendingRequests'] }}</h5>
                            <div>Pending Requests</div>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('manager.leave-requests.index', ['status' => 'pending']) }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['approvedRequests'] }}</h5>
                            <div>Approved Requests</div>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('manager.leave-requests.index', ['status' => 'approved']) }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $stats['rejectedRequests'] }}</h5>
                            <div>Rejected Requests</div>
                        </div>
                        <div>
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('manager.leave-requests.index', ['status' => 'rejected']) }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Leave Requests
        </div>
        <div class="card-body">
            <div class="mb-3">
                <form action="{{ route('manager.leave-requests.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="leave_type_id" class="form-label">Leave Type</label>
                        <select class="form-select" id="leave_type_id" name="leave_type_id">
                            <option value="">All Types</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="employee_id" class="form-label">Employee</label>
                        <select class="form-select" id="employee_id" name="employee_id">
                            <option value="">All Employees</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="d-grid gap-2 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="table-responsive">
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
                            <th>Requested On</th>
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
                                @elseif($request->status == 'canceled')
                                    <span class="badge bg-secondary">Canceled</span>
                                @endif
                            </td>
                            <td>{{ $request->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('manager.leave-requests.show', $request->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($request->status == 'pending')
                                    <a href="{{ route('manager.leave-requests.approve', $request->id) }}" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this leave request?')">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="#" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    @endif
                                </div>
                                
                                <!-- Reject Modal -->
                                <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $request->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="rejectModalLabel{{ $request->id }}">Reject Leave Request</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('manager.leave-requests.reject', $request->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="rejection_reason" class="form-label">Rejection Reason</label>
                                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
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
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
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