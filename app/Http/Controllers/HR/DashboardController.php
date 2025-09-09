<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the HR dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats = [
            'total_employees' => Employee::count(),
            'on_leave' => $this->getEmployeesOnLeaveCount(),
            'pending_leave_requests' => LeaveRequest::where('status', 'pending')->count(),
            'attendance_issues' => $this->getAttendanceIssuesCount(),
        ];

        // Get recent leave requests with eager loading
        $recentLeaveRequests = LeaveRequest::with(['employee.user', 'leaveType'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($request) {
                // Ensure the required relationships are loaded
                if (!$request->relationLoaded('employee')) {
                    $request->load('employee');
                }
                if ($request->employee && !$request->employee->relationLoaded('user')) {
                    $request->employee->load('user');
                }
                if (!$request->relationLoaded('leaveType')) {
                    $request->load('leaveType');
                }
                return $request;
            });

        // Get upcoming holidays
        $upcomingHolidays = Holiday::where('date', '>=', now())
            ->orderBy('date')
            ->take(5)
            ->get();

        // Ensure all required data is available in the view
        $viewData = [
            'stats' => (object) $stats, // Convert array to object for better IDE support
            'recentLeaveRequests' => $recentLeaveRequests,
            'upcomingHolidays' => $upcomingHolidays
        ];

        // Debug the data being passed to the view
        \Log::debug('HR Dashboard Data:', $viewData);

        return view('hr.dashboard', $viewData);
    }

    /**
     * Get the count of employees on leave today.
     *
     * @return int
     */
    protected function getEmployeesOnLeaveCount()
    {
        $today = now()->toDateString();
        
        return LeaveRequest::where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->count();
    }

    /**
     * Get the count of attendance issues.
     *
     * @return int
     */
    protected function getAttendanceIssuesCount()
    {
        // Count employees who haven't checked in today
        $today = now()->toDateString();
        $totalEmployees = Employee::count();
        $presentCount = DB::table('attendances')
            ->whereDate('date', $today)
            ->distinct('employee_id')
            ->count('employee_id');
            
        return $totalEmployees - $presentCount;
    }
}
