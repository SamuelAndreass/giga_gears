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
        //
        $middleware->alias([
            'redirect.seller' => App\Http\Middleware\RedirectIfSeller::class,
            'ensure.seller' => App\Http\Middleware\EnsureIfSeller::class,
            'ensure.admin' => App\Http\Middleware\EnsureUserIsAdmin::class,
            'ensure.active' => \App\Http\Middleware\EnsureUserActive::class,
            'store.active' => \App\Http\Middleware\EnsureStoreIsActive::class,
        ]);

 
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
