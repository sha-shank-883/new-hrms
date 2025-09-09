<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Attendance::with('user');

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

        $users = User::whereHas('roles', function($q) {
                $q->where('name', 'employee');
            })
            ->orderBy('name')
            ->get();

        return view('admin.attendances.index', compact('attendances', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $employees = User::whereHas('roles', function($q) {
                $q->where('name', 'employee');
            })
            ->orderBy('name')
            ->get();

        return view('admin.attendances.create', compact('employees'));
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
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,half_day',
            'check_in' => 'nullable|required_if:status,present,late,half_day|date_format:H:i',
            'check_out' => 'nullable|required_if:status,present,late,half_day|date_format:H:i|after:check_in',
            'is_late' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if attendance record already exists for this employee and date
        $existingAttendance = Attendance::where('user_id', $request->user_id)
            ->where('date', '>=', Carbon::parse($request->date)->startOfDay())
            ->where('date', '<=', Carbon::parse($request->date)->endOfDay())
            ->first();

        if ($existingAttendance) {
            return redirect()->back()
                ->with('error', 'Attendance record already exists for this employee on this date.')
                ->withInput();
        }

        $attendance = new Attendance();
        $attendance->user_id = $request->user_id;
        $attendance->date = $request->date;
        $attendance->status = $request->status;
        
        if (in_array($request->status, ['present', 'late', 'half_day'])) {
            $attendance->check_in = $request->check_in;
            $attendance->check_out = $request->check_out;
            
            // Calculate working hours
            if ($request->check_in && $request->check_out) {
                $checkIn = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->check_in);
                $checkOut = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->check_out);
                $attendance->working_hours = $checkOut->diffInHours($checkIn) + ($checkOut->diffInMinutes($checkIn) % 60) / 60;
            }
            
            $attendance->is_late = $request->has('is_late') ? $request->is_late : false;
        }
        
        $attendance->notes = $request->notes;
        $attendance->save();

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Attendance record created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\View\View
     */
    public function show(Attendance $attendance)
    {
        // Get monthly summary for this employee
        $month = Carbon::parse($attendance->date)->format('m');
        $year = Carbon::parse($attendance->date)->format('Y');
        
        $monthlySummary = $this->getMonthlyAttendanceSummary($attendance->user_id, $month, $year);
        
        // Get recent attendances for this employee
        $recentAttendances = Attendance::where('user_id', $attendance->user_id)
            ->where('id', '!=', $attendance->id)
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        return view('admin.attendances.show', compact('attendance', 'monthlySummary', 'recentAttendances'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\View\View
     */
    public function edit(Attendance $attendance)
    {
        $employees = User::whereHas('roles', function($q) {
                $q->where('name', 'employee');
            })
            ->orderBy('name')
            ->get();

        return view('admin.attendances.edit', compact('attendance', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,half_day',
            'check_in' => 'nullable|required_if:status,present,late,half_day|date_format:H:i',
            'check_out' => 'nullable|required_if:status,present,late,half_day|date_format:H:i|after:check_in',
            'is_late' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if attendance record already exists for this employee and date (excluding this record)
        $existingAttendance = Attendance::where('user_id', $request->user_id)
            ->where('date', '>=', Carbon::parse($request->date)->startOfDay())
            ->where('date', '<=', Carbon::parse($request->date)->endOfDay())
            ->where('id', '!=', $attendance->id)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()
                ->with('error', 'Attendance record already exists for this employee on this date.')
                ->withInput();
        }

        $attendance->user_id = $request->user_id;
        $attendance->date = $request->date;
        $attendance->status = $request->status;
        
        if (in_array($request->status, ['present', 'late', 'half_day'])) {
            $attendance->check_in = $request->check_in;
            $attendance->check_out = $request->check_out;
            
            // Calculate working hours
            if ($request->check_in && $request->check_out) {
                $checkIn = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->check_in);
                $checkOut = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->check_out);
                $attendance->working_hours = $checkOut->diffInHours($checkIn) + ($checkOut->diffInMinutes($checkIn) % 60) / 60;
            }
            
            $attendance->is_late = $request->has('is_late') ? $request->is_late : false;
        } else {
            $attendance->check_in = null;
            $attendance->check_out = null;
            $attendance->working_hours = 0;
            $attendance->is_late = false;
        }
        
        $attendance->notes = $request->notes;
        $attendance->save();

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Attendance record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Attendance  $attendance
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Attendance record deleted successfully.');
    }

    /**
     * Display the attendance report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function report(Request $request)
    {
        $users = User::whereHas('roles', function($q) {
                $q->where('name', 'employee');
            })
            ->orderBy('name')
            ->get();

        $departments = Department::orderBy('name')
            ->get();

        $month = $request->month ?? Carbon::now()->format('m');
        $year = $request->year ?? Carbon::now()->format('Y');
        $employeeId = $request->employee_id;
        $departmentId = $request->department_id;

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

        // Filter employees by department if specified
        $filteredEmployees = $users;
        if ($departmentId) {
            $filteredEmployees = $users->filter(function ($user) use ($departmentId) {
                return $user->department_id == $departmentId;
            });
        }

        // If specific employee is selected, only include that employee
        if ($employeeId) {
            $filteredEmployees = $users->filter(function ($user) use ($employeeId) {
                return $user->id == $employeeId;
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
                ->get();

            foreach ($attendances as $attendance) {
                $date = Carbon::parse($attendance->date)->format('Y-m-d');
                if (isset($dailyTrend[$date])) {
                    $dailyTrend[$date][$attendance->status]++;
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
        
        // Calculate summary percentages for the view
        $total = array_sum($overallSummary);
        $summary = [
            'presentCount' => $overallSummary['present'],
            'lateCount' => $overallSummary['late'],
            'halfDayCount' => $overallSummary['half_day'],
            'absentCount' => $overallSummary['absent'],
            'totalDays' => $total,
            'presentPercentage' => $total > 0 ? ($overallSummary['present'] / $total) * 100 : 0,
            'latePercentage' => $total > 0 ? ($overallSummary['late'] / $total) * 100 : 0,
            'halfDayPercentage' => $total > 0 ? ($overallSummary['half_day'] / $total) * 100 : 0,
            'absentPercentage' => $total > 0 ? ($overallSummary['absent'] / $total) * 100 : 0,
        ];
        
        // Prepare employee stats for the detailed report
        $employeeStats = [];
        foreach ($filteredEmployees as $employee) {
            $monthlySummary = $this->getMonthlyAttendanceSummary($employee->id, $month, $year);
            $attendedDays = $monthlySummary['present_count'] + $monthlySummary['late_count'] + $monthlySummary['half_day_count'];
            $attendancePercentage = $monthlySummary['working_days'] > 0 ? 
                round(($attendedDays / $monthlySummary['working_days']) * 100, 1) : 0;
                
            $employeeStats[] = [
                'id' => $employee->id,
                'name' => $employee->name,
                'department' => $employee->department ? $employee->department->name : 'N/A',
                'presentCount' => $monthlySummary['present_count'],
                'lateCount' => $monthlySummary['late_count'],
                'halfDayCount' => $monthlySummary['half_day_count'],
                'absentCount' => $monthlySummary['absent_count'],
                'workingHours' => $monthlySummary['total_working_hours'],
                'attendancePercentage' => $attendancePercentage
            ];
        }
        
        // Prepare chart data for attendance trend
        $chartData = [
            'dates' => [],
            'present' => [],
            'late' => [],
            'absent' => []
        ];
        
        // Get the number of days in the selected month
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
        
        // Initialize daily counts
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::createFromDate($year, $month, $day);
            $dateString = $date->format('Y-m-d');
            
            $chartData['dates'][] = $date->format('M d');
            $chartData['present'][$day - 1] = 0;
            $chartData['late'][$day - 1] = 0;
            $chartData['absent'][$day - 1] = 0;
            
            // Count attendance for each status on this date
            foreach ($filteredEmployees as $employee) {
                $attendance = Attendance::where('user_id', $employee->id)
                    ->where('date', '>=', $dateString . ' 00:00:00')
                    ->where('date', '<=', $dateString . ' 23:59:59')
                    ->first();
                    
                if ($attendance) {
                    switch ($attendance->status) {
                        case 'present':
                            $chartData['present'][$day - 1]++;
                            break;
                        case 'late':
                            $chartData['late'][$day - 1]++;
                            break;
                        case 'absent':
                            $chartData['absent'][$day - 1]++;
                            break;
                    }
                } else if ($date->isWeekday()) {
                    // Only count weekdays for absent
                    $chartData['absent'][$day - 1]++;
                }
            }
        }

        return view('admin.attendances.report', compact(
            'users',
            'departments',
            'month',
            'year',
            'employeeId',
            'departmentId',
            'summaryData',
            'summary',
            'employeeStats',
            'overallSummary',
            'dailyTrend',
            'calendarData',
            'chartData'
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
    /**
     * Export attendance report to Excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        // Get parameters from request
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $employeeId = $request->user_id;
        $departmentId = $request->department_id;

        // Get filtered users
        $query = User::whereHas('roles', function($q) {
                $q->where('name', 'employee');
            })
            ->with('department')
            ->orderBy('name');

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        $users = $query->get();

        // Generate CSV content
        $headers = [
            'Employee Name',
            'Department',
            'Present',
            'Late',
            'Half Day',
            'Absent',
            'Working Hours',
            'Attendance %'
        ];

        $rows = [];
        foreach ($users as $user) {
            $summary = $this->getMonthlyAttendanceSummary($user->id, $month, $year);
            $attendedDays = $summary['present_count'] + $summary['late_count'] + $summary['half_day_count'];
            $attendancePercentage = $summary['working_days'] > 0 ? 
                round(($attendedDays / $summary['working_days']) * 100, 2) : 0;

            $rows[] = [
                $user->name,
                $user->department ? $user->department->name : 'N/A',
                $summary['present_count'],
                $summary['late_count'],
                $summary['half_day_count'],
                $summary['absent_count'],
                number_format($summary['total_working_hours'], 1),
                $attendancePercentage . '%'
            ];
        }

        // Generate CSV file
        $filename = 'attendance_report_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // Add headers
        fputcsv($handle, $headers);
        
        // Add rows
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        // Return download response
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
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