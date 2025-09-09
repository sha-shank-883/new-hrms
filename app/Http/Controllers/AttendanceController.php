namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the attendances.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get filter parameters
        $employeeId = $request->input('employee_id');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Different views based on user role
        if ($user->hasRole('super_admin') || $user->hasRole('hr_manager')) {
            // Admin/HR view - see all attendances
            $query = Attendance::query();

            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }

            // Get employees for the filter dropdown
            $employees = Employee::with('user')->get();

            $attendances = $query->whereBetween('date', [$startDate, $endDate])
                ->with(['employee.user'])
                ->orderBy('date', 'desc')
                ->orderBy('check_in', 'desc')
                ->paginate(15);

            return view('admin.attendances.index', compact('attendances', 'employees', 'employeeId', 'startDate', 'endDate'));
        } elseif ($user->hasRole('department_manager')) {
            // Department manager view - see attendances from their department
            $departmentId = $user->employee->department_id;

            $query = Attendance::whereHas('employee', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            });

            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }

            // Get employees for the filter dropdown (only from this department)
            $employees = Employee::where('department_id', $departmentId)
                ->with('user')
                ->get();

            $attendances = $query->whereBetween('date', [$startDate, $endDate])
                ->with(['employee.user'])
                ->orderBy('date', 'desc')
                ->orderBy('check_in', 'desc')
                ->paginate(15);

            return view('admin.attendances.index', compact('attendances', 'employees', 'employeeId', 'startDate', 'endDate'));
        } else {
            // Employee view - see only their own attendance
            $employeeId = $user->employee->id;

            $attendances = Attendance::where('employee_id', $employeeId)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->paginate(15);

            return view('employee.attendances.index', compact('attendances', 'startDate', 'endDate'));
        }
    }

    /**
     * Show the form for recording attendance.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin') || $user->hasRole('hr_manager')) {
            // Admin/HR can record attendance for any employee
            $employees = Employee::with('user')->get();
            return view('admin.attendances.create', compact('employees'));
        } else {
            // Employee can only check in/out for themselves
            $today = now()->toDateString();
            $attendance = Attendance::where('employee_id', $user->employee->id)
                ->where('date', $today)
                ->first();

            return view('employee.attendances.check-in-out', compact('attendance'));
        }
    }

    /**
     * Store a newly created attendance record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin') || $user->hasRole('hr_manager')) {
            return $this->storeAdminAttendance($request);
        } else {
            return $this->storeEmployeeAttendance($user);
        }
    }

    /**
     * Store attendance record for admin/HR.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function storeAdminAttendance($request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'required|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,half_day,late',
            'notes' => 'nullable|string',
        ]);

        // Check if attendance record already exists for the given employee and date
        $existingAttendance = Attendance::where('employee_id', $request->employee_id)
            ->where('date', $request->date)
            ->first();

        if ($existingAttendance) {
            return redirect()->back()->with('error', 'An attendance record already exists for this employee on this date.');
        }

        // Create attendance record
        $attendance = new Attendance();
        $attendance->employee_id = $request->employee_id;
        $attendance->date = Carbon::parse($request->date);
        $attendance->check_in = $request->check_in;
        $attendance->check_out = $request->check_out;
        $attendance->status = $request->status;
        $attendance->notes = $request->notes;
        $attendance->save();

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Attendance record created successfully.');
    }

    /**
     * Store attendance record for employee self-service.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    private function storeEmployeeAttendance($user)
    {
        $employeeId = $user->employee->id;
        $today = Carbon::now()->toDateString();
        $now = Carbon::now()->format('H:i');

        // Check if the employee has already checked in today
        $todayAttendance = Attendance::where('employee_id', $employeeId)
            ->where('date', $today)
            ->first();

        if ($todayAttendance) {
            // If already checked in but not checked out, then check out
            if (!$todayAttendance->check_out) {
                return $this->handleEmployeeCheckOut($todayAttendance, $now);
            } else {
                return redirect()->route('employee.attendances.index')
                    ->with('error', 'You have already completed your attendance for today.');
            }
        } else {
            return $this->handleEmployeeCheckIn($employeeId, $today, $now);
        }
    }

    /**
     * Handle employee check-out.
     *
     * @param  \App\Models\Attendance  $attendance
     * @param  string  $checkOutTime
     * @return \Illuminate\Http\Response
     */
    private function handleEmployeeCheckOut($attendance, $checkOutTime)
    {
        if (!$attendance->check_out) {
            $attendance->check_out = $checkOutTime;

            // Calculate working hours
            $checkIn = Carbon::createFromFormat('H:i', $attendance->check_in);
            $checkOut = Carbon::createFromFormat('H:i', $checkOutTime);
            $hoursWorked = $checkIn->diffInHours($checkOut) + ($checkIn->diffInMinutes($checkOut) % 60) / 60;

            $attendance->hours_worked = $hoursWorked;
            $attendance->save();

            return redirect()->route('employee.attendances.index')
                ->with('success', 'Checked out successfully at ' . $checkOutTime);
        }

        return redirect()->route('employee.attendances.index')
            ->with('error', 'You have already completed your attendance for today.');
    }

    /**
     * Handle employee check-in.
     *
     * @param  int  $employeeId
     * @param  string  $date
     * @param  string  $checkInTime
     * @return \Illuminate\Http\Response
     */
    private function handleEmployeeCheckIn($employeeId, $date, $checkInTime)
    {
        $attendance = new Attendance();
        $attendance->employee_id = $employeeId;
        $attendance->date = $date;
        $attendance->check_in = $checkInTime;

        // Determine if late based on company policy
        $startTime = Carbon::createFromFormat('H:i', '09:00');
        $currentTime = Carbon::createFromFormat('H:i', $checkInTime);

        if ($currentTime->greaterThan($startTime)) {
            $attendance->status = 'late';
            $attendance->notes = 'Checked in late at ' . $checkInTime;
        } else {
            $attendance->status = 'present';
        }

        $attendance->save();

        return redirect()->route('employee.attendances.index')
            ->with('success', 'Checked in successfully at ' . $checkInTime);
    }
}
