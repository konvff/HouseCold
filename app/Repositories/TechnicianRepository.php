<?php

namespace App\Repositories;

use App\Models\Technician;
use App\Repositories\Contracts\TechnicianRepositoryInterface;
use App\Enums\TechnicianStatus;
use Illuminate\Database\Eloquent\Collection;

class TechnicianRepository implements TechnicianRepositoryInterface
{
    public function find(int $id): ?Technician
    {
        return Technician::find($id);
    }

    public function findByUserId(int $userId): ?Technician
    {
        return Technician::where('user_id', $userId)->first();
    }

    public function create(array $data): Technician
    {
        return Technician::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Technician::where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return Technician::destroy($id) > 0;
    }

    public function getActiveTechnicians(): Collection
    {
        return Technician::where('status', TechnicianStatus::ACTIVE->value)->get();
    }

    public function getAvailableTechnicians(): Collection
    {
        return Technician::where('status', TechnicianStatus::ACTIVE->value)
            ->where('is_available', true)
            ->get();
    }

    public function getTechniciansByServiceType(int $serviceTypeId): Collection
    {
        return Technician::where('status', TechnicianStatus::ACTIVE->value)
            ->whereHas('serviceTypes', function($query) use ($serviceTypeId) {
                $query->where('service_type_id', $serviceTypeId);
            })
            ->get();
    }

    public function getTechniciansBySpecialty(string $specialty): Collection
    {
        return Technician::where('status', TechnicianStatus::ACTIVE->value)
            ->whereJsonContains('specialties', $specialty)
            ->get();
    }
}
