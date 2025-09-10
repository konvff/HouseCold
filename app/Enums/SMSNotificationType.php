<?php

namespace App\Enums;

enum SMSNotificationType: string
{
    case APPOINTMENT_NOTIFICATION = 'appointment_notification';
    case ACCEPTANCE_CONFIRMATION = 'acceptance_confirmation';
    case APPOINTMENT_TAKEN = 'appointment_taken';
    case APPOINTMENT_REMINDER = 'appointment_reminder';
    case RESPONSE_RECEIVED = 'response_received';

    public function label(): string
    {
        return match($this) {
            self::APPOINTMENT_NOTIFICATION => 'Appointment Notification',
            self::ACCEPTANCE_CONFIRMATION => 'Acceptance Confirmation',
            self::APPOINTMENT_TAKEN => 'Appointment Taken',
            self::APPOINTMENT_REMINDER => 'Appointment Reminder',
            self::RESPONSE_RECEIVED => 'Response Received',
        };
    }
}

