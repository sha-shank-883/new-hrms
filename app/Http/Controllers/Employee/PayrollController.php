<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    /**
     * Display a listing of the employee's payslips.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check if user has an associated employee record
        if (!$user->employee) {
            return redirect()->route('employee.dashboard')
                ->with('error', 'Employee record not found. Please contact HR.');
        }
        
        $query = Payroll::where('employee_id', $user->employee->id);

        // Filter by month
        if ($request->has('month') && $request->month) {
            $query->where('month', $request->month);
        }
        
        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }
        
        $payrolls = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(10);
        
        // Get unique years and months from payrolls for filtering
        $years = Payroll::where('employee_id', $user->employee->id)
            ->select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
            
        $months = Payroll::where('employee_id', $user->employee->id)
            ->where('year', $request->year ?? now()->year)
            ->select('month')
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');
            
        return view('employee.payrolls.index', compact('payrolls', 'years', 'months'));
    }

    /**
     * Display the specified payslip.
     */
    public function show(Payroll $payroll)
    {
        $employeeId = Auth::user()->employee->id;
        
        // Check if the payroll belongs to the current employee
        if ($payroll->employee_id !== $employeeId) {
            abort(403, 'Unauthorized action.');
        }
        
        $payroll->load(['employee.user', 'employee.department', 'createdBy']);
        
        return view('employee.payslips.show', compact('payroll'));
    }
}