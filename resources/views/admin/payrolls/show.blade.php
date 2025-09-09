@extends('layouts.admin')

@section('title', 'Payroll Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Payroll Details</h1>
        <div>
            <a href="{{ route('admin.payrolls.edit', $payroll->id) }}" class="btn btn-primary">
                <i class="fas fa-edit fa-sm text-white-50 mr-1"></i> Edit Payroll
            </a>
            <a href="{{ route('admin.payrolls.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Back to Payroll List
            </a>
        </div>
    </div>

    <!-- Payroll Information -->
    <div class="row">
        <!-- Employee Information -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Employee Information</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $payroll->employee->user->name }}</div>
                            <div class="text-sm text-gray-600">ID: {{ $payroll->employee->employee_id }}</div>
                            <div class="text-sm text-gray-600">Department: {{ $payroll->employee->department->name }}</div>
                            <div class="text-sm text-gray-600">Position: {{ $payroll->employee->position }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payroll Period -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Payroll Period</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $payroll->month }} {{ $payroll->year }}</div>
                            <div class="text-sm text-gray-600">Created: {{ $payroll->created_at->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-600">Created By: {{ $payroll->createdBy->name }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Payment Status</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($payroll->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($payroll->status == 'processed')
                                    <span class="badge badge-info">Processed</span>
                                @elseif($payroll->status == 'paid')
                                    <span class="badge badge-success">Paid</span>
                                @endif
                            </div>
                            @if($payroll->payment_date)
                                <div class="text-sm text-gray-600">Payment Date: {{ $payroll->payment_date->format('M d, Y') }}</div>
                            @endif
                            @if($payroll->payment_method)
                                <div class="text-sm text-gray-600">Method: {{ $payroll->payment_method }}</div>
                            @endif
                            @if($payroll->payment_reference)
                                <div class="text-sm text-gray-600">Reference: {{ $payroll->payment_reference }}</div>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Breakdown -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Salary Breakdown</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Basic Salary -->
                <div class="col-md-3 mb-4">
                    <div class="card border-left-primary h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Basic Salary</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($payroll->basic_salary, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Allowances -->
                <div class="col-md-3 mb-4">
                    <div class="card border-left-success h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Allowances</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($payroll->allowances, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-plus fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Deductions -->
                <div class="col-md-3 mb-4">
                    <div class="card border-left-danger h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Deductions</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($payroll->deductions, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-minus fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Net Salary -->
                <div class="col-md-3 mb-4">
                    <div class="card border-left-info h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Net Salary</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($payroll->net_salary, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-equals fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($payroll->notes)
            <div class="row mt-2">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Notes</h6>
                        </div>
                        <div class="card-body">
                            {{ $payroll->notes }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex justify-content-between">
                    <a href="{{ route('admin.payrolls.edit', $payroll->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit fa-sm mr-1"></i> Edit Payroll
                    </a>
                    <form action="{{ route('admin.payrolls.destroy', $payroll->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this payroll record?')">
                            <i class="fas fa-trash fa-sm mr-1"></i> Delete Payroll
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection