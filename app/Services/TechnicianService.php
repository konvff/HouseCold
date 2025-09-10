<?php

namespace App\Services;

use App\Models\Technician;
use App\Repositories\Contracts\TechnicianRepositoryInterface;
use App\Repositories\Contracts\TechnicianAvailabilityRepositoryInterface;
use App\Services\Contracts\TechnicianServiceInterface;
use App\Enums\TechnicianStatus;

class TechnicianService implements TechnicianServiceInterface
{
    public function __construct(
        private TechnicianRepositoryInterface $technicianRepository,
        private TechnicianAvailabilityRepositoryInterface $availabilityRepository
    ) {}

    public function createTechnician(array $data): Technician
    {
        return $this->technicianRepository->create($data);
    }

    public function updateTechnician(int $id, array $data): bool
    {
        return $this->technicianRepository->update($id, $data);
    }

    public function deleteTechnician(int $id): bool
    {
        return $this->technicianRepository->delete($id);
    }

    public function getAvailableTechnicians(): array
    {
        return $this->technicianRepository->getAvailableTechnicians()->toArray();
    }

    public function getTechniciansByServiceType(int $serviceTypeId): array
    {
        return $this->technicianRepository->getTechniciansByServiceType($serviceTypeId)->toArray();
    }

    public function updateTechnicianAvailability(int $technicianId, array $availabilityData): bool
    {
        foreach ($availabilityData as $availability) {
            if (isset($availability['id'])) {
                $this->availabilityRepository->update($availability['id'], $availability);
            } else {
                $availability['technician_id'] = $technicianId;
                $this->availabilityRepository->create($availability);
            }
        }
        return true;
    }

    public function toggleTechnicianStatus(int $technicianId): bool
    {
        $technician = $this->technicianRepository->find($technicianId);
        if (!$technician) {
            return false;
        }

        $newStatus = $technician->status === TechnicianStatus::ACTIVE->value
            ? TechnicianStatus::INACTIVE->value
            : TechnicianStatus::ACTIVE->value;

        return $this->technicianRepository->update($technicianId, ['status' => $newStatus]);
    }
}
