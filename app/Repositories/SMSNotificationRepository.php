<?php

namespace App\Repositories;

use App\Models\SMSNotification;
use App\Repositories\Contracts\SMSNotificationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SMSNotificationRepository implements SMSNotificationRepositoryInterface
{
    public function create(array $data): SMSNotification
    {
        return SMSNotification::create($data);
    }

    public function findByTwilioSid(string $sid): ?SMSNotification
    {
        return SMSNotification::where('twilio_sid', $sid)->first();
    }

    public function updateStatus(string $sid, string $status): bool
    {
        return SMSNotification::where('twilio_sid', $sid)
            ->update([
                'status' => $status,
                'delivered_at' => $status === 'delivered' ? now() : null
            ]) > 0;
    }

    public function getRecentNotifications(int $days = 7): Collection
    {
        return SMSNotification::where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getNotificationsByType(string $type): Collection
    {
        return SMSNotification::where('type', $type)->get();
    }
}

