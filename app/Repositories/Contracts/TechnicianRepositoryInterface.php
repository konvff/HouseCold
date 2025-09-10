<?php

namespace App\Repositories\Contracts;

use App\Models\Technician;
use Illuminate\Database\Eloquent\Collection;

interface TechnicianRepositoryInterface
{
    public function find(int $id): ?Technician;
    public function findByUserId(int $userId): ?Technician;
    public function create(array $data): Technician;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getActiveTechnicians(): Collection;
    public function getAvailableTechnicians(): Collection;
    public function getTechniciansByServiceType(int $serviceTypeId): Collection;
    public function getTechniciansBySpecialty(string $specialty): Collection;
}
