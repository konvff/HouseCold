<?php

namespace App\Services\Contracts;

use App\Models\Appointment;
use App\Models\Technician;

interface SMSServiceInterface
{
    public function notifyAvailableTechnicians(Appointment $appointment): int;
    public function sendAppointmentNotification(Technician $technician, Appointment $appointment): bool;
    public function sendAcceptanceConfirmation(Technician $technician, Appointment $appointment): bool;
    public function notifyAppointmentTaken(Appointment $appointment, Technician $assignedTechnician): void;
    public function sendAppointmentReminder(Technician $technician, Appointment $appointment): bool;
}

