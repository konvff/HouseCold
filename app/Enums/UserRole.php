<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case TECHNICIAN = 'technician';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::TECHNICIAN => 'Technician',
            self::CUSTOMER => 'Customer',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ADMIN => 'danger',
            self::TECHNICIAN => 'primary',
            self::CUSTOMER => 'info',
        };
    }

    public function permissions(): array
    {
        return match($this) {
            self::ADMIN => ['manage_all', 'view_reports', 'manage_technicians', 'manage_appointments'],
            self::TECHNICIAN => ['view_own_appointments', 'manage_availability', 'start_timer', 'complete_jobs'],
            self::CUSTOMER => ['book_appointments', 'view_own_appointments'],
        };
    }
}
