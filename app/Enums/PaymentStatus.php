<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case AUTHORIZED = 'authorized';
    case CAPTURED = 'captured';
    case VOIDED = 'voided';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::AUTHORIZED => 'Authorized',
            self::CAPTURED => 'Captured',
            self::VOIDED => 'Voided',
            self::REFUNDED => 'Refunded',
            self::FAILED => 'Failed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::AUTHORIZED => 'info',
            self::CAPTURED => 'success',
            self::VOIDED => 'secondary',
            self::REFUNDED => 'primary',
            self::FAILED => 'danger',
        };
    }

    public function isCompleted(): bool
    {
        return in_array($this, [self::CAPTURED, self::REFUNDED]);
    }

    public function canCapture(): bool
    {
        return $this === self::AUTHORIZED;
    }

    public function canVoid(): bool
    {
        return in_array($this, [self::AUTHORIZED, self::PENDING]);
    }

    public function canRefund(): bool
    {
        return $this === self::CAPTURED;
    }
}
