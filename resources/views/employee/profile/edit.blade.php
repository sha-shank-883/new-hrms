@extends('layouts.employee')

@section('title', 'Edit Profile')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Profile</h1>
        <a href="{{ route('employee.profile.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Profile
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Update Profile Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('employee.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-lg-6">
                                <h5 class="mb-3">Personal Information</h5>
                                
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $employee->user->name) }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $employee->user->email) }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password">Password (leave blank to keep current)</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                                
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}">
                                </div>
                            </div>
                            
                            <!-- Additional Information -->
                            <div class="col-lg-6">
                                <h5 class="mb-3">Additional Information</h5>
                                
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $employee->address) }}</textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="emergency_contact">Emergency Contact</label>
                                    <input type="text" class="form-control" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $employee->emergency_contact) }}">
                                </div>
                                
                                <div class="form-group">
                                    <label for="emergency_contact_relationship">Emergency Contact Relationship</label>
                                    <input type="text" class="form-control" id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) }}">
                                </div>
                                
                                <div class="form-group">
                                    <label for="marital_status">Marital Status</label>
                                    <select class="form-control" id="marital_status" name="marital_status">
                                        <option value="single" {{ old('marital_status', $employee->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ old('marital_status', $employee->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="divorced" {{ old('marital_status', $employee->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="widowed" {{ old('marital_status', $employee->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection