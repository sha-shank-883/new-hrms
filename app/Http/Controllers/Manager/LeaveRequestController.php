<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\LeaveBalance;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get the department managed by this manager
        $department = Department::where('manager_id', $user->id)
            ->first();

        if (!$department) {
            return redirect()->back()
                ->with('error', 'You are not assigned as a manager to any department.');
        }

        // Get employees in this department
        $departmentEmployees = User::where('department_id', $department->id)
            ->where('role_id', 3) // Employee role
            ->pluck('id')
            ->toArray();

        $query = LeaveRequest::with(['employee.user', 'leaveType'])
            ->whereHas('employee', function($q) use ($departmentEmployees) {
                $q->whereIn('user_id', $departmentEmployees);
            });

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('leave_type_id') && $request->leave_type_id) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->has('employee_id') && $request->employee_id) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('user_id', $request->employee_id);
            });
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        $leaveTypes = LeaveType::where('is_active', true)
            ->orderBy('name')
            ->get();

        $employees = User::where('department_id', $department->id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'employee');
            })
            ->orderBy('name')
            ->get();

        // Get counts for summary
        $totalRequests = LeaveRequest::whereHas('employee', function($q) use ($departmentEmployees) {
                $q->whereIn('user_id', $departmentEmployees);
            })
            ->count();

        $pendingRequests = LeaveRequest::whereHas('employee', function($q) use ($departmentEmployees) {
                $q->whereIn('user_id', $departmentEmployees);
            })
            ->where('status', 'pending')
            ->count();

        $approvedRequests = LeaveRequest::whereHas('employee', function($q) use ($departmentEmployees) {
                $q->whereIn('user_id', $departmentEmployees);
            })
            ->where('status', 'approved')
            ->count();

        $rejectedRequests = LeaveRequest::whereHas('employee', function($q) use ($departmentEmployees) {
                $q->whereIn('user_id', $departmentEmployees);
            })
            ->where('status', 'rejected')
            ->count();

        return view('manager.leave_requests.index', compact(
            'leaveRequests',
            'leaveTypes',
            'employees',
            'totalRequests',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'department'
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = Auth::user();

        // Get the department managed by this manager
        $department = Department::where('manager_id', $user->id)
            ->first();

        if (!$department) {
            return redirect()->back()
                ->with('error', 'You are not assigned as a manager to any department.');
        }

        // Get employees in this department
        $departmentEmployees = User::where('department_id', $department->id)
            ->where('role_id', 3) // Employee role
            ->pluck('id')
            ->toArray();

        $leaveRequest = LeaveRequest::with(['employee.user', 'leaveType'])
            ->whereHas('employee', function($q) use ($departmentEmployees) {
                $q->whereIn('user_id', $departmentEmployees);
            })
            ->findOrFail($id);

        $leaveBalances = LeaveBalance::where('user_id', $leaveRequest->employee->user_id)
            ->with('leaveType')
            ->get();

        $recentRequests = LeaveRequest::with(['leaveType', 'employee.user'])
            ->whereHas('employee', function($q) use ($leaveRequest) {
                $q->where('user_id', $leaveRequest->employee->user_id);
            })
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('manager.leave_requests.show', compact(
            'leaveRequest',
            'leaveBalances',
            'recentRequests'
        ));
    }

    /**
     * Approve the specified leave request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        $user = Auth::user();

        // Get the department managed by this manager
        $department = Department::where('manager_id', $user->id)
            ->first();

        if (!$department) {
            return redirect()->back()
                ->with('error', 'You are not assigned as a manager to any department.');
        }

        // Get employees in this department
        $departmentEmployees = User::where('department_id', $department->id)
            ->where('role_id', 3)
            ->pluck('id')
            ->toArray();

        $leaveRequest = LeaveRequest::whereHas('employee', function($q) use ($departmentEmployees) {
                $q->whereIn('user_id', $departmentEmployees);
            })
            ->findOrFail($id);

        if ($leaveRequest->status != 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending leave requests can be approved.');
        }

        $leaveRequest->status = 'approved';
        $leaveRequest->approved_by = $user->id;
        $leaveRequest->approved_at = now();
        $leaveRequest->save();

        // Notify the employee about the approval
        $employee = $leaveRequest->employee->user;

        return redirect()->route('manager.leave-requests.index')
            ->with('success', 'Leave request has been approved successfully.');
    }

    /**
     * Reject the specified leave request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        // Get the department managed by this manager
        $department = Department::where('manager_id', $user->id)
            ->first();

        if (!$department) {
            return redirect()->back()
                ->with('error', 'You are not assigned as a manager to any department.');
        }

        // Get employees in this department
        $departmentEmployees = User::where('department_id', $department->id)
            ->where('role_id', 3) // Employee role
            ->pluck('id')
            ->toArray();

        $leaveRequest = LeaveRequest::whereHas('employee', function($q) use ($departmentEmployees) {
                $q->whereIn('user_id', $departmentEmployees);
            })
            ->findOrFail($id);

        if ($leaveRequest->status != 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending leave requests can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $leaveRequest->status = 'rejected';
        $leaveRequest->rejection_reason = $request->rejection_reason;
        $leaveRequest->approved_by = $user->id;
        $leaveRequest->approved_at = now();
        $leaveRequest->save();

        // Notify the employee about the rejection
        $employee = $leaveRequest->employee->user;

        return redirect()->route('manager.leave-requests.index')
            ->with('success', 'Leave request has been rejected.');
    }
}
