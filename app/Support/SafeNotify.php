<?php

namespace App\Support;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Throwable;

class SafeNotify
{
    /**
     * Send a notification without letting a failure (missing mail config,
     * an unreachable push service, etc.) break the request that triggered
     * it. The state change already happened by the time this runs — a
     * notification is a side effect, not the source of truth for whether
     * the actual action succeeded.
     */
    public static function to(mixed $notifiable, Notification $notification): void
    {
        try {
            NotificationFacade::send($notifiable, $notification);
        } catch (Throwable $e) {
            Log::error('Notification failed to send: ' . get_class($notification), [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
