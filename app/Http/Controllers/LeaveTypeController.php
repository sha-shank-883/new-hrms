<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the leave types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Fetch all leave types
        $leaveTypes = LeaveType::orderBy('name', 'asc')
            ->paginate(10);
        
        return view('admin.leave_types.index', compact('leaveTypes'));
    }

    /**
     * Show the form for creating a new leave type.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.leave_types.create');
    }

    /**
     * Store a newly created leave type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'days_allowed' => 'required|integer|min:1',
            'requires_approval' => 'boolean',
            'color' => 'required|string|max:7',
        ]);
        
        // Create the leave type
        $leaveType = new LeaveType();
        $leaveType->name = $request->name;
        $leaveType->slug = Str::slug($request->name);
        $leaveType->description = $request->description;
        $leaveType->days_allowed = $request->days_allowed;
        $leaveType->requires_approval = $request->has('requires_approval');
        $leaveType->color = $request->color;
        $leaveType->save();
        
        return redirect()->route('admin.leave_types.index')
            ->with('success', 'Leave type created successfully.');
    }

    /**
     * Show the form for editing the specified leave type.
     *
     * @param  \App\Models\LeaveType  $leaveType
     * @return \Illuminate\Http\Response
     */
    public function edit(LeaveType $leaveType)
    {
        
        return view('admin.leave_types.edit', compact('leaveType'));
    }

    /**
     * Update the specified leave type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LeaveType  $leaveType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LeaveType $leaveType)
    {
        
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'days_allowed' => 'required|integer|min:1',
            'requires_approval' => 'boolean',
            'color' => 'required|string|max:7',
        ]);
        
        // Update the leave type
        $leaveType->name = $request->name;
        $leaveType->slug = Str::slug($request->name);
        $leaveType->description = $request->description;
        $leaveType->days_allowed = $request->days_allowed;
        $leaveType->requires_approval = $request->has('requires_approval');
        $leaveType->color = $request->color;
        $leaveType->save();
        
        return redirect()->route('admin.leave_types.index')
            ->with('success', 'Leave type updated successfully.');
    }

    /**
     * Remove the specified leave type from storage.
     *
     * @param  \App\Models\LeaveType  $leaveType
     * @return \Illuminate\Http\Response
     */
    public function destroy(LeaveType $leaveType)
    {
        
        // Check if the leave type has any leave requests
        if ($leaveType->leaveRequests()->count() > 0) {
            return redirect()->route('admin.leave_types.index')
                ->with('error', 'Cannot delete leave type because it has associated leave requests.');
        }
        
        // Delete the leave type
        $leaveType->delete();
        
        return redirect()->route('admin.leave_types.index')
            ->with('success', 'Leave type deleted successfully.');
    }
}