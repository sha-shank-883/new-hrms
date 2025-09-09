<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $leaveTypes = LeaveType::orderBy('name')
            ->paginate(10);

        return view('admin.leave_types.index', compact('leaveTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.leave_types.create');
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'days_allowed' => 'required|numeric|min:0',
            'color' => 'required|string|max:7',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $leaveType = new LeaveType();
        $leaveType->name = $request->name;
        $leaveType->description = $request->description;
        $leaveType->days_allowed = $request->days_allowed;
        $leaveType->color = $request->color;
        $leaveType->requires_approval = $request->has('requires_approval');
        $leaveType->is_active = $request->has('is_active');
        $leaveType->save();

        return redirect()->route('admin.leave-types.index')
            ->with('success', 'Leave type created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $leaveType = LeaveType::findOrFail($id);

        return view('admin.leave_types.edit', compact('leaveType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'days_allowed' => 'required|numeric|min:0',
            'color' => 'required|string|max:7',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $leaveType = LeaveType::findOrFail($id);

        $leaveType->name = $request->name;
        $leaveType->description = $request->description;
        $leaveType->days_allowed = $request->days_allowed;
        $leaveType->color = $request->color;
        $leaveType->requires_approval = $request->has('requires_approval');
        $leaveType->is_active = $request->has('is_active');
        $leaveType->save();

        return redirect()->route('admin.leave-types.index')
            ->with('success', 'Leave type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $leaveType = LeaveType::findOrFail($id);

        // Check if the leave type is being used in leave requests
        if ($leaveType->leaveRequests()->count() > 0) {
            return redirect()->route('admin.leave-types.index')
                ->with('error', 'Cannot delete leave type as it is being used in leave requests.');
        }

        $leaveType->delete();

        return redirect()->route('admin.leave-types.index')
            ->with('success', 'Leave type deleted successfully.');
    }
}