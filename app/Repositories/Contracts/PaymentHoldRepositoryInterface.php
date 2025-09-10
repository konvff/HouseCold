<?php

namespace App\Repositories\Contracts;

use App\Models\PaymentHold;
use Illuminate\Database\Eloquent\Collection;

interface PaymentHoldRepositoryInterface
{
    public function find(int $id): ?PaymentHold;
    public function findByAppointmentId(int $appointmentId): ?PaymentHold;
    public function create(array $data): PaymentHold;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getByStatus(string $status): Collection;
    public function getExpiredHolds(): Collection;
}
