<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'hourly_rate',
        'estimated_duration_minutes',
        'is_active'
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'estimated_duration_minutes' => 'integer',
        'is_active' => 'boolean'
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function technicians()
    {
        return $this->belongsToMany(Technician::class, 'technician_service_type', 'service_type_id', 'technician_id');
    }

    public function technicianAvailabilities()
    {
        return $this->hasManyThrough(
            TechnicianAvailability::class,
            Technician::class,
            'id', // Foreign key on technicians table
            'technician_id', // Foreign key on technician_availabilities table
            'id', // Local key on service_types table
            'id' // Local key on technicians table
        )->whereHas('technician', function($query) {
            $query->whereHas('serviceTypes', function($q) {
                $q->where('service_type_id', $this->id);
            });
        });
    }
}
