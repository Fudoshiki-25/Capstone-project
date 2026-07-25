<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Redirect to login when unauthenticated
        $middleware->redirectGuestsTo(fn () => route('login'));

        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->alias([
            'role'      => \App\Http\Middleware\EnsureUserHasRole::class,
            'guest.only' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'no.cache'  => \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();