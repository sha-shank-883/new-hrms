<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PayrollController extends Controller
{
    protected $payrollService;

    /**
     * Create a new controller instance.
     *
     * @param PayrollService $payrollService
     */
    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    /**
     * Display a listing of the payrolls.
     */
    public function index(Request $request)
    {
        $query = Payroll::with(['employee.user', 'employee.department']);

        // Filter by month
        if ($request->has('month') && $request->month) {
            $query->where('month', $request->month);
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('year', $request->year);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by department
        if ($request->has('department_id') && $request->department_id) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Search by employee name or ID
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $payrolls = $query->latest()->paginate(10);
        $departments = DB::table('departments')->get();
        
        // Get unique years and months from payrolls for filtering
        $years = Payroll::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
            
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        return view('admin.payrolls.index', compact('payrolls', 'departments', 'years', 'months'));
    }

    /**
     * Show the form for creating a new payroll.
     */
    public function create()
    {
        $employees = Employee::with(['user', 'department'])
            ->where('employment_status', '!=', 'terminated')
            ->get();
            
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        
        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 1);
        
        $paymentMethods = ['Bank Transfer', 'Cash', 'Check', 'PayPal', 'Other'];
        $statuses = ['pending', 'processed', 'paid'];
        
        return view('admin.payrolls.create', compact('employees', 'months', 'years', 'paymentMethods', 'statuses'));
    }

    /**
     * Store a newly created payroll in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|string',
            'year' => 'required|integer|min:2000|max:2100',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,processed,paid',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string',
            'payment_reference' => 'nullable|string',
        ]);
        
        // Check if a payroll already exists for this employee, month, and year
        $exists = Payroll::where('employee_id', $request->employee_id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->exists();
            
        if ($exists) {
            return redirect()->back()->with('error', 'A payroll record already exists for this employee for the selected month and year.');
        }
        
        // Calculate net salary using PayrollService
        $allowances = $request->allowances ?? 0;
        $deductions = $request->deductions ?? 0;
        $netSalary = $this->payrollService->calculateNetSalary(
            $request->basic_salary,
            $allowances,
            $deductions
        );
        
        // Create the payroll record
        Payroll::create([
            'employee_id' => $request->employee_id,
            'month' => $request->month,
            'year' => $request->year,
            'basic_salary' => $request->basic_salary,
            'allowances' => $allowances,
            'deductions' => $deductions,
            'net_salary' => $netSalary,
            'notes' => $request->notes,
            'status' => $request->status,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'payment_reference' => $request->payment_reference,
            'created_by' => Auth::id(),
        ]);
        
        return redirect()->route('admin.payrolls.index')
            ->with('success', 'Payroll record created successfully.');
    }

    /**
     * Display the specified payroll.
     */
    public function show(Payroll $payroll)
    {
        
        $payroll->load(['employee.user', 'employee.department', 'createdBy']);
        
        return view('admin.payrolls.show', compact('payroll'));
    }

    /**
     * Show the form for editing the specified payroll.
     */
    public function edit(Payroll $payroll)
    {
        
        $payroll->load(['employee.user', 'employee.department']);
        
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        
        $currentYear = date('Y');
        $years = range($currentYear - 2, $currentYear + 1);
        
        $paymentMethods = ['Bank Transfer', 'Cash', 'Check', 'PayPal', 'Other'];
        $statuses = ['pending', 'processed', 'paid'];
        
        return view('admin.payrolls.edit', compact('payroll', 'months', 'years', 'paymentMethods', 'statuses'));
    }

    /**
     * Update the specified payroll in storage.
     */
    public function update(Request $request, Payroll $payroll)
    {
        
        $request->validate([
            'month' => 'required|string',
            'year' => 'required|integer|min:2000|max:2100',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,processed,paid',
            'payment_date' => 'nullable|date',
            'payment_method' => 'nullable|string',
            'payment_reference' => 'nullable|string',
        ]);
        
        // Check if a payroll already exists for this employee, month, and year (excluding the current one)
        $exists = Payroll::where('employee_id', $payroll->employee_id)
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->where('id', '!=', $payroll->id)
            ->exists();
            
        if ($exists) {
            return redirect()->back()->with('error', 'Another payroll record already exists for this employee for the selected month and year.');
        }
        
        // Calculate net salary using PayrollService
        $allowances = $request->allowances ?? 0;
        $deductions = $request->deductions ?? 0;
        $netSalary = $this->payrollService->calculateNetSalary(
            $request->basic_salary,
            $allowances,
            $deductions
        );
        
        // Update the payroll record
        $payroll->update([
            'month' => $request->month,
            'year' => $request->year,
            'basic_salary' => $request->basic_salary,
            'allowances' => $allowances,
            'deductions' => $deductions,
            'net_salary' => $netSalary,
            'notes' => $request->notes,
            'status' => $request->status,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'payment_reference' => $request->payment_reference,
        ]);
        
        return redirect()->route('admin.payrolls.index')
            ->with('success', 'Payroll record updated successfully.');
    }

    /**
     * Generate payroll for all active employees.
     */
    public function generatePayroll(Request $request)
    {
        $request->validate([
            'month' => 'required|string',
            'year' => 'required|integer|min:2000|max:2100',
        ]);
        
        $month = $request->month;
        $year = $request->year;
        
        // Use the PayrollService to generate payrolls for all employees
        $result = $this->payrollService->generatePayrollsForAllEmployees($month, $year);
        
        $message = "Generated {$result['generated']} payroll records. Skipped {$result['skipped']} existing records.";
        
        return redirect()->route('admin.payrolls.index', ['month' => $month, 'year' => $year])
            ->with('success', $message);
    }
}