@extends('layouts.employee')

@section('title', 'Payslip Details')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Payslip Details</h1>
        <div>
            <a href="{{ route('employee.payrolls.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Back to Payslips
            </a>
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print fa-sm text-white-50 mr-1"></i> Print Payslip
            </button>
        </div>
    </div>

    <!-- Payslip Information -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Payslip for {{ $payroll->month }} {{ $payroll->year }}</h6>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <!-- Employee Information -->
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Employee Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Employee ID:</strong></td>
                            <td>{{ $payroll->employee->employee_id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Name:</strong></td>
                            <td>{{ $payroll->employee->user->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Department:</strong></td>
                            <td>{{ $payroll->employee->department->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Position:</strong></td>
                            <td>{{ $payroll->employee->position }}</td>
                        </tr>
                        <tr>
                            <td><strong>Join Date:</strong></td>
                            <td>{{ $payroll->employee->join_date->format('M d, Y') }}</td>
                        </tr>
                    </table>
                </div>
                
                <!-- Payroll Information -->
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Payroll Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%"><strong>Payroll Period:</strong></td>
                            <td>{{ $payroll->month }} {{ $payroll->year }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @if($payroll->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($payroll->status == 'processed')
                                    <span class="badge badge-info">Processed</span>
                                @elseif($payroll->status == 'paid')
                                    <span class="badge badge-success">Paid</span>
                                @endif
                            </td>
                        </tr>
                        @if($payroll->payment_date)
                        <tr>
                            <td><strong>Payment Date:</strong></td>
                            <td>{{ $payroll->payment_date->format('M d, Y') }}</td>
                        </tr>
                        @endif
                        @if($payroll->payment_method)
                        <tr>
                            <td><strong>Payment Method:</strong></td>
                            <td>{{ $payroll->payment_method }}</td>
                        </tr>
                        @endif
                        @if($payroll->payment_reference)
                        <tr>
                            <td><strong>Reference:</strong></td>
                            <td>{{ $payroll->payment_reference }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <hr>

            <!-- Salary Breakdown -->
            <div class="row">
                <div class="col-md-12">
                    <h5 class="font-weight-bold mb-3">Salary Breakdown</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Basic Salary</strong></td>
                                    <td class="text-right">${{ number_format($payroll->basic_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Allowances</strong></td>
                                    <td class="text-right">${{ number_format($payroll->allowances, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Deductions</strong></td>
                                    <td class="text-right">-${{ number_format($payroll->deductions, 2) }}</td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Net Salary</strong></td>
                                    <td class="text-right"><strong>${{ number_format($payroll->net_salary, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($payroll->notes)
            <div class="row mt-4">
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
</div>

<!-- Print Styles -->
<style type="text/css" media="print">
    @page {
        size: auto;
        margin: 10mm;
    }
    body {
        background-color: #fff;
        margin: 0;
        padding: 0;
    }
    .container-fluid {
        width: 100%;
        padding: 0;
    }
    .no-print, .no-print * {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .card-header {
        background-color: #fff !important;
        border-bottom: 2px solid #4e73df !important;
    }
    .btn, .sidebar, .topbar, footer {
        display: none !important;
    }
    #wrapper #content-wrapper {
        margin-left: 0 !important;
    }
</style>
@endsection