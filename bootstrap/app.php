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
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ Register global middleware
        $middleware->append([
            \App\Http\Middleware\XSSProtection::class,
        ]);

        // ✅ Register web middleware group
        $middleware->web(append: [
            \App\Http\Middleware\PasswordPolicy::class,
        ]);

        // ✅ Register alias middleware
        $middleware->alias([
            'xss.protection' => \App\Http\Middleware\XSSProtection::class,
            'password.policy' => \App\Http\Middleware\PasswordPolicy::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ✅ Register custom exception handling
        $exceptions->renderable(function (\App\Exceptions\CustomException $e) {
            return response()->view('errors.custom', [
                'message' => $e->getUserMessage(),
                'errorCode' => $e->getErrorCode()
            ], $e->getErrorCode());
        });

        $exceptions->renderable(function (\App\Exceptions\ReportException $e) {
            return response()->view('errors.custom', [
                'message' => $e->getUserMessage(),
                'errorCode' => $e->getErrorCode()
            ], $e->getErrorCode());
        });
    })
    ->create();