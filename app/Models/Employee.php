<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'user_id',
        'department_id',
        'position',
        'joining_date',
        'termination_date',
        'employment_status',
        'salary',
        'phone',
        'emergency_contact',
        'emergency_contact_relationship',
        'address',
        'date_of_birth',
        'gender',
        'marital_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'joining_date' => 'date',
        'termination_date' => 'date',
        'date_of_birth' => 'date',
        'salary' => 'decimal:2',
    ];

    /**
     * Get the user that owns the employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department that the employee belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
    /**
     * Get the leave requests for the employee.
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    /**
     * Get the attendances for the employee.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the payrolls for the employee.
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }
}