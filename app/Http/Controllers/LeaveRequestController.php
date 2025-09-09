<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the leave requests.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        // Different views based on user role
        if ($user->hasRole('super_admin') || $user->hasRole('hr_manager')) {
            // Admin/HR view - see all leave requests
            $leaveRequests = LeaveRequest::with(['employee.user', 'leaveType'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($user->hasRole('department_manager')) {
            // Department manager view - see requests from their department
            $departmentId = $user->employee->department_id;

            $leaveRequests = LeaveRequest::whereHas('employee', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->with(['employee.user', 'leaveType'])
              ->orderBy('created_at', 'desc')
              ->paginate(10);
        } else {
            // Employee view - only see their own requests
            $leaveRequests = LeaveRequest::with(['leaveType'])
                ->whereHas('employee', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        // Determine the view based on user role
        if ($user->hasRole('super_admin') || $user->hasRole('hr_manager') || $user->hasRole('department_manager')) {
            return view('admin.leave-requests.index', compact('leaveRequests'));
        } else {
            return view('employee.leave_requests.index', compact('leaveRequests'));
        }
    }

    /**
     * Show the form for creating a new leave request.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();

        // Get leave types
        $leaveTypes = LeaveType::get();

        // If user is an employee, they can only request leave for themselves
        if (!$user->hasRole('super_admin') && !$user->hasRole('hr_manager')) {
            return view('employee.leave_requests.create', compact('leaveTypes'));
        }

        // For admins/HR, they can create leave requests for any employee
        $employees = Employee::with('user')
            ->get();

        return view('admin.leave_requests.create', compact('leaveTypes', 'employees'));
    }

    /**
     * Store a newly created leave request in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validate the request
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'employee_id' => $user->hasRole('super_admin') || $user->hasRole('hr_manager') ? 'required|exists:employees,id' : '',
        ]);

        // Calculate the number of days
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $days = $startDate->diffInDays($endDate) + 1; // Include both start and end days

        // Get the leave type
        $leaveType = LeaveType::findOrFail($request->leave_type_id);

        // Check if the employee has enough leave days available
        // This would require a more complex leave balance tracking system
        // For now, we'll just check against the leave type's allowed days
        if ($days > $leaveType->days_allowed) {
            return back()->withInput()->with('error', 'The requested leave exceeds the maximum allowed days for this leave type.');
        }

        // Determine the employee ID
        $employeeId = $user->hasRole('super_admin') || $user->hasRole('hr_manager')
            ? $request->employee_id
            : $user->employee->id;

        // Create the leave request
        $leaveRequest = new LeaveRequest();
        $leaveRequest->employee_id = $employeeId;
        $leaveRequest->leave_type_id = $request->leave_type_id;
        $leaveRequest->start_date = $request->start_date;
        $leaveRequest->end_date = $request->end_date;
        $leaveRequest->days = $days;
        $leaveRequest->reason = $request->reason;
        $leaveRequest->status = $leaveType->requires_approval ? 'pending' : 'approved';
        $leaveRequest->save();

        // Redirect based on user role
        if ($user->hasRole('super_admin') || $user->hasRole('hr_manager')) {
            return redirect()->route('admin.leave-requests.index')
                ->with('success', 'Leave request created successfully.');
        } else {
            return redirect()->route('employee.leave_requests.index')
                ->with('success', 'Leave request submitted successfully.');
        }
    }

    /**
     * Display the specified leave request.
     *
     * @param  \App\Models\LeaveRequest  $leaveRequest
     * @return \Illuminate\Http\Response
     */
    public function show(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();

        // Check if the user has permission to view this leave request
        if (!$this->canAccessLeaveRequest($user, $leaveRequest)) {
            return redirect()->back()->with('error', 'You do not have permission to view this leave request.');
        }

        // Load the relationships
        $leaveRequest->load(['employee.user', 'leaveType']);

        // Determine the view based on user role
        if ($user->hasRole('super_admin') || $user->hasRole('hr_manager') || $user->hasRole('department_manager')) {
            return view('admin.leave_requests.show', compact('leaveRequest'));
        } else {
            return view('employee.leave_requests.show', compact('leaveRequest'));
        }
    }

    /**
     * Approve the specified leave request.
     *
     * @param  \App\Models\LeaveRequest  $leaveRequest
     * @return \Illuminate\Http\Response
     */
    public function approve(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();

        // Check if the user has permission to approve this leave request
        if (!$user->hasRole('super_admin') && !$user->hasRole('hr_manager') &&
            !($user->hasRole('department_manager') && $user->employee->department_id === $leaveRequest->employee->department_id)) {
            return redirect()->back()->with('error', 'You do not have permission to approve this leave request.');
        }

        // Check if the leave request is pending
        if ($leaveRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This leave request has already been processed.');
        }

        // Approve the leave request
        $leaveRequest->status = 'approved';
        $leaveRequest->approved_by = $user->id;
        $leaveRequest->approved_at = now();
        $leaveRequest->save();

        // Send notification to the employee
        // This would require setting up a notification system

        return redirect()->back()->with('success', 'Leave request approved successfully.');
    }

    /**
     * Reject the specified leave request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LeaveRequest  $leaveRequest
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $user = Auth::user();

        // Check if the user has permission to reject this leave request
        if (!$user->hasRole('super_admin') && !$user->hasRole('hr_manager') &&
            !($user->hasRole('department_manager') && $user->employee->department_id === $leaveRequest->employee->department_id)) {
            return redirect()->back()->with('error', 'You do not have permission to reject this leave request.');
        }

        // Check if the leave request is pending
        if ($leaveRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'This leave request has already been processed.');
        }

        // Validate the rejection reason
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        // Reject the leave request
        $leaveRequest->status = 'rejected';
        $leaveRequest->rejected_by = $user->id;
        $leaveRequest->rejected_at = now();
        $leaveRequest->rejection_reason = $request->rejection_reason;
        $leaveRequest->save();

        // Send notification to the employee
        // This would require setting up a notification system

        return redirect()->back()->with('success', 'Leave request rejected successfully.');
    }

    /**
     * Cancel the specified leave request.
     *
     * @param  \App\Models\LeaveRequest  $leaveRequest
     * @return \Illuminate\Http\Response
     */
    public function cancel(LeaveRequest $leaveRequest)
    {
        $user = Auth::user();

        // Check if the user has permission to cancel this leave request
        if (!$user->hasRole('super_admin') && !$user->hasRole('hr_manager') &&
            $user->employee->id !== $leaveRequest->employee_id) {
            return redirect()->back()->with('error', 'You do not have permission to cancel this leave request.');
        }

        // Check if the leave request can be cancelled
        if ($leaveRequest->status === 'cancelled') {
            return redirect()->back()->with('error', 'This leave request has already been cancelled.');
        }

        // Check if the leave has already started
        if (Carbon::parse($leaveRequest->start_date)->isPast()) {
            return redirect()->back()->with('error', 'Cannot cancel a leave that has already started or ended.');
        }

        // Cancel the leave request
        $leaveRequest->status = 'cancelled';
        $leaveRequest->cancelled_by = $user->id;
        $leaveRequest->cancelled_at = now();
        $leaveRequest->save();

        return redirect()->back()->with('success', 'Leave request cancelled successfully.');
    }

    /**
     * Check if the user can access the leave request.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LeaveRequest  $leaveRequest
     * @return bool
     */
    private function canAccessLeaveRequest($user, $leaveRequest)
    {
        // Super admin and HR manager can access all leave requests
        if ($user->hasRole('super_admin') || $user->hasRole('hr_manager')) {
            return true;
        }

        // Department manager can access leave requests from their department
        if ($user->hasRole('department_manager')) {
            return $leaveRequest->employee->department_id === $user->employee->department_id;
        }

        // Employee can only access their own leave requests
        return $leaveRequest->employee->user_id === $user->id;
    }
}
