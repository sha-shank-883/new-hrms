@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Create Leave Request</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.leave-requests.index') }}">Leave Requests</a></li>
        <li class="breadcrumb-item active">Create</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i>
            Create New Leave Request
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

            <form action="{{ route('admin.leave_requests.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="user_id" class="form-label">Employee <span class="text-danger">*</span></label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        <option value="">Select Employee</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="leave_type_id" class="form-label">Leave Type <span class="text-danger">*</span></label>
                    <select class="form-select" id="leave_type_id" name="leave_type_id" required>
                        <option value="">Select Leave Type</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}" data-days="{{ $type->days_allowed }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->days_allowed }} days/year)
                            </option>
                        @endforeach
                    </select>
                    <div id="days-info" class="form-text mt-2 d-none">
                        <span class="text-info">Available days: <span id="available-days">0</span></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="days" class="form-label">Number of Days</label>
                    <input type="number" class="form-control" id="days" name="days" value="{{ old('days', 1) }}" min="0.5" step="0.5" readonly>
                    <small class="text-muted">This will be calculated automatically based on start and end dates.</small>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required>{{ old('reason') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="mb-3" id="rejection-reason-container" style="display: none;">
                    <label for="rejection_reason" class="form-label">Rejection Reason</label>
                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="2">{{ old('rejection_reason') }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Leave Request
                    </button>
                </div>
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
            const startDate = new Date($('#start_date').val());
            const endDate = new Date($('#end_date').val());

            if (startDate && endDate && startDate <= endDate) {
                // Calculate the difference in days
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // Include both start and end days
                $('#days').val(diffDays);
            }
        }

        // Show/hide rejection reason based on status
        $('#status').change(function() {
            if ($(this).val() === 'rejected') {
                $('#rejection-reason-container').show();
            } else {
                $('#rejection-reason-container').hide();
            }
        });

        // Trigger status change on load
        $('#status').trigger('change');

        // Calculate days when dates change
        $('#start_date, #end_date').change(calculateDays);

        // Show available days when leave type is selected
        $('#leave_type_id').change(function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                const daysAllowed = selectedOption.data('days');
                $('#available-days').text(daysAllowed);
                $('#days-info').removeClass('d-none');
            } else {
                $('#days-info').addClass('d-none');
            }
        });

        // Trigger leave type change on load
        $('#leave_type_id').trigger('change');
    });
</script>
@endsection
