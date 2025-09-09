@extends('layouts.admin')

@section('title', 'Create Leave Request')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Leave Request</h1>
        <a href="{{ route('admin.leave-requests.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Leave Requests
        </a>
    </div>

    <!-- Alert Messages -->
    @include('common.alert')

    <!-- Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Leave Request Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.leave-requests.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_id">Employee <span class="text-danger">*</span></label>
                            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('user_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="leave_type_id">Leave Type <span class="text-danger">*</span></label>
                            <select class="form-control @error('leave_type_id') is-invalid @enderror" id="leave_type_id" name="leave_type_id" required>
                                <option value="">Select Leave Type</option>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }} ({{ $type->days_allowed }} days allowed)
                                    </option>
                                @endforeach
                            </select>
                            @error('leave_type_id')
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
                            <label for="start_date">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                            @error('end_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reason">Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                    @error('reason')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    @error('status')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div id="rejection_reason_container" class="form-group" style="display: none;">
                    <label for="rejection_reason">Rejection Reason</label>
                    <textarea class="form-control @error('rejection_reason') is-invalid @enderror" id="rejection_reason" name="rejection_reason" rows="3">{{ old('rejection_reason') }}</textarea>
                    @error('rejection_reason')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Leave Request
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Calculate days between dates
        function calculateDays() {
            var startDate = new Date($('#start_date').val());
            var endDate = new Date($('#end_date').val());
            
            if (startDate && endDate) {
                // Add one day to include both start and end dates
                var diffTime = Math.abs(endDate - startDate);
                var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                
                // Display the calculated days
                $('#days').val(diffDays);
            }
        }
        
        $('#start_date, #end_date').change(calculateDays);
        
        // Show/hide rejection reason based on status
        $('#status').change(function() {
            if ($(this).val() === 'rejected') {
                $('#rejection_reason_container').show();
                $('#rejection_reason').attr('required', true);
            } else {
                $('#rejection_reason_container').hide();
                $('#rejection_reason').attr('required', false);
            }
        });
        
        // Trigger on page load for edit form
        $('#status').trigger('change');
    });
</script>
@endsection