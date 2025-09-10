<?php

namespace App\Enums;

enum TechnicianStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case ON_LEAVE = 'on_leave';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::SUSPENDED => 'Suspended',
            self::ON_LEAVE => 'On Leave',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'secondary',
            self::SUSPENDED => 'danger',
            self::ON_LEAVE => 'warning',
        };
    }

    public function canReceiveAppointments(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isAvailable(): bool
    {
        return in_array($this, [self::ACTIVE]);
    }
}
