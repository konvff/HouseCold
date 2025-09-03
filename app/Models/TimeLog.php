<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'technician_id',
        'started_at',
        'ended_at',
        'duration_minutes',
        'notes'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_minutes' => 'integer'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }
}
