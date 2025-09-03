<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'technician_id',
        'day_of_week',
        'start_time',
        'end_time',
        'start_date',
        'end_date',
        'is_recurring',
        'is_active'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }
}
