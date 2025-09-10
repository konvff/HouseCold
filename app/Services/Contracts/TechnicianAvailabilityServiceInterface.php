<?php

namespace App\Services\Contracts;

use App\Models\TechnicianAvailability;
use App\Models\Technician;

interface TechnicianAvailabilityServiceInterface
{
    public function createAvailability(array $data): TechnicianAvailability;
    public function updateAvailability(int $id, array $data): bool;
    public function deleteAvailability(int $id): bool;
    public function checkOverlappingAvailability(int $technicianId, string $dayOfWeek, string $startTime, string $endTime, ?int $excludeId = null): bool;
    public function getTechnicianAvailabilities(int $technicianId): array;
    public function getAvailableTechniciansForDay(string $dayOfWeek): array;
    public function validateAvailabilityData(array $data): array;
}
