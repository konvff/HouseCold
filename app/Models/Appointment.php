<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'service_notes',
        'service_type_id',
        'technician_id',
        'status',
        'scheduled_at',
        'estimated_duration_minutes',
        'estimated_cost',
        'actual_cost'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'estimated_duration_minutes' => 'integer',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2'
    ];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function paymentHold()
    {
        return $this->hasOne(PaymentHold::class);
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
    }

    public function getCurrentTimeLog()
    {
        return $this->timeLogs()->whereNull('ended_at')->first();
    }
}
