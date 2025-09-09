<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'name',
        'date',
        'description',
        'is_recurring',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
