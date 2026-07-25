<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Abort with 403 unless the authenticated web-guard user's role is one
     * of the given roles. Used to separate 'admin' from 'superadmin' since
     * both share the same 'web' guard/users table.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::guard('web')->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
