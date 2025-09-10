<?php

namespace App\Services\Contracts;

use App\Models\User;

interface UserServiceInterface
{
    public function createUser(array $data): User;
    public function updateUser(int $id, array $data): bool;
    public function deleteUser(int $id): bool;
    public function getUserByEmail(string $email): ?User;
    public function getUsersByRole(string $role): array;
    public function changeUserRole(int $userId, string $role): bool;
}
