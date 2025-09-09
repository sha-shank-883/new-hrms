<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Employee::query();
        
        // Apply department filter
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('employment_status', $request->status);
        }
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Get employees with pagination
        $employees = $query->with(['user', 'department'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);
        
        // Get all departments for filter dropdown
        $departments = Department::all();
        
        return view('manager.employees.index', compact('employees', 'departments'));
    }

    /**
     * Display the specified employee.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        // Load relationships
        $employee->load([
            'user',
            'department',
            'attendances' => function($query) {
                $query->orderBy('date', 'desc');
            },
            'leaveRequests' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
        ]);
        
        // Get leave balances
        $leaveBalances = LeaveBalance::where('employee_id', $employee->id)
                                    ->with('leaveType')
                                    ->get();
        
        return view('manager.employees.show', compact('employee', 'leaveBalances'));
    }
}