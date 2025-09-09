<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display the employee's profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->with(['department', 'user'])->firstOrFail();
        
        // Get recent attendance records
        $recentAttendance = $employee->attendances()
                                    ->orderBy('date', 'desc')
                                    ->take(5)
                                    ->get();
        
        // Get recent leave requests
        $recentLeaveRequests = $employee->leaveRequests()
                                      ->with('leaveType')
                                      ->orderBy('created_at', 'desc')
                                      ->take(5)
                                      ->get();
        
        return view('employee.profile.index', compact('employee', 'recentAttendance', 'recentLeaveRequests'));
    }
    
    /**
     * Show the form for editing the employee's profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function editProfile()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->with('user')->firstOrFail();
        
        return view('employee.profile.edit', compact('employee'));
    }
    
    /**
     * Update the employee's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();
        
        // Validate request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:50',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
        ]);
        
        // Update user information
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        // Update employee information
        $employee->phone = $request->phone;
        $employee->address = $request->address;
        $employee->emergency_contact = $request->emergency_contact;
        $employee->emergency_contact_relationship = $request->emergency_contact_relationship;
        $employee->marital_status = $request->marital_status;
        
        $employee->save();
        
        return redirect()->route('employee.profile.index')
                         ->with('success', 'Profile updated successfully.');
    }
}