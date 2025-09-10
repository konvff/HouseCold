<?php

namespace App\Enums;

enum DayOfWeek: string
{
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';
    case SATURDAY = 'saturday';
    case SUNDAY = 'sunday';

    public function label(): string
    {
        return match($this) {
            self::MONDAY => 'Monday',
            self::TUESDAY => 'Tuesday',
            self::WEDNESDAY => 'Wednesday',
            self::THURSDAY => 'Thursday',
            self::FRIDAY => 'Friday',
            self::SATURDAY => 'Saturday',
            self::SUNDAY => 'Sunday',
        };
    }

    public function shortLabel(): string
    {
        return match($this) {
            self::MONDAY => 'Mon',
            self::TUESDAY => 'Tue',
            self::WEDNESDAY => 'Wed',
            self::THURSDAY => 'Thu',
            self::FRIDAY => 'Fri',
            self::SATURDAY => 'Sat',
            self::SUNDAY => 'Sun',
        };
    }

    public function isWeekend(): bool
    {
        return in_array($this, [self::SATURDAY, self::SUNDAY]);
    }

    public function isWeekday(): bool
    {
        return !$this->isWeekend();
    }

    public static function getAllDays(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function getWeekdays(): array
    {
        return array_map(fn($case) => $case->value, array_filter(self::cases(), fn($case) => $case->isWeekday()));
    }

    public static function getWeekends(): array
    {
        return array_map(fn($case) => $case->value, array_filter(self::cases(), fn($case) => $case->isWeekend()));
    }
}
