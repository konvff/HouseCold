<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return User::where('id', $id)->update($data) > 0;
    }

    public function delete(int $id): bool
    {
        return User::destroy($id) > 0;
    }

    public function getByRole(string $role): Collection
    {
        return User::where('role', $role)->get();
    }

    public function getActiveUsers(): Collection
    {
        return User::whereNotNull('email_verified_at')->get();
    }
}
