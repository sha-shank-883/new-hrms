<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Employee::with(['user', 'department']);
            
        // Filter by department
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('employment_status', $request->status);
        }
        
        // Search by name or employee ID
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('employee_id', 'like', "%{$search}%");
        }
        
        $employees = $query->paginate(10);
        $departments = Department::all();
        
        return view('admin.employees.index', compact('employees', 'departments'));
    }

    /**
     * Show the form for creating a new employee.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $departments = Department::all();
        return view('admin.employees.create', compact('departments'));
    }

    /**
     * Store a newly created employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'joining_date' => 'required|date',
            'employment_status' => 'required|in:active,on_leave,terminated',
            'salary' => 'required|numeric',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'marital_status' => 'required|in:single,married,divorced,widowed',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            
            // Assign employee role
            $user->assignRole('employee');
            
            // Generate employee ID
            $employeeId = 'EMP' . str_pad(Employee::count() + 1, 4, '0', STR_PAD_LEFT);
            
            // Create employee
            $employee = new Employee([
                'employee_id' => $employeeId,
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'position' => $request->position,
                'joining_date' => $request->joining_date,
                'employment_status' => $request->employment_status,
                'salary' => $request->salary,
                'phone' => $request->phone,
                'emergency_contact' => $request->emergency_contact,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'marital_status' => $request->marital_status,
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified employee.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\View\View
     */
    public function show(Employee $employee)
    {
        $employee = Employee::with(['user', 'department', 'attendances', 'leaveRequests', 'payrolls'])
            ->findOrFail($employee->id);
            
        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\View\View
     */
    public function edit(Employee $employee)
    {
        $employee->load('user');
        $departments = Department::all();
        
        return view('admin.employees.edit', compact('employee', 'departments'));
    }

    /**
     * Update the specified employee in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->user_id,
            'department_id' => 'required|exists:departments,id',
            'position' => 'required|string|max:255',
            'joining_date' => 'required|date',
            'employment_status' => 'required|in:active,on_leave,terminated',
            'salary' => 'required|numeric',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'marital_status' => 'required|in:single,married,divorced,widowed',
        ]);
        
        if ($request->has('password') && $request->password) {
            $request->validate([
                'password' => 'string|min:8',
            ]);
        }
        
        DB::beginTransaction();
        
        try {
            // Update user
            $user = User::find($employee->user_id);
            $user->name = $request->name;
            $user->email = $request->email;
            
            if ($request->has('password') && $request->password) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            
            // Update employee
            $employee->update([
                'department_id' => $request->department_id,
                'position' => $request->position,
                'joining_date' => $request->joining_date,
                'employment_status' => $request->employment_status,
                'termination_date' => $request->employment_status === 'terminated' ? $request->termination_date : null,
                'salary' => $request->salary,
                'phone' => $request->phone,
                'emergency_contact' => $request->emergency_contact,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'marital_status' => $request->marital_status,
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating employee: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified employee from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        DB::beginTransaction();
        
        try {
            // Delete employee
            $employee->delete();
            
            // Delete user
            User::destroy($employee->user_id);
            
            DB::commit();
            
            return redirect()->route('admin.employees.index')
                ->with('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting employee: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the employee profile.
     *
     * @return \Illuminate\View\View
     */
    public function profile()
    {
        $user = Auth::user();
        $employee = Employee::with('department')
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        // Get recent attendances
        $recentAttendances = Attendance::where('employee_id', $employee->id)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();
            
        // Get recent leave requests
        $recentLeaveRequests = LeaveRequest::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        return view('employee.profile', compact('employee', 'recentAttendances', 'recentLeaveRequests'));
    }
    
    /**
     * Update the employee profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:50',
            'address' => 'required|string',
            'marital_status' => 'required|in:single,married,divorced,widowed',
        ]);
        
        if ($request->has('password') && $request->password) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
        }
        
        DB::beginTransaction();
        
        try {
            // Update user
            $user->name = $request->name;
            $user->email = $request->email;
            
            if ($request->has('password') && $request->password) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            
            // Update employee
            $employee->update([
                'phone' => $request->phone,
                'emergency_contact' => $request->emergency_contact,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
                'address' => $request->address,
                'marital_status' => $request->marital_status,
            ]);
            
            DB::commit();
            
            return redirect()->route('employee.profile')
                ->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating profile: ' . $e->getMessage())
                ->withInput();
        }
    }
}