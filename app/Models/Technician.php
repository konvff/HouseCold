<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'specialties',
        'status',
        'hourly_rate'
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'specialties' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function availabilities()
    {
        return $this->hasMany(TechnicianAvailability::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
    }

    public function serviceTypes()
    {
        return $this->belongsToMany(ServiceType::class, 'technician_service_type', 'technician_id', 'service_type_id');
    }
}
