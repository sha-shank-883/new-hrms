@extends('layouts.admin')

@section('title', 'Edit Department: ' . $department->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Department: {{ $department->name }}</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $department->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Department Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code', $department->code) }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                     id="description" name="description" rows="3">{{ old('description', $department->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="manager_id" class="form-label">Department Manager</label>
                            <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id">
                                <option value="">-- Select Manager --</option>
                                @foreach($managers as $id => $name)
                                    <option value="{{ $id }}" {{ (old('manager_id', $department->manager_id) == $id) ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('manager_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to List
                            </a>
                            <div>
                                <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-info">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Update Department
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
