<?php

// bootstrap/app.php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // ← Important
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Le groupe 'api' ne doit PAS inclure VerifyCsrfToken
        $middleware->api(prepend: [
            // \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class, // ← À SUPPRIMER si présent
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();