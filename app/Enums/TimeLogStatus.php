<?php

namespace App\Enums;

enum TimeLogStatus: string
{
    case RUNNING = 'running';
    case PAUSED = 'paused';
    case STOPPED = 'stopped';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match($this) {
            self::RUNNING => 'Running',
            self::PAUSED => 'Paused',
            self::STOPPED => 'Stopped',
            self::COMPLETED => 'Completed',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::RUNNING => 'success',
            self::PAUSED => 'warning',
            self::STOPPED => 'secondary',
            self::COMPLETED => 'primary',
        };
    }

    public function canStart(): bool
    {
        return in_array($this, [self::STOPPED, self::PAUSED]);
    }

    public function canPause(): bool
    {
        return $this === self::RUNNING;
    }

    public function canResume(): bool
    {
        return $this === self::PAUSED;
    }

    public function canStop(): bool
    {
        return in_array($this, [self::RUNNING, self::PAUSED]);
    }
}
