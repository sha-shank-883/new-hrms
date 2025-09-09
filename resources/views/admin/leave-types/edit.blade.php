@extends('layouts.admin')

@section('title', 'Edit Leave Type')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Leave Type</h1>
        <a href="{{ route('admin.leave-types.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Leave Types
        </a>
    </div>

    <!-- Alert Messages -->
    @include('common.alert')

    <!-- Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Leave Type Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.leave-types.update', $leaveType->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $leaveType->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="days_allowed">Days Allowed <span class="text-danger">*</span></label>
                            <input type="number" step="0.5" min="0" class="form-control @error('days_allowed') is-invalid @enderror" id="days_allowed" name="days_allowed" value="{{ old('days_allowed', $leaveType->days_allowed) }}" required>
                            @error('days_allowed')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="color">Color</label>
                            <input type="color" class="form-control @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $leaveType->color ?? '#3498db') }}">
                            @error('color')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="is_active">Status</label>
                            <select class="form-control @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', $leaveType->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $leaveType->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $leaveType->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Leave Type
                </button>
            </form>
        </div>
    </div>
</div>
@endsection