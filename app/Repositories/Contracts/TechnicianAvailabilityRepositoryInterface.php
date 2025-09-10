<?php

namespace App\Repositories\Contracts;

use App\Models\TechnicianAvailability;
use Illuminate\Database\Eloquent\Collection;

interface TechnicianAvailabilityRepositoryInterface
{
    public function find(int $id): ?TechnicianAvailability;
    public function create(array $data): TechnicianAvailability;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getByTechnicianId(int $technicianId): Collection;
    public function getByDayOfWeek(string $dayOfWeek): Collection;
    public function getActiveAvailabilities(): Collection;
}
