<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\SMSNotificationType;
use App\Enums\SMSNotificationStatus;

class SMSNotification extends Model
{
    protected $table = 'sms_notifications';

    protected $fillable = [
        'appointment_id',
        'technician_id',
        'user_id',
        'type',
        'message',
        'twilio_sid',
        'status',
        'error_message',
        'sent_at',
        'delivered_at',
        'phone_number',
        'direction'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'type' => SMSNotificationType::class,
        'status' => SMSNotificationStatus::class
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
