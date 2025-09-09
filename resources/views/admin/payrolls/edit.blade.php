@extends('layouts.admin')

@section('title', 'Edit Payroll')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Payroll</h1>
        <a href="{{ route('admin.payrolls.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Back to Payroll List
        </a>
    </div>

    <!-- Alerts -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Edit Payroll Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Payroll Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payrolls.update', $payroll->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Employee Information (Read-only) -->
                    <div class="col-md-6 mb-3">
                        <label>Employee</label>
                        <input type="text" class="form-control" value="{{ $payroll->employee->employee_id }} - {{ $payroll->employee->user->name }} ({{ $payroll->employee->department->name }})" readonly>
                    </div>
                    
                    <!-- Period Selection -->
                    <div class="col-md-3 mb-3">
                        <label for="month">Month <span class="text-danger">*</span></label>
                        <select class="form-control" id="month" name="month" required>
                            @foreach($months as $month)
                                <option value="{{ $month }}" {{ $payroll->month == $month ? 'selected' : '' }}>{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="year">Year <span class="text-danger">*</span></label>
                        <select class="form-control" id="year" name="year" required>
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $payroll->year == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Salary Information -->
                    <div class="col-md-3 mb-3">
                        <label for="basic_salary">Basic Salary <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="basic_salary" name="basic_salary" step="0.01" min="0" value="{{ $payroll->basic_salary }}" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="allowances">Allowances</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="allowances" name="allowances" step="0.01" min="0" value="{{ $payroll->allowances }}">
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="deductions">Deductions</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="deductions" name="deductions" step="0.01" min="0" value="{{ $payroll->deductions }}">
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="net_salary">Net Salary</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="net_salary" step="0.01" min="0" value="{{ $payroll->net_salary }}" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Payment Information -->
                    <div class="col-md-4 mb-3">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="status" name="status" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ $payroll->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="payment_method">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method">
                            <option value="">Select Payment Method</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method }}" {{ $payroll->payment_method == $method ? 'selected' : '' }}>{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="payment_date">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ $payroll->payment_date ? $payroll->payment_date->format('Y-m-d') : '' }}">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="payment_reference">Payment Reference</label>
                        <input type="text" class="form-control" id="payment_reference" name="payment_reference" placeholder="Transaction ID, Check Number, etc." value="{{ $payroll->payment_reference }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes about this payroll">{{ $payroll->notes }}</textarea>
                    </div>
                </div>
                
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Update Payroll</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Calculate net salary when values change
        $('#basic_salary, #allowances, #deductions').on('input', function() {
            calculateNetSalary();
        });
        
        function calculateNetSalary() {
            var basicSalary = parseFloat($('#basic_salary').val()) || 0;
            var allowances = parseFloat($('#allowances').val()) || 0;
            var deductions = parseFloat($('#deductions').val()) || 0;
            
            var netSalary = basicSalary + allowances - deductions;
            $('#net_salary').val(netSalary.toFixed(2));
        }
    });
</script>
@endsection