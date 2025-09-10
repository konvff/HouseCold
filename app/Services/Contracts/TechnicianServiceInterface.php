<?php

namespace App\Services\Contracts;

use App\Models\Technician;

interface TechnicianServiceInterface
{
    public function createTechnician(array $data): Technician;
    public function updateTechnician(int $id, array $data): bool;
    public function deleteTechnician(int $id): bool;
    public function getAvailableTechnicians(): array;
    public function getTechniciansByServiceType(int $serviceTypeId): array;
    public function updateTechnicianAvailability(int $technicianId, array $availabilityData): bool;
    public function toggleTechnicianStatus(int $technicianId): bool;
}
