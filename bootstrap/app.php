<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Self-healing directory & permission check for Hostinger / Shared Hosting deployment
$requiredDirectories = [
    __DIR__ . '/cache',
    dirname(__DIR__) . '/storage',
    dirname(__DIR__) . '/storage/app',
    dirname(__DIR__) . '/storage/framework',
    dirname(__DIR__) . '/storage/framework/cache',
    dirname(__DIR__) . '/storage/framework/cache/data',
    dirname(__DIR__) . '/storage/framework/sessions',
    dirname(__DIR__) . '/storage/framework/views',
    dirname(__DIR__) . '/storage/logs',
];

foreach ($requiredDirectories as $dir) {
    if (!file_exists($dir)) {
        @mkdir($dir, 0777, true);
    }
    if (file_exists($dir) && !is_writable($dir)) {
        @chmod($dir, 0777);
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
