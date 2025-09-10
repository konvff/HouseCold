<?php

namespace App\Services\Contracts;

use App\Models\Appointment;
use App\Models\TimeLog;

interface TimeLogServiceInterface
{
    public function startTimer(Appointment $appointment): TimeLog;
    public function pauseTimer(Appointment $appointment): bool;
    public function resumeTimer(Appointment $appointment): bool;
    public function stopTimer(Appointment $appointment): bool;
    public function getCurrentTimer(Appointment $appointment): ?TimeLog;
    public function calculateTotalTime(Appointment $appointment): int;
    public function calculateActualCost(Appointment $appointment): float;
}
