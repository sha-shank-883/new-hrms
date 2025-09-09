<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
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

        // Get employees from departments managed by the current manager
        $managedDepartmentIds = DB::table('departments')
            ->where('manager_id', Auth::id())
            ->pluck('id');

        $query->whereHas('employee', function ($q) use ($managedDepartmentIds) {
            $q->whereIn('department_id', $managedDepartmentIds);
        });

        $payrolls = $query->latest()->paginate(10);
        $departments = DB::table('departments')
            ->whereIn('id', $managedDepartmentIds)
            ->get();
        
        // Get unique years and months from payrolls for filtering
        $years = Payroll::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
            
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        return view('manager.payrolls.index', compact('payrolls', 'departments', 'years', 'months'));
    }

    /**
     * Display the specified payroll.
     */
    public function show(Payroll $payroll)
    {
        // Get departments managed by the current manager
        $managedDepartmentIds = DB::table('departments')
            ->where('manager_id', Auth::id())
            ->pluck('id');
            
        // Check if the payroll's employee belongs to a department managed by the current manager
        $employeeDepartmentId = $payroll->employee->department_id;
        if (!in_array($employeeDepartmentId, $managedDepartmentIds->toArray())) {
            abort(403, 'Unauthorized action.');
        }
        
        $payroll->load(['employee.user', 'employee.department', 'createdBy']);
        
        return view('manager.payrolls.show', compact('payroll'));
    }
}