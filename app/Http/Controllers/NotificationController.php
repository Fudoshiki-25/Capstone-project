<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /admin/notifications
     * Latest 20 notifications for the bell dropdown, plus the unread count
     * for the badge. Uses Laravel's built-in database notifications table
     * (Notifiable trait on User already provides notifications()/
     * unreadNotifications()).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = $user->notifications()->latest()->take(20)->get();

        return response()->json([
            'notifications' => $notifications->map(fn ($n) => [
                'id'         => $n->id,
                'data'       => $n->data,
                'read'       => $n->read_at !== null,
                'created_at' => $n->created_at->diffForHumans(),
            ]),
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * POST /admin/notifications/{notification}/read
     * Marks a single notification read (called when the admin clicks it).
     */
    public function markRead(Request $request, string $notification)
    {
        $record = $request->user()->notifications()->findOrFail($notification);
        $record->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * POST /admin/notifications/read-all
     * "Mark all as read" — clears the badge without opening each one.
     */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}