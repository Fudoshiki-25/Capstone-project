<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackLastSeen
{
    /**
     * Refreshes the authenticated admin/superadmin's last_seen_at, powering
     * the "online now" indicator on the superadmin's Admin Accounts table.
     * Throttled to once a minute per user (via a plain timestamp comparison,
     * not a cache store) so this doesn't add a write to every single request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if ($user && (! $user->last_seen_at || $user->last_seen_at->lt(now()->subMinute()))) {
            $user->timestamps = false;
            $user->update(['last_seen_at' => now()]);
        }

        return $next($request);
    }
}
