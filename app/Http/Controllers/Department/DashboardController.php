<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the department dashboard.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        
        // Get all departments for the dropdown
        $departments = \App\Models\Department::orderBy('name')->get();
        
        // Get selected department from request or use user's department
        $selectedDepartmentId = request('department_id', $user->department_id);
        
        if (!$selectedDepartmentId && !$user->hasRole('admin|super_admin')) {
            if ($departments->isEmpty()) {
                return redirect()->back()->with('error', 'No departments found.');
            }
            $selectedDepartmentId = $departments->first()->id;
        }
        
        $departmentId = $selectedDepartmentId;
        
        // If no department is selected and user is admin, show all departments
        if (!$selectedDepartmentId && $user->hasRole('admin|super_admin')) {
            // Get first department for the view
            $selectedDepartmentId = $departments->first()?->id;
        }
        
        // Get department stats
        $stats = [
            'team_members' => $this->getTeamMembersCount($departmentId),
            'on_leave' => $this->getTeamOnLeaveCount($departmentId),
            'pending_approvals' => $this->getPendingApprovalsCount($departmentId),
            'attendance_issues' => $this->getTeamAttendanceIssues($departmentId),
        ];

        // Get team leave requests
        $teamLeaveRequests = LeaveRequest::whereHas('user', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->with(['user', 'leaveType'])
            ->whereIn('status', ['pending', 'approved'])
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->latest()
            ->take(5)
            ->get();

        // Get today's attendance
        $today = now()->toDateString();
        $teamAttendance = Attendance::with('user')
            ->whereDate('date', $today)
            ->whereHas('user', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->orderBy('check_in', 'desc')
            ->take(5)
            ->get();

        // Get selected department
        $department = \App\Models\Department::find($selectedDepartmentId);
        
        if (!$department) {
            return redirect()->back()->with('error', 'Selected department not found.');
        }
        
        // Get recent activities
        $recentActivities = \App\Models\Activity::where('department_id', $departmentId)
            ->latest()
            ->take(5)
            ->get();

        // Ensure all required data is available
        $viewData = [
            'stats' => $stats,
            'teamLeaveRequests' => $teamLeaveRequests ?? collect(),
            'teamAttendance' => $teamAttendance,
            'department' => $department,
            'departments' => $departments,
            'selectedDepartmentId' => $selectedDepartmentId,
            'recentActivities' => $recentActivities,
            'today' => now()->format('l, F j, Y')
        ];

        // Debug: Uncomment to check the data being passed to the view
        // dd($viewData);

        return view('department.dashboard', $viewData);
    }

    /**
     * Get the count of team members in the department.
     *
     * @param int $departmentId
     * @return int
     */
    protected function getTeamMembersCount($departmentId)
    {
        return Employee::where('department_id', $departmentId)->count();
    }

    /**
     * Get the count of team members on leave today.
     *
     * @param int $departmentId
     * @return int
     */
    protected function getTeamOnLeaveCount($departmentId)
    {
        $today = now()->toDateString();
        
        return LeaveRequest::where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereHas('user', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->count();
    }

    /**
     * Get the count of pending leave approvals.
     *
     * @param int $departmentId
     * @return int
     */
    protected function getPendingApprovalsCount($departmentId)
    {
        return LeaveRequest::where('status', 'pending')
            ->whereHas('user', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->count();
    }

    /**
     * Get the count of attendance issues in the team.
     *
     * @param int $departmentId
     * @return int
     */
    protected function getTeamAttendanceIssues($departmentId)
    {
        $today = now()->toDateString();
        $teamMembers = Employee::where('department_id', $departmentId)->pluck('id');
        
        $presentCount = Attendance::whereIn('employee_id', $teamMembers)
            ->whereDate('date', $today)
            ->distinct('employee_id')
            ->count('employee_id');
            
        return $teamMembers->count() - $presentCount;
    }
}
