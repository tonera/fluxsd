<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    // ->withBroadcasting(
    //     __DIR__.'/../routes/channels.php',
    //     ['prefix' => 'api', 'middleware' => ['auth:sanctum']],
    // )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\Localization::class, //i18n
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
        $middleware->api(append: [
            \App\Http\Middleware\Localization::class, //i18n
        ]);
        $middleware->statefulApi();

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
