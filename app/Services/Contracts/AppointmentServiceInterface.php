<?php

namespace App\Services\Contracts;

use App\Models\Appointment;
use App\Models\Technician;

interface AppointmentServiceInterface
{
    public function createAppointment(array $data): Appointment;
    public function assignTechnician(Appointment $appointment, Technician $technician): bool;
    public function processTechnicianResponse(Technician $technician, string $response): void;
}

