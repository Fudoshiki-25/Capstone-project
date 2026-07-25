<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Blocks an already-logged-in user from reaching guest-only pages
     * (login, register, admission, forgot-password) — they must log out
     * first. Sends them straight to whichever dashboard matches their guard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('parent')->check()) {
            return redirect('/parent');
        }

        if (Auth::guard('web')->check()) {
            return redirect('/admin');
        }

        return $next($request);
    }
}
