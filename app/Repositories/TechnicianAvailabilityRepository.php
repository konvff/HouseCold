<?php

namespace App\Repositories;

use App\Models\TechnicianAvailability;
use App\Repositories\Contracts\TechnicianAvailabilityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TechnicianAvailabilityRepository implements TechnicianAvailabilityRepositoryInterface
{
    public function find(int $id): ?TechnicianAvailability
    {
        return TechnicianAvailability::find($id);
    }

    public function create(array $data): TechnicianAvailability
    {
        return TechnicianAvailability::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return TechnicianAvailability::where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return TechnicianAvailability::destroy($id) > 0;
    }

    public function getByTechnicianId(int $technicianId): Collection
    {
        return TechnicianAvailability::where('technician_id', $technicianId)->get();
    }

    public function getByDayOfWeek(string $dayOfWeek): Collection
    {
        return TechnicianAvailability::where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->get();
    }

    public function getActiveAvailabilities(): Collection
    {
        return TechnicianAvailability::where('is_active', true)->get();
    }
}
