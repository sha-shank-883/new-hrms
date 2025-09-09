<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalEmployees' => Employee::count(),
            'totalDepartments' => Department::count(),
            'pendingLeaves' => LeaveRequest::where('status', 'pending')->count(),
            'todayAttendances' => Attendance::where('date', '>=', Carbon::today()->startOfDay())
                ->where('date', '<=', Carbon::today()->endOfDay())
                ->count(),
            'recentLeaves' => LeaveRequest::with(['user', 'leaveType'])
                ->latest()
                ->take(5)
                ->get(),
            'recentAttendances' => Attendance::with('user')
                ->latest()
                ->take(5)
                ->get()
        ];

        return view('admin.dashboard', $data);
    }
}
