<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'default_days',
        'requires_approval',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'default_days' => 'integer',
        'requires_approval' => 'boolean',
    ];

    /**
     * Get the leave requests for the leave type.
     */
    public function leaveRequests
    (): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
