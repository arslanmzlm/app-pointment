<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    public function get(User|int $user): Collection
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        return Notification::query()
            ->where('user_id', $user)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function seen(User|int $user): int
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        return Notification::query()
            ->where('user_id', $user)
            ->whereNull('seen_at')
            ->update(['seen' => now()]);
    }

    public function deleteAll(User|int $user): int
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        return Notification::query()
            ->where('user_id', $user)
            ->delete();
    }
}
