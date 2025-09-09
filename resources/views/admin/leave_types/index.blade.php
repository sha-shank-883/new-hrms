@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Leave Types</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Leave Types</li>
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
                <i class="fas fa-table me-1"></i>
                Leave Types
            </div>
            <div>
                <a href="{{ route('admin.leave_types.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New Leave Type
                </a>
            </div>
        </div>
        <div class="card-body">
            <table id="leaveTypesTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Days Allowed</th>
                        <th>Color</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveTypes as $leaveType)
                    <tr>
                        <td>{{ $leaveType->id }}</td>
                        <td>{{ $leaveType->name }}</td>
                        <td>{{ $leaveType->description }}</td>
                        <td>{{ $leaveType->days_allowed }}</td>
                        <td>
                            <span class="badge" style="background-color: {{ $leaveType->color }}">
                                {{ $leaveType->color }}
                            </span>
                        </td>
                        <td>{{ $leaveType->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.leave_types.edit', $leaveType->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.leave_types.destroy', $leaveType->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this leave type?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="mt-4">
                {{ $leaveTypes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#leaveTypesTable').DataTable({
            paging: false,
            info: false,
        });
    });
</script>
@endsection