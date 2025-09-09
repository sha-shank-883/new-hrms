<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PayrollService
{
    /**
     * Calculate the net salary for an employee
     *
     * @param float $basicSalary
     * @param float $allowances
     * @param float $deductions
     * @return float
     */
    public function calculateNetSalary($basicSalary, $allowances, $deductions)
    {
        return (float)($basicSalary + $allowances - $deductions);
    }

    /**
     * Generate payroll for a specific employee for a given month and year
     *
     * @param Employee $employee
     * @param string $month
     * @param int $year
     * @return Payroll|null
     */
    public function generateEmployeePayroll(Employee $employee, string $month, int $year): ?Payroll
    {
        // Check if payroll already exists for this employee, month and year
        $existingPayroll = Payroll::where('employee_id', $employee->id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($existingPayroll) {
            return null; // Payroll already exists
        }

        // Get basic salary from employee record
        $basicSalary = $employee->salary;

        // Calculate allowances (can be extended with more complex logic)
        $allowances = $this->calculateAllowances($employee, $month, $year);

        // Calculate deductions (can be extended with more complex logic)
        $deductions = $this->calculateDeductions($employee, $month, $year);

        // Calculate net salary
        $netSalary = $this->calculateNetSalary(
            (float)$basicSalary,
            (float)$allowances,
            (float)$deductions
        );

        // Create new payroll record
        $payroll = new Payroll();
        $payroll->employee_id = $employee->id;
        $payroll->month = $month;
        $payroll->year = $year;
        $payroll->basic_salary = (float)$basicSalary;
        $payroll->allowances = (float)$allowances;
        $payroll->deductions = (float)$deductions;
        $payroll->net_salary = $netSalary;
        $payroll->status = 'pending';
        $payroll->created_by = Auth::id();
        $payroll->save();

        return $payroll;
    }

    /**
     * Calculate allowances for an employee
     * This can be extended with more complex logic based on business requirements
     *
     * @param Employee $employee
     * @param string $month
     * @param int $year
     * @return float
     */
    protected function calculateAllowances(Employee $employee, string $month, int $year): float
    {
        // Basic implementation - can be extended with more complex logic
        // For example, attendance bonuses, performance bonuses, etc.
        $allowances = 0;

        // Example: Add transportation allowance
        $allowances += 100;

        // Example: Add meal allowance
        $allowances += 150;

        // Example: Add overtime allowance based on attendance
        $overtimeAllowance = $this->calculateOvertimeAllowance($employee, $month, $year);
        $allowances += $overtimeAllowance;

        return $allowances;
    }

    /**
     * Calculate deductions for an employee
     * This can be extended with more complex logic based on business requirements
     *
     * @param Employee $employee
     * @param string $month
     * @param int $year
     * @return float
     */
    protected function calculateDeductions(Employee $employee, string $month, int $year): float
    {
        // Basic implementation - can be extended with more complex logic
        // For example, tax deductions, insurance, etc.
        $deductions = 0;

        // Example: Tax deduction (simplified)
        $taxRate = 0.1; // 10%
        $taxableAmount = $employee->salary;
        $taxDeduction = $taxableAmount * $taxRate;
        $deductions += $taxDeduction;

        // Example: Absence deduction based on attendance and leave records
        $absenceDeduction = $this->calculateAbsenceDeduction($employee, $month, $year);
        $deductions += $absenceDeduction;

        return $deductions;
    }

    /**
     * Calculate overtime allowance based on attendance records
     *
     * @param Employee $employee
     * @param string $month
     * @param int $year
     * @return float
     */
    protected function calculateOvertimeAllowance(Employee $employee, string $month, int $year): float
    {
        // Convert month name to month number
        $monthNumber = Carbon::parse("1 {$month} {$year}")->month;
        
        // Get attendance records for the month with overtime
        $attendanceRecords = Attendance::where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNumber)
            ->where('overtime_hours', '>', 0)
            ->get();

        // Calculate overtime allowance
        $overtimeRate = 1.5; // 1.5x regular hourly rate
        $hourlyRate = $employee->salary / 176; // Assuming 22 working days of 8 hours each
        $totalOvertimeHours = $attendanceRecords->sum('overtime_hours');
        
        return $totalOvertimeHours * $hourlyRate * $overtimeRate;
    }

    /**
     * Calculate deductions for absences
     *
     * @param Employee $employee
     * @param string $month
     * @param int $year
     * @return float
     */
    protected function calculateAbsenceDeduction(Employee $employee, string $month, int $year): float
    {
        // Convert month name to month number
        $monthNumber = Carbon::parse("1 {$month} {$year}")->month;
        
        // Get the number of working days in the month
        $daysInMonth = Carbon::parse("1 {$month} {$year}")->daysInMonth;
        $workingDays = 0;
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::parse("{$year}-{$monthNumber}-{$day}");
            if (!$date->isWeekend()) {
                $workingDays++;
            }
        }
        
        // Get attendance records for the month
        $attendanceRecords = Attendance::where('employee_id', $employee->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNumber)
            ->get();
        
        // Get approved leave requests for the month
        $approvedLeaves = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where(function($query) use ($year, $monthNumber) {
                $query->whereYear('start_date', $year)
                    ->whereMonth('start_date', $monthNumber);
            })
            ->orWhere(function($query) use ($year, $monthNumber) {
                $query->whereYear('end_date', $year)
                    ->whereMonth('end_date', $monthNumber);
            })
            ->get();
        
        // Calculate days present
        $daysPresent = $attendanceRecords->count();
        
        // Calculate days on approved leave
        $daysOnApprovedLeave = 0;
        foreach ($approvedLeaves as $leave) {
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);
            
            // Adjust dates to be within the current month
            if ($startDate->month < $monthNumber && $startDate->year == $year) {
                $startDate = Carbon::parse("1 {$month} {$year}");
            }
            
            if ($endDate->month > $monthNumber && $endDate->year == $year) {
                $endDate = Carbon::parse("{$daysInMonth} {$month} {$year}");
            }
            
            // Count working days in the leave period
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                if (!$currentDate->isWeekend()) {
                    $daysOnApprovedLeave++;
                }
                $currentDate->addDay();
            }
        }
        
        // Calculate unauthorized absences
        $unauthorizedAbsences = $workingDays - $daysPresent - $daysOnApprovedLeave;
        $unauthorizedAbsences = max(0, $unauthorizedAbsences); // Ensure no negative value
        
        // Calculate deduction amount
        $dailyRate = $employee->salary / $workingDays;
        return $unauthorizedAbsences * $dailyRate;
    }

    /**
     * Generate payrolls for all active employees for a given month and year
     *
     * @param string $month
     * @param int $year
     * @return array
     */
    public function generatePayrollsForAllEmployees(string $month, int $year): array
    {
        $employees = Employee::where('status', 'active')->get();
        
        $results = [
            'success' => 0,
            'skipped' => 0,
            'failed' => 0
        ];
        
        foreach ($employees as $employee) {
            try {
                $payroll = $this->generateEmployeePayroll($employee, $month, $year);
                
                if ($payroll) {
                    $results['success']++;
                } else {
                    $results['skipped']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
            }
        }
        
        return $results;
    }
}