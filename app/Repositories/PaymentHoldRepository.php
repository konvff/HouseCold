<?php

namespace App\Repositories;

use App\Models\PaymentHold;
use App\Repositories\Contracts\PaymentHoldRepositoryInterface;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Collection;

class PaymentHoldRepository implements PaymentHoldRepositoryInterface
{
    public function find(int $id): ?PaymentHold
    {
        return PaymentHold::find($id);
    }

    public function findByAppointmentId(int $appointmentId): ?PaymentHold
    {
        return PaymentHold::where('appointment_id', $appointmentId)->first();
    }

    public function create(array $data): PaymentHold
    {
        return PaymentHold::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return PaymentHold::where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return PaymentHold::destroy($id) > 0;
    }

    public function getByStatus(string $status): Collection
    {
        return PaymentHold::where('status', $status)->get();
    }

    public function getExpiredHolds(): Collection
    {
        return PaymentHold::where('status', PaymentStatus::AUTHORIZED->value)
            ->where('expires_at', '<', now())
            ->get();
    }
}
