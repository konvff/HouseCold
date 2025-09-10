<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function find(int $id): ?Appointment
    {
        return Appointment::find($id);
    }

    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return Appointment::where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return Appointment::destroy($id) > 0;
    }

    public function findByStatus(string $status): Collection
    {
        return Appointment::where('status', $status)->get();
    }

    public function findPendingForTechnician(int $technicianId): ?Appointment
    {
        return Appointment::where('status', AppointmentStatus::PENDING->value)
            ->whereHas('smsNotifications', function($query) use ($technicianId) {
                $query->where('technician_id', $technicianId)
                      ->where('type', 'appointment_notification')
                      ->where('created_at', '>=', now()->subMinutes(15));
            })
            ->with(['serviceType', 'paymentHold'])
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function assignTechnician(int $appointmentId, int $technicianId): bool
    {
        return Appointment::where('id', $appointmentId)
            ->update([
                'technician_id' => $technicianId,
                'status' => AppointmentStatus::CONFIRMED->value
            ]) > 0;
    }

    public function getUpcomingAppointments(int $hours = 1): Collection
    {
        $reminderTime = Carbon::now()->addHours($hours);

        return Appointment::where('status', AppointmentStatus::CONFIRMED->value)
            ->where('technician_id', '!=', null)
            ->whereBetween('scheduled_at', [
                $reminderTime->copy()->subMinutes(5),
                $reminderTime->copy()->addMinutes(5)
            ])
            ->with(['technician.user', 'serviceType'])
            ->get();
    }
}

