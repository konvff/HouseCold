<?php

namespace App\Repositories\Contracts;

use App\Models\SMSNotification;
use Illuminate\Database\Eloquent\Collection;

interface SMSNotificationRepositoryInterface
{
    public function create(array $data): SMSNotification;
    public function findByTwilioSid(string $sid): ?SMSNotification;
    public function updateStatus(string $sid, string $status): bool;
    public function getRecentNotifications(int $days = 7): Collection;
    public function getNotificationsByType(string $type): Collection;
}

