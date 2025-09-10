<?php

namespace App\Repositories\Contracts;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Collection;

interface AppointmentRepositoryInterface
{
    public function find(int $id): ?Appointment;
    public function create(array $data): Appointment;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByStatus(string $status): Collection;
    public function findPendingForTechnician(int $technicianId): ?Appointment;
    public function assignTechnician(int $appointmentId, int $technicianId): bool;
    public function getUpcomingAppointments(int $hours = 1): Collection;
}

