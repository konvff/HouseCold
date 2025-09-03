<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHold extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'amount',
        'status',
        'cardknox_transaction_id',
        'cardknox_auth_code',
        'card_last_four',
        'card_type',
        'expires_at',
        'captured_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'captured_at' => 'datetime'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
