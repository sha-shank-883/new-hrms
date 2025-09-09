<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the employee dashboard.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login');
            }

            $today = now()->toDateString();
            
            // Initialize default values
            $data = [
                'currentAttendance' => null,
                'recentLeaveRequests' => collect(),
                'leaveBalances' => [
                    'available' => 0,
                    'taken' => 0,
                    'total' => 0
                ],
                'attendanceCalendar' => []
            ];

            // Get current attendance status with null check
            $data['currentAttendance'] = Attendance::where('employee_id', $user->id)
                ->where('date', '>=', $today . ' 00:00:00')
                ->where('date', '<=', $today . ' 23:59:59')
                ->first();
            
            // Get recent leave requests with null check
            $data['recentLeaveRequests'] = LeaveRequest::where('user_id', $user->id)
                ->with('leaveType')
                ->latest()
                ->take(5)
                ->get() ?? collect();
            
            // Calculate leave balances with null check
            $data['leaveBalances'] = $this->getLeaveBalances($user) ?? [
                'available' => 0,
                'taken' => 0,
                'total' => 0
            ];
            
            // Generate attendance calendar with null check
            $data['attendanceCalendar'] = $this->generateAttendanceCalendar($user) ?? [];

            // Log the data being passed to the view for debugging
            \Log::debug('Employee Dashboard Data:', [
                'user_id' => $user->id,
                'has_attendance' => !is_null($data['currentAttendance']),
                'leave_requests_count' => $data['recentLeaveRequests']->count(),
                'leave_balances' => $data['leaveBalances']
            ]);

            return view('employee.dashboard', $data);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in Employee Dashboard: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return a default view with error message
            return view('employee.dashboard', [
                'currentAttendance' => null,
                'recentLeaveRequests' => collect(),
                'leaveBalances' => [
                    'available' => 0,
                    'taken' => 0,
                    'total' => 0
                ],
                'attendanceCalendar' => [],
                'error' => 'An error occurred while loading the dashboard.'
            ]);
        }
    }

    /**
     * Get leave balances for the employee.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    protected function getLeaveBalances($user)
    {
        $leaveTypes = LeaveType::all();
        $balances = [];
        
        foreach ($leaveTypes as $type) {
            $usedDays = LeaveRequest::where('user_id', $user->id)
                ->where('leave_type_id', $type->id)
                ->where('status', 'approved')
                ->whereYear('start_date', now()->year)
                ->sum('days');
                
            $balances[$type->name] = [
                'total' => $type->annual_quota,
                'used' => $usedDays,
                'remaining' => max(0, $type->annual_quota - $usedDays)
            ];
        }
        
        // For simplicity, return the first leave type's balance
        $firstType = $leaveTypes->first();
        if ($firstType) {
            return [
                'total' => $firstType->annual_quota,
                'used' => $balances[$firstType->name]['used'] ?? 0,
                'available' => $balances[$firstType->name]['remaining'] ?? $firstType->annual_quota
            ];
        }
        
        return ['total' => 0, 'used' => 0, 'available' => 0];
    }
    
    /**
     * Generate attendance calendar for the current month.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    protected function generateAttendanceCalendar($user)
    {
        $today = now();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();
        
        // Get all attendance records for the month
        $attendance = Attendance::where('employee_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy(function($item) {
                return $item->date->format('Y-m-d');
            });
        
        // Get all leave requests for the month
        $leaveDays = LeaveRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                      ->orWhere(function($q) use ($startOfMonth, $endOfMonth) {
                          $q->where('start_date', '<=', $startOfMonth)
                            ->where('end_date', '>=', $endOfMonth);
                      });
            })
            ->get()
            ->flatMap(function($request) {
                $dates = [];
                $current = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);
                
                while ($current->lte($endDate)) {
                    $dates[$current->format('Y-m-d')] = 'leave';
                    $current->addDay();
                }
                
                return $dates;
            });
        
        // Generate calendar
        $calendar = [];
        $currentDay = $startOfMonth->copy();
        
        // Start with empty days before the 1st of the month
        $week = array_fill(0, $startOfMonth->dayOfWeek, ['day' => '', 'status' => '']);
        
        while ($currentDay->lte($endOfMonth)) {
            $dateStr = $currentDay->format('Y-m-d');
            $isWeekend = $currentDay->isWeekend();
            $isToday = $currentDay->isToday();
            
            if (isset($leaveDays[$dateStr])) {
                $status = 'leave';
            } elseif (isset($attendance[$dateStr])) {
                $status = 'present';
            } else {
                $status = $isWeekend ? '' : 'absent';
            }
            
            $week[] = [
                'day' => $currentDay->day,
                'status' => $status,
                'is_today' => $isToday,
                'is_weekend' => $isWeekend
            ];
            
            // If we've reached the end of the week, add it to the calendar and start a new week
            if ($currentDay->dayOfWeek == 6) {
                $calendar[] = $week;
                $week = [];
            }
            
            $currentDay->addDay();
        }
        
        // Add the last week if it's not empty
        if (!empty($week)) {
            // Fill the remaining days of the week with empty days
            while (count($week) < 7) {
                $week[] = ['day' => '', 'status' => ''];
            }
            $calendar[] = $week;
        }
        
        return $calendar;
    }
}
