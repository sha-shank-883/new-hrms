@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Role</h4>
                    <div class="card-tools">
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Roles
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $role->slug) }}" required>
                            <small class="text-muted">This is a unique identifier for the role</small>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $role->description) }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="row">
                                @foreach($permissions->groupBy(function($item) {
                                    return explode('-', $item->slug)[0];
                                }) as $group => $items)
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h5 class="mb-0 text-capitalize">{{ $group }}</h5>
                                            </div>
                                            <div class="card-body">
                                                @foreach($items as $permission)
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                            name="permissions[]" 
                                                            value="{{ $permission->id }}" 
                                                            id="permission_{{ $permission->id }}"
                                                            {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                            {{ $permission->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Role</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection