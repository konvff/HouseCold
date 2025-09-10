<?php

namespace App\Enums;

enum SMSNotificationStatus: string
{
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';
    case RECEIVED = 'received';

    public function label(): string
    {
        return match($this) {
            self::SENT => 'Sent',
            self::DELIVERED => 'Delivered',
            self::FAILED => 'Failed',
            self::RECEIVED => 'Received',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::SENT => 'info',
            self::DELIVERED => 'success',
            self::FAILED => 'danger',
            self::RECEIVED => 'primary',
        };
    }
}

