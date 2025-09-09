@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Create Leave Type</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.leave_types.index') }}">Leave Types</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i>
            Create New Leave Type
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('admin.leave_types.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    <small class="text-muted">Example: Annual Leave, Sick Leave, etc.</small>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                </div>
                
                <div class="mb-3">
                    <label for="days_allowed" class="form-label">Days Allowed Per Year <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="days_allowed" name="days_allowed" value="{{ old('days_allowed') }}" min="1" required>
                </div>
                
                <div class="mb-3">
                    <label for="color" class="form-label">Color</label>
                    <input type="color" class="form-control form-control-color" id="color" name="color" value="{{ old('color', '#3498db') }}" title="Choose a color for this leave type">
                    <small class="text-muted">This color will be used to identify this leave type in calendars and reports.</small>
                </div>
                
                <div class="mb-3">
                    <label for="requires_approval" class="form-label">Requires Approval</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="requires_approval" name="requires_approval" value="1" {{ old('requires_approval') ? 'checked' : '' }}>
                        <label class="form-check-label" for="requires_approval">
                            This leave type requires manager approval
                        </label>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="is_active" class="form-label">Status</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.leave_types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Leave Type
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection