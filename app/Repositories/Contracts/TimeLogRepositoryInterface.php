<?php

namespace App\Repositories\Contracts;

use App\Models\TimeLog;
use Illuminate\Database\Eloquent\Collection;

interface TimeLogRepositoryInterface
{
    public function find(int $id): ?TimeLog;
    public function create(array $data): TimeLog;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getByAppointmentId(int $appointmentId): Collection;
    public function getCurrentTimeLog(int $appointmentId): ?TimeLog;
    public function getCompletedTimeLogs(int $appointmentId): Collection;
}
