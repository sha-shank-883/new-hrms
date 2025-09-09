@extends('layouts.employee')

@section('title', 'My Payslips')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">My Payslips</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('employee.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Payslips</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-file-invoice me-1"></i>
            My Payslips
        </div>
        <div class="card-body">
            @if($payslips->isEmpty())
                <div class="alert alert-info">
                    No payslips found.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Pay Period</th>
                                <th>Payment Date</th>
                                <th>Basic Salary</th>
                                <th>Net Salary</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payslips as $payslip)
                                <tr>
                                    <td>{{ $payslip->pay_period_start->format('M d, Y') }} - {{ $payslip->pay_period_end->format('M d, Y') }}</td>
                                    <td>{{ $payslip->payment_date->format('M d, Y') }}</td>
                                    <td>{{ number_format($payslip->basic_salary, 2) }}</td>
                                    <td>{{ number_format($payslip->net_salary, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payslip->status === 'paid' ? 'success' : 'warning' }}">
                                            {{ ucfirst($payslip->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('employee.payslips.show', $payslip->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $payslips->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
