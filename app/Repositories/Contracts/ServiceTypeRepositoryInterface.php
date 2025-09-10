<?php

namespace App\Repositories\Contracts;

use App\Models\ServiceType;
use Illuminate\Database\Eloquent\Collection;

interface ServiceTypeRepositoryInterface
{
    public function find(int $id): ?ServiceType;
    public function create(array $data): ServiceType;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getActiveServiceTypes(): Collection;
    public function getServiceTypesByCategory(string $category): Collection;
}
