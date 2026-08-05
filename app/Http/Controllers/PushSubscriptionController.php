<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushSubscriptionController extends Controller
{
    /**
     * POST /push/subscribe
     * Stores (or refreshes) the browser's push subscription for the
     * currently-logged-in parent, so EnrollmentSubmitted/Approved,
     * DocumentNeedsResubmit, and NewAnnouncementPosted can reach them.
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint'      => 'required|string',
            'keys.p256dh'   => 'required|string',
            'keys.auth'     => 'required|string',
        ]);

        $parent = Auth::guard('parent')->user();

        $parent->updatePushSubscription(
            $validated['endpoint'],
            $validated['keys']['p256dh'],
            $validated['keys']['auth'],
            'aes128gcm'
        );

        return response()->json(['success' => true]);
    }

    /**
     * POST /push/unsubscribe
     * Called when the parent turns notifications back off in this browser.
     */
    public function unsubscribe(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'required|string',
        ]);

        Auth::guard('parent')->user()->deletePushSubscription($validated['endpoint']);

        return response()->json(['success' => true]);
    }
}
