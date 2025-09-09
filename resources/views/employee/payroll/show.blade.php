@extends('layouts.employee')

@section('title', 'Payslip Details')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Payslip Details</h1>
        <a href="{{ route('employee.payslips.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Payslips
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Payslip #{{ $payslip->id }}</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Employee Information</h6>
                    <p class="mb-1"><strong>Name:</strong> {{ $payslip->employee->user->name }}</p>
                    <p class="mb-1"><strong>Employee ID:</strong> {{ $payslip->employee->employee_id }}</p>
                    <p class="mb-1"><strong>Department:</strong> {{ $payslip->employee->department->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Position:</strong> {{ $payslip->employee->position }}</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h6>Pay Period</h6>
                    <p class="mb-1"><strong>From:</strong> {{ $payslip->pay_period_start->format('M d, Y') }}</p>
                    <p class="mb-1"><strong>To:</strong> {{ $payslip->pay_period_end->format('M d, Y') }}</p>
                    <p class="mb-1"><strong>Payment Date:</strong> {{ $payslip->payment_date->format('M d, Y') }}</p>
                    <p class="mb-1">
                        <strong>Status:</strong> 
                        <span class="badge bg-{{ $payslip->status === 'paid' ? 'success' : 'warning' }}">
                            {{ ucfirst($payslip->status) }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Earnings</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <td>Basic Salary</td>
                                        <td class="text-end">{{ number_format($payslip->basic_salary, 2) }}</td>
                                    </tr>
                                    @if($payslip->allowances && is_array($payslip->allowances))
                                        @foreach($payslip->allowances as $allowance)
                                            <tr>
                                                <td>{{ $allowance['name'] }}</td>
                                                <td class="text-end">{{ number_format($allowance['amount'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr class="table-active">
                                        <th>Total Earnings</th>
                                        <th class="text-end">{{ number_format($payslip->gross_salary, 2) }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Deductions</h6>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    @if($payslip->deductions && is_array($payslip->deductions))
                                        @foreach($payslip->deductions as $deduction)
                                            <tr>
                                                <td>{{ $deduction['name'] }}</td>
                                                <td class="text-end">{{ number_format($deduction['amount'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    <tr class="table-active">
                                        <th>Total Deductions</th>
                                        <th class="text-end">{{ number_format($payslip->total_deductions, 2) }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Gross Salary</th>
                                    <td class="text-end">{{ number_format($payslip->gross_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Deductions</th>
                                    <td class="text-end">{{ number_format($payslip->total_deductions, 2) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <th>Net Salary</th>
                                    <th class="text-end">{{ number_format($payslip->net_salary, 2) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <div class="d-flex justify-content-between">
                    <div>
                        @if($payslip->status === 'paid')
                            <button class="btn btn-success" disabled>
                                <i class="fas fa-check-circle me-1"></i> Paid
                            </button>
                        @else
                            <button class="btn btn-warning" disabled>
                                <i class="fas fa-clock me-1"></i> Pending
                            </button>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('employee.payslips.download', $payslip->id) }}" 
                           class="btn btn-primary me-2">
                            <i class="fas fa-download me-1"></i> Download PDF
                        </a>
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print, .card-header {
            display: none !important;
        }
        .card {
            border: none !important;
        }
        body {
            padding: 20px;
            font-size: 12px;
        }
        .table th, .table td {
            padding: 0.3rem;
        }
    }
</style>
@endsection
