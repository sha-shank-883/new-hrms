<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
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
        
        $query = Attendance::with('user')
            ->whereIn('user_id', $departmentEmployees);
        
        // Apply filters
        if ($request->has('employee_id') && $request->employee_id) {
            $query->where('user_id', $request->employee_id);
        }
        
        if ($request->has('date') && $request->date) {
            $query->whereDate('date', $request->date);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $attendances = $query->orderBy('date', 'desc')
            ->paginate(10);
        
        $employees = User::where('department_id', $department->id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'employee');
            })
            ->orderBy('name')
            ->get();
        
        // Get today's summary
        $today = Carbon::today()->format('Y-m-d');
        $todayPresent = Attendance::whereIn('user_id', $departmentEmployees)
            ->whereDate('date', $today)
            ->where('status', 'present')
            ->count();
            
        $todayLate = Attendance::whereIn('user_id', $departmentEmployees)
            ->whereDate('date', $today)
            ->where('status', 'late')
            ->count();
            
        $todayAbsent = Attendance::whereIn('user_id', $departmentEmployees)
            ->whereDate('date', $today)
            ->where('status', 'absent')
            ->count();
        
        $totalEmployees = count($departmentEmployees);
        
        return view('manager.attendances.index', compact(
            'attendances', 
            'employees', 
            'department',
            'todayPresent',
            'todayLate',
            'todayAbsent',
            'totalEmployees'
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
        
        $attendance = Attendance::with('user')
            ->whereIn('user_id', $departmentEmployees)
            ->findOrFail($id);
        
        // Get monthly summary for this employee
        $month = Carbon::parse($attendance->date)->format('m');
        $year = Carbon::parse($attendance->date)->format('Y');
        $monthlySummary = $this->getMonthlyAttendanceSummary($attendance->user_id, $month, $year);
        
        // Get recent attendances for this employee
        $recentAttendances = Attendance::where('user_id', $attendance->user_id)
            ->where('id', '!=', $id)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();
        
        return view('manager.attendances.show', compact('attendance', 'monthlySummary', 'recentAttendances'));
    }

    /**
     * Display the attendance report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function report(Request $request)
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
        $employees = User::where('department_id', $department->id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'employee');
            })
            ->orderBy('name')
            ->get();
        
        $month = $request->month ?? Carbon::now()->format('m');
        $year = $request->year ?? Carbon::now()->format('Y');
        $employeeId = $request->employee_id;
        
        // Initialize summary data
        $summaryData = [];
        $dailyTrend = [];
        $calendarData = [];
        
        // Get all days in the selected month
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
            $dailyTrend[$date] = [
                'present' => 0,
                'late' => 0,
                'half_day' => 0,
                'absent' => 0,
            ];
        }
        
        // If specific employee is selected, only include that employee
        $filteredEmployees = $employees;
        if ($employeeId) {
            $filteredEmployees = $employees->filter(function ($employee) use ($employeeId) {
                return $employee->id == $employeeId;
            });
        }
        
        // Get attendance data for all filtered employees
        foreach ($filteredEmployees as $employee) {
            $monthlySummary = $this->getMonthlyAttendanceSummary($employee->id, $month, $year);
            
            $summaryData[] = [
                'employee' => $employee,
                'summary' => $monthlySummary,
            ];
            
            // Update daily trend data
            $attendances = Attendance::where('user_id', $employee->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->groupBy(function($date) {
                    return \Carbon\Carbon::parse($date->date)->format('d');
                });
            
            foreach ($attendances as $date => $attendance) {
                if (isset($dailyTrend[$date])) {
                    $dailyTrend[$date][$attendance[0]->status]++;
                }
                
                // Add to calendar data if specific employee is selected
                if ($employeeId) {
                    $calendarData[] = [
                        'date' => $date,
                        'status' => $attendance->status,
                        'check_in' => $attendance->check_in,
                        'check_out' => $attendance->check_out,
                        'working_hours' => $attendance->working_hours,
                        'is_late' => $attendance->is_late,
                    ];
                }
            }
        }
        
        // Calculate overall summary
        $overallSummary = [
            'present' => 0,
            'late' => 0,
            'half_day' => 0,
            'absent' => 0,
        ];
        
        foreach ($summaryData as $data) {
            $overallSummary['present'] += $data['summary']['present_count'];
            $overallSummary['late'] += $data['summary']['late_count'];
            $overallSummary['half_day'] += $data['summary']['half_day_count'];
            $overallSummary['absent'] += $data['summary']['absent_count'];
        }
        
        return view('manager.attendances.report', compact(
            'employees',
            'department',
            'month',
            'year',
            'employeeId',
            'summaryData',
            'overallSummary',
            'dailyTrend',
            'calendarData'
        ));
    }

    /**
     * Get monthly attendance summary for an employee.
     *
     * @param  int  $userId
     * @param  string  $month
     * @param  string  $year
     * @return array
     */
    private function getMonthlyAttendanceSummary($userId, $month, $year)
    {
        $attendances = Attendance::where('user_id', $userId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $presentCount = $attendances->where('status', 'present')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $halfDayCount = $attendances->where('status', 'half_day')->count();
        $absentCount = $attendances->where('status', 'absent')->count();

        $totalWorkingHours = $attendances->sum('working_hours');

        // Calculate working days in the month (excluding weekends)
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        $workingDays = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            if (!$date->isWeekend()) {
                $workingDays++;
            }
        }

        // Calculate attendance percentage
        $attendedDays = $presentCount + $lateCount + $halfDayCount;
        $attendancePercentage = $workingDays > 0 ? ($attendedDays / $workingDays) * 100 : 0;

        return [
            'present_count' => $presentCount,
            'late_count' => $lateCount,
            'half_day_count' => $halfDayCount,
            'absent_count' => $absentCount,
            'total_working_hours' => $totalWorkingHours,
            'working_days' => $workingDays,
            'attendance_percentage' => $attendancePercentage,
        ];
    }
}