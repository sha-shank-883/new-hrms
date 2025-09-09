<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeePayrollController extends Controller
{
    /**
     * Display a listing of the payslips for the authenticated employee.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $employee = $user->employee;
        
        if (!$employee) {
            return redirect()->route('employee.dashboard')
                ->with('error', 'No employee record found for your account.');
        }

        $payslips = $employee->payslips()
            ->orderBy('pay_period_start', 'desc')
            ->paginate(10);

        return view('employee.payroll.index', compact('payslips'));
    }

    /**
     * Display the specified payslip.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        $user = Auth::user();
        $payslip = Payroll::where('employee_id', $user->employee->id)
            ->findOrFail($id);

        return view('employee.payroll.show', compact('payslip'));
    }
}
