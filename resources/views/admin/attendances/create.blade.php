@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Add Attendance Record</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.attendances.index') }}">Attendance</a></li>
        <li class="breadcrumb-item active">Add Record</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i>
            Create New Attendance Record
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
            
            <form action="{{ route('admin.attendances.store') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="user_id" class="form-label">Employee <span class="text-danger">*</span></label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Select Employee</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="late" {{ old('status') == 'late' ? 'selected' : '' }}>Late</option>
                                <option value="half_day" {{ old('status') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="check_in" class="form-label">Check In Time</label>
                            <input type="time" class="form-control @error('check_in') is-invalid @enderror" id="check_in" name="check_in" value="{{ old('check_in', '09:00') }}">
                            @error('check_in')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="check_out" class="form-label">Check Out Time</label>
                            <input type="time" class="form-control @error('check_out') is-invalid @enderror" id="check_out" name="check_out" value="{{ old('check_out', '18:00') }}">
                            @error('check_out')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="is_late" class="form-label">Late Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_late" name="is_late" value="1" {{ old('is_late') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_late">Mark as Late</label>
                            </div>
                            @error('is_late')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Save Attendance Record
                    </button>
                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto-set late status based on check-in time
        $('#check_in').on('change', function() {
            const checkInTime = $(this).val();
            const startTime = '09:00';
            
            if (checkInTime > startTime) {
                $('#is_late').prop('checked', true);
            } else {
                $('#is_late').prop('checked', false);
            }
        });
        
        // Toggle check-in/check-out fields based on status
        $('#status').on('change', function() {
            const status = $(this).val();
            
            if (status === 'absent') {
                $('#check_in, #check_out').val('').prop('disabled', true);
                $('#is_late').prop('checked', false).prop('disabled', true);
            } else {
                $('#check_in').val('09:00').prop('disabled', false);
                $('#check_out').val('18:00').prop('disabled', false);
                $('#is_late').prop('disabled', false);
                
                if (status === 'late') {
                    $('#is_late').prop('checked', true);
                }
            }
        });
        
        // Initial status check
        $('#status').trigger('change');
    });
</script>
@endsection