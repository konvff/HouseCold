<?php

namespace App\Services;

use App\Models\TechnicianAvailability;
use App\Models\Technician;
use App\Repositories\Contracts\TechnicianAvailabilityRepositoryInterface;
use App\Repositories\Contracts\TechnicianRepositoryInterface;
use App\Services\Contracts\TechnicianAvailabilityServiceInterface;
use App\Enums\DayOfWeek;
use Illuminate\Support\Facades\Validator;

class TechnicianAvailabilityService implements TechnicianAvailabilityServiceInterface
{
    public function __construct(
        private TechnicianAvailabilityRepositoryInterface $availabilityRepository,
        private TechnicianRepositoryInterface $technicianRepository
    ) {}

    public function createAvailability(array $data): TechnicianAvailability
    {
        $validatedData = $this->validateAvailabilityData($data);

        if ($this->checkOverlappingAvailability(
            $validatedData['technician_id'],
            $validatedData['day_of_week'],
            $validatedData['start_time'],
            $validatedData['end_time']
        )) {
            throw new \Exception('This time slot overlaps with existing availability for this technician.');
        }

        return $this->availabilityRepository->create($validatedData);
    }

    public function updateAvailability(int $id, array $data): bool
    {
        $validatedData = $this->validateAvailabilityData($data);

        if ($this->checkOverlappingAvailability(
            $validatedData['technician_id'],
            $validatedData['day_of_week'],
            $validatedData['start_time'],
            $validatedData['end_time'],
            $id
        )) {
            throw new \Exception('This time slot overlaps with existing availability for this technician.');
        }

        return $this->availabilityRepository->update($id, $validatedData);
    }

    public function deleteAvailability(int $id): bool
    {
        return $this->availabilityRepository->delete($id);
    }

    public function checkOverlappingAvailability(int $technicianId, string $dayOfWeek, string $startTime, string $endTime, ?int $excludeId = null): bool
    {
        $query = TechnicianAvailability::where('technician_id', $technicianId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where(function($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    public function getTechnicianAvailabilities(int $technicianId): array
    {
        return $this->availabilityRepository->getByTechnicianId($technicianId)->toArray();
    }

    public function getAvailableTechniciansForDay(string $dayOfWeek): array
    {
        $availabilities = $this->availabilityRepository->getByDayOfWeek($dayOfWeek);
        $technicianIds = $availabilities->pluck('technician_id')->unique();

        return $this->technicianRepository->getActiveTechnicians()
            ->whereIn('id', $technicianIds)
            ->toArray();
    }

    public function validateAvailabilityData(array $data): array
    {
        $validator = Validator::make($data, [
            'technician_id' => 'required|exists:technicians,id',
            'day_of_week' => 'required|in:' . implode(',', DayOfWeek::getAllDays()),
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'is_recurring' => 'boolean',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . implode(', ', $validator->errors()->all()));
        }

        return $validator->validated();
    }
}
