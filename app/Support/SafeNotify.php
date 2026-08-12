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
            // Logging itself can fail too (permissions, disk full, etc.) —
            // swallow that as well, since the whole point of this method is
            // that nothing here is ever allowed to reach the caller.
            try {
                Log::error('Notification failed to send: ' . get_class($notification), [
                    'error' => $e->getMessage(),
                ]);
            } catch (Throwable) {
                // Nothing more we can do — deliberately silent.
            }
        }
    }
}
