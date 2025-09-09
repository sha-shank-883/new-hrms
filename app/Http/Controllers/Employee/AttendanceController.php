<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    /**
     * Show the check-in form.
     *
     * @return \Illuminate\View\View
     */
    public function showCheckInForm()
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');
        
        // Get today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($todayAttendance && $todayAttendance->check_in) {
            return redirect()->route('employee.attendances.index')
                ->with('warning', 'You have already checked in today.');
        }

        // Get recent attendance records (last 5)
        $recentAttendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        return view('employee.attendances.check-in', [
            'todayAttendance' => $todayAttendance,
            'recentAttendances' => $recentAttendances
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get today's attendance
        $today = Carbon::today()->format('Y-m-d');
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();
        
        // Get monthly summary
        $month = $request->month ?? Carbon::now()->format('m');
        $year = $request->year ?? Carbon::now()->format('Y');
        $monthlySummary = $this->getMonthlyAttendanceSummary($user->id, $month, $year);
        
        // Get recent attendances
        $query = Attendance::where('user_id', $user->id);
            
        if ($request->has('month') && $request->month) {
            $query->whereMonth('date', $request->month);
        }
        
        if ($request->has('year') && $request->year) {
            $query->whereYear('date', $request->year);
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $attendances = $query->orderBy('date', 'desc')
            ->paginate(10);
            
        // Calculate attendance statistics
        $totalDays = $attendances->total();
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays = $attendances->where('status', 'absent')->count();
        $lateDays = $attendances->where('is_late', true)->count();
        $halfDayCount = $attendances->where('status', 'half_day')->count();
        
        // Calculate percentages
        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;
        $latePercentage = $totalDays > 0 ? round(($lateDays / $totalDays) * 100) : 0;
        $absentPercentage = $totalDays > 0 ? round(($absentDays / $totalDays) * 100) : 0;
        $halfDayPercentage = $totalDays > 0 ? round(($halfDayCount / $totalDays) * 100) : 0;
        
        // Calculate working hours (assuming 8 hours per working day)
        $workingHours = ($presentDays * 8) + ($halfDayCount * 4);
        
        $stats = [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'half_day_count' => $halfDayCount,
            'working_hours' => $workingHours,
            'attendance_percentage' => $attendancePercentage,
            'late_percentage' => $latePercentage,
            'absent_percentage' => $absentPercentage,
            'half_day_percentage' => $halfDayPercentage,
            'present_percentage' => $attendancePercentage // Alias for consistency with view
        ];
        
        return view('employee.attendances.index', compact(
            'todayAttendance', 
            'monthlySummary', 
            'attendances',
            'month',
            'year',
            'stats'
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
        
        $attendance = Attendance::where('user_id', $user->id)
            ->findOrFail($id);
        
        // Get monthly summary
        $month = Carbon::parse($attendance->date)->format('m');
        $year = Carbon::parse($attendance->date)->format('Y');
        $monthlySummary = $this->getMonthlyAttendanceSummary($user->id, $month, $year);
        
        return view('employee.attendances.show', compact('attendance', 'monthlySummary'));
    }

    /**
     * Check in for today.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');
        
        // Check if already checked in today
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();
        
        if ($todayAttendance) {
            return redirect()->back()
                ->with('error', 'You have already checked in today.');
        }
        
        // Get company settings for check-in time
        $companyStartTime = '09:00'; // Default start time, should be from company settings
        
        $now = Carbon::now();
        $checkInTime = $now->format('H:i');
        
        // Determine if late based on company start time
        $startTime = Carbon::createFromFormat('H:i', $companyStartTime);
        $isLate = $now->gt($startTime);
        
        // Get the employee record for the user
        $employee = $user->employee;
        
        if (!$employee) {
            return redirect()->back()
                ->with('error', 'No employee record found for your account. Please contact HR.');
        }
        
        $attendance = new Attendance();
        $attendance->user_id = $user->id;
        $attendance->employee_id = $employee->id; // Use the employee's ID
        $attendance->date = $today;
        $attendance->check_in = $checkInTime;
        $attendance->status = $isLate ? 'late' : 'present';
        $attendance->is_late = $isLate;
        $attendance->save();
        
        return redirect()->back()
            ->with('success', 'You have successfully checked in.');
    }

    /**
     * Check out for today.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');
        
        // Check if already checked in today
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();
        
        if (!$todayAttendance) {
            return redirect()->back()
                ->with('error', 'You have not checked in today.');
        }
        
        if ($todayAttendance->check_out) {
            return redirect()->back()
                ->with('error', 'You have already checked out today.');
        }
        
        $now = Carbon::now();
        $checkOutTime = $now->format('H:i');
        
        $todayAttendance->check_out = $checkOutTime;
        
        // Calculate working hours
        $checkIn = Carbon::createFromFormat('Y-m-d H:i', $today . ' ' . $todayAttendance->check_in);
        $checkOut = Carbon::createFromFormat('Y-m-d H:i', $today . ' ' . $checkOutTime);
        $todayAttendance->working_hours = $checkOut->diffInHours($checkIn) + ($checkOut->diffInMinutes($checkIn) % 60) / 60;
        
        $todayAttendance->save();
        
        return redirect()->back()
            ->with('success', 'You have successfully checked out.');
    }

    /**
     * Get monthly attendance summary for the employee.
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