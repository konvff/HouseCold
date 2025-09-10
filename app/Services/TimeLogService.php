<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\TimeLog;
use App\Repositories\Contracts\TimeLogRepositoryInterface;
use App\Services\Contracts\TimeLogServiceInterface;
use App\Enums\TimeLogStatus;

class TimeLogService implements TimeLogServiceInterface
{
    public function __construct(
        private TimeLogRepositoryInterface $timeLogRepository
    ) {}

    public function startTimer(Appointment $appointment): TimeLog
    {
        $currentTimer = $this->getCurrentTimer($appointment);
        if ($currentTimer) {
            throw new \Exception('Timer is already running for this appointment');
        }

        return $this->timeLogRepository->create([
            'appointment_id' => $appointment->id,
            'started_at' => now(),
            'status' => TimeLogStatus::RUNNING->value
        ]);
    }

    public function pauseTimer(Appointment $appointment): bool
    {
        $currentTimer = $this->getCurrentTimer($appointment);
        if (!$currentTimer || $currentTimer->status !== TimeLogStatus::RUNNING->value) {
            return false;
        }

        return $this->timeLogRepository->update($currentTimer->id, [
            'status' => TimeLogStatus::PAUSED->value,
            'paused_at' => now()
        ]);
    }

    public function resumeTimer(Appointment $appointment): bool
    {
        $currentTimer = $this->getCurrentTimer($appointment);
        if (!$currentTimer || $currentTimer->status !== TimeLogStatus::PAUSED->value) {
            return false;
        }

        $pausedDuration = $currentTimer->paused_duration ?? 0;
        $pausedDuration += now()->diffInSeconds($currentTimer->paused_at);

        return $this->timeLogRepository->update($currentTimer->id, [
            'status' => TimeLogStatus::RUNNING->value,
            'paused_duration' => $pausedDuration,
            'paused_at' => null
        ]);
    }

    public function stopTimer(Appointment $appointment): bool
    {
        $currentTimer = $this->getCurrentTimer($appointment);
        if (!$currentTimer) {
            return false;
        }

        $pausedDuration = $currentTimer->paused_duration ?? 0;
        if ($currentTimer->paused_at) {
            $pausedDuration += now()->diffInSeconds($currentTimer->paused_at);
        }

        return $this->timeLogRepository->update($currentTimer->id, [
            'ended_at' => now(),
            'status' => TimeLogStatus::STOPPED->value,
            'paused_duration' => $pausedDuration
        ]);
    }

    public function getCurrentTimer(Appointment $appointment): ?TimeLog
    {
        return $this->timeLogRepository->getCurrentTimeLog($appointment->id);
    }

    public function calculateTotalTime(Appointment $appointment): int
    {
        $totalSeconds = 0;
        foreach ($appointment->timeLogs as $timeLog) {
            if ($timeLog->started_at && $timeLog->ended_at) {
                $start = \Carbon\Carbon::parse($timeLog->started_at);
                $end = \Carbon\Carbon::parse($timeLog->ended_at);
                $totalSeconds += $end->diffInSeconds($start);
                $totalSeconds -= $timeLog->paused_duration ?? 0;
            }
        }
        return $totalSeconds;
    }

    public function calculateActualCost(Appointment $appointment): float
    {
        $totalMinutes = $this->calculateTotalTime($appointment) / 60;

        if ($totalMinutes === 0) {
            return $appointment->estimated_cost;
        }

        $hourlyRate = $appointment->serviceType->hourly_rate;
        return ($hourlyRate / 60) * $totalMinutes;
    }
}
