<?php

use App\Http\Middleware\EnsureWishSecret;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'wish.secret' => EnsureWishSecret::class,
        ]);

        // Donation tools call these from outside the browser. The shared
        // secret in EnsureWishSecret is the credential, not the CSRF cookie.
        $middleware->preventRequestForgery(except: [
            'santa/wishes',
            'santa/wishes/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('santa/*') || $request->expectsJson(),
        );
    })->create();
