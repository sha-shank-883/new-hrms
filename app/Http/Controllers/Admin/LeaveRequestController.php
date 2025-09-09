<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['user', 'leaveType']);

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('leave_type_id') && $request->leave_type_id) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('user_id', $request->employee_id);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('start_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('end_date', '<=', $request->end_date);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        $leaveTypes = LeaveType::where('is_active', true)
            ->orderBy('name')
            ->get();

        $employees = User::role('employee')
            ->orderBy('name')
            ->get();

        return view('admin.leave-requests.index', compact('leaveRequests', 'leaveTypes', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $leaveTypes = LeaveType::where('is_active', true)
            ->orderBy('name')
            ->get();

        $employees = User::role('employee')
            ->orderBy('name')
            ->get();

        return view('admin.leave_requests.create', compact('leaveTypes', 'employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|numeric|min:0.5',
            'reason' => 'required|string',
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'required_if:status,rejected',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if the employee has enough leave balance
        $leaveType = LeaveType::findOrFail($request->leave_type_id);
        $leaveBalance = LeaveBalance::where('user_id', $request->user_id)
            ->where('leave_type_id', $request->leave_type_id)
            ->first();

        if (!$leaveBalance) {
            // Create a new leave balance if it doesn't exist
            $leaveBalance = new LeaveBalance();
            $leaveBalance->user_id = $request->user_id;
            $leaveBalance->leave_type_id = $request->leave_type_id;
            $leaveBalance->total_days = $leaveType->days_allowed;
            $leaveBalance->used_days = 0;
            $leaveBalance->remaining_days = $leaveType->days_allowed;
            $leaveBalance->save();
        }

        if ($request->status == 'approved' && $request->days > $leaveBalance->remaining_days) {
            return redirect()->back()
                ->with('error', 'Employee does not have enough leave balance.')
                ->withInput();
        }

        $leaveRequest = new LeaveRequest();
        $leaveRequest->user_id = $request->user_id;
        $leaveRequest->leave_type_id = $request->leave_type_id;
        $leaveRequest->start_date = $request->start_date;
        $leaveRequest->end_date = $request->end_date;
        $leaveRequest->days = $request->days;
        $leaveRequest->reason = $request->reason;
        $leaveRequest->status = $request->status;
        $leaveRequest->rejection_reason = $request->rejection_reason;
        $leaveRequest->save();

        // Update leave balance if approved
        if ($request->status == 'approved') {
            $leaveBalance->used_days += $request->days;
            $leaveBalance->remaining_days -= $request->days;
            $leaveBalance->save();
        }

        return redirect()->route('admin.leave-requests.index')
            ->with('success', 'Leave request created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $leaveRequest = LeaveRequest::with(['user', 'leaveType'])
            ->findOrFail($id);

        $leaveBalances = LeaveBalance::with('leaveType')
            ->where('user_id', $leaveRequest->user_id)
            ->get();

        $recentRequests = LeaveRequest::with('leaveType')
            ->where('user_id', $leaveRequest->user_id)
            ->where('id', '!=', $id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.leave_requests.show', compact('leaveRequest', 'leaveBalances', 'recentRequests'));
    }

    /**
     * Approve the specified leave request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->status != 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending leave requests can be approved.');
        }

        // Check if the employee has enough leave balance
        $leaveBalance = LeaveBalance::where('user_id', $leaveRequest->user_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->first();

        if (!$leaveBalance) {
            // Create a new leave balance if it doesn't exist
            $leaveType = LeaveType::findOrFail($leaveRequest->leave_type_id);
            $leaveBalance = new LeaveBalance();
            $leaveBalance->user_id = $leaveRequest->user_id;
            $leaveBalance->leave_type_id = $leaveRequest->leave_type_id;
            $leaveBalance->total_days = $leaveType->days_allowed;
            $leaveBalance->used_days = 0;
            $leaveBalance->remaining_days = $leaveType->days_allowed;
            $leaveBalance->save();
        }

        if ($leaveRequest->days > $leaveBalance->remaining_days) {
            return redirect()->back()
                ->with('error', 'Employee does not have enough leave balance.');
        }

        $leaveRequest->status = 'approved';
        $leaveRequest->save();

        // Update leave balance
        $leaveBalance->used_days += $leaveRequest->days;
        $leaveBalance->remaining_days -= $leaveRequest->days;
        $leaveBalance->save();

        return redirect()->back()
            ->with('success', 'Leave request approved successfully.');
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
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->status != 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending leave requests can be rejected.');
        }

        $leaveRequest->status = 'rejected';
        $leaveRequest->rejection_reason = $request->rejection_reason;
        $leaveRequest->save();

        return redirect()->back()
            ->with('success', 'Leave request rejected successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        // If the leave request was approved, restore the leave balance
        if ($leaveRequest->status == 'approved') {
            $leaveBalance = LeaveBalance::where('user_id', $leaveRequest->user_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->first();

            if ($leaveBalance) {
                $leaveBalance->used_days -= $leaveRequest->days;
                $leaveBalance->remaining_days += $leaveRequest->days;
                $leaveBalance->save();
            }
        }

        $leaveRequest->delete();

        return redirect()->route('admin.leave-requests.index')
            ->with('success', 'Leave request deleted successfully.');
    }
}
