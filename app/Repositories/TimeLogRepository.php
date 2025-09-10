<?php

namespace App\Repositories;

use App\Models\TimeLog;
use App\Repositories\Contracts\TimeLogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TimeLogRepository implements TimeLogRepositoryInterface
{
    public function find(int $id): ?TimeLog
    {
        return TimeLog::find($id);
    }

    public function create(array $data): TimeLog
    {
        return TimeLog::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return TimeLog::where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return TimeLog::destroy($id) > 0;
    }

    public function getByAppointmentId(int $appointmentId): Collection
    {
        return TimeLog::where('appointment_id', $appointmentId)->get();
    }

    public function getCurrentTimeLog(int $appointmentId): ?TimeLog
    {
        return TimeLog::where('appointment_id', $appointmentId)
            ->whereNull('ended_at')
            ->first();
    }

    public function getCompletedTimeLogs(int $appointmentId): Collection
    {
        return TimeLog::where('appointment_id', $appointmentId)
            ->whereNotNull('ended_at')
            ->get();
    }
}
