<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        $leaveRequests = LeaveRequest::with('leaveType')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $leaveBalances = LeaveBalance::with('leaveType')
            ->where('user_id', $user->id)
            ->get();
        
        $pendingCount = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        
        $approvedCount = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
        
        return view('employee.leave_requests.index', compact(
            'leaveRequests', 
            'leaveBalances', 
            'pendingCount', 
            'approvedCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        
        $leaveTypes = LeaveType::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $leaveBalances = LeaveBalance::with('leaveType')
            ->where('user_id', $user->id)
            ->get();
        
        // Create leave balances for leave types that don't have a balance yet
        foreach ($leaveTypes as $leaveType) {
            $exists = false;
            foreach ($leaveBalances as $balance) {
                if ($balance->leave_type_id == $leaveType->id) {
                    $exists = true;
                    break;
                }
            }
            
            if (!$exists) {
                $leaveBalance = new LeaveBalance();
                $leaveBalance->user_id = $user->id;
                $leaveBalance->leave_type_id = $leaveType->id;
                $leaveBalance->total_days = $leaveType->days_allowed;
                $leaveBalance->used_days = 0;
                $leaveBalance->remaining_days = $leaveType->days_allowed;
                $leaveBalance->save();
                
                $leaveBalances->push($leaveBalance);
            }
        }
        
        return view('employee.leave_requests.create', compact('leaveTypes', 'leaveBalances'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'days' => 'required|numeric|min:0.5',
            'reason' => 'required|string',
            'agree_terms' => 'required|accepted',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Check if the employee has enough leave balance
        $leaveBalance = LeaveBalance::where('user_id', $user->id)
            ->where('leave_type_id', $request->leave_type_id)
            ->first();
        
        if (!$leaveBalance) {
            // Create a new leave balance if it doesn't exist
            $leaveType = LeaveType::findOrFail($request->leave_type_id);
            $leaveBalance = new LeaveBalance();
            $leaveBalance->user_id = $user->id;
            $leaveBalance->leave_type_id = $request->leave_type_id;
            $leaveBalance->total_days = $leaveType->days_allowed;
            $leaveBalance->used_days = 0;
            $leaveBalance->remaining_days = $leaveType->days_allowed;
            $leaveBalance->save();
        }
        
        if ($request->days > $leaveBalance->remaining_days) {
            return redirect()->back()
                ->with('error', 'You do not have enough leave balance.')
                ->withInput();
        }
        
        // Check for overlapping leave requests
        $overlapping = LeaveRequest::where('user_id', $user->id)
            ->where('status', '!=', 'rejected')
            ->where(function($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function($query) use ($request) {
                        $query->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->exists();
        
        if ($overlapping) {
            return redirect()->back()
                ->with('error', 'You already have a leave request for this period.')
                ->withInput();
        }
        
        $leaveRequest = new LeaveRequest();
        $leaveRequest->user_id = $user->id;
        $leaveRequest->leave_type_id = $request->leave_type_id;
        $leaveRequest->start_date = $request->start_date;
        $leaveRequest->end_date = $request->end_date;
        $leaveRequest->days = $request->days;
        $leaveRequest->reason = $request->reason;
        $leaveRequest->status = 'pending';
        $leaveRequest->save();
        
        return redirect()->route('employee.leave_requests.index')
            ->with('success', 'Leave request submitted successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $leaveRequest = LeaveRequest::with('leaveType')
            ->where('user_id', $user->id)
            ->findOrFail($id);
        
        return view('employee.leave_requests.show', compact('leaveRequest'));
    }

    /**
     * Cancel the specified leave request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $user = Auth::user();
        
        $leaveRequest = LeaveRequest::where('user_id', $user->id)
            ->findOrFail($id);
        
        if ($leaveRequest->status != 'pending') {
            return redirect()->back()
                ->with('error', 'Only pending leave requests can be cancelled.');
        }
        
        $leaveRequest->delete();
        
        return redirect()->route('employee.leave_requests.index')
            ->with('success', 'Leave request cancelled successfully.');
    }
}