@extends('layouts.admin')

@section('title', 'Edit Employee')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Employee</h1>
        <a href="{{ route('admin.employees.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <!-- Alerts -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Edit Employee Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Employee Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Account Information</h5>
                        
                        <div class="form-group">
                            <label for="employee_id">Employee ID</label>
                            <input type="text" class="form-control" id="employee_id" value="{{ $employee->employee_id }}" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="name">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $employee->user->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $employee->user->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password (leave blank to keep current)</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="department_id">Department <span class="text-danger">*</span></label>
                            <select class="form-control @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="position">Position <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position', $employee->position) }}" required>
                            @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="joining_date">Joining Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('joining_date') is-invalid @enderror" id="joining_date" name="joining_date" value="{{ old('joining_date', $employee->joining_date->format('Y-m-d')) }}" required>
                            @error('joining_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="employment_status">Employment Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('employment_status') is-invalid @enderror" id="employment_status" name="employment_status" required>
                                <option value="active" {{ old('employment_status', $employee->employment_status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="on_leave" {{ old('employment_status', $employee->employment_status) == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                                <option value="terminated" {{ old('employment_status', $employee->employment_status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                            </select>
                            @error('employment_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group" id="termination_date_group" style="display: none;">
                            <label for="termination_date">Termination Date</label>
                            <input type="date" class="form-control @error('termination_date') is-invalid @enderror" id="termination_date" name="termination_date" value="{{ old('termination_date', $employee->termination_date ? $employee->termination_date->format('Y-m-d') : '') }}">
                            @error('termination_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="salary">Salary <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('salary') is-invalid @enderror" id="salary" name="salary" value="{{ old('salary', $employee->salary) }}" required>
                            @error('salary')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="mb-3">Personal Information</h5>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" required>
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="emergency_contact">Emergency Contact</label>
                            <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $employee->emergency_contact) }}">
                            @error('emergency_contact')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="emergency_contact_relationship">Emergency Contact Relationship</label>
                            <input type="text" class="form-control @error('emergency_contact_relationship') is-invalid @enderror" id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) }}">
                            @error('emergency_contact_relationship')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address', $employee->address) }}</textarea>
                            @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="date_of_birth">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth->format('Y-m-d')) }}" required>
                            @error('date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="gender">Gender <span class="text-danger">*</span></label>
                            <select class="form-control @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $employee->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="marital_status">Marital Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('marital_status') is-invalid @enderror" id="marital_status" name="marital_status" required>
                                <option value="">Select Marital Status</option>
                                <option value="single" {{ old('marital_status', $employee->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                <option value="married" {{ old('marital_status', $employee->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                <option value="divorced" {{ old('marital_status', $employee->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                <option value="widowed" {{ old('marital_status', $employee->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                            </select>
                            @error('marital_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Employee</button>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Show/hide termination date based on employment status
        $('#employment_status').change(function() {
            if ($(this).val() === 'terminated') {
                $('#termination_date_group').show();
                $('#termination_date').prop('required', true);
            } else {
                $('#termination_date_group').hide();
                $('#termination_date').prop('required', false);
            }
        });
        
        // Trigger change event on page load
        $('#employment_status').trigger('change');
    });
</script>
@endsection