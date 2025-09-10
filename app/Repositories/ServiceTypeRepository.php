<?php

namespace App\Repositories;

use App\Models\ServiceType;
use App\Repositories\Contracts\ServiceTypeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ServiceTypeRepository implements ServiceTypeRepositoryInterface
{
    public function find(int $id): ?ServiceType
    {
        return ServiceType::find($id);
    }

    public function create(array $data): ServiceType
    {
        return ServiceType::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return ServiceType::where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return ServiceType::destroy($id) > 0;
    }

    public function getActiveServiceTypes(): Collection
    {
        return ServiceType::where('is_active', true)->get();
    }

    public function getServiceTypesByCategory(string $category): Collection
    {
        return ServiceType::where('is_active', true)
            ->where('category', $category)
            ->get();
    }
}
