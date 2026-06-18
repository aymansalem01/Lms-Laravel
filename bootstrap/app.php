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
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'student.view' => \App\Http\Middleware\StudentViewMiddleware::class,
        ]);

        $middleware->appendToGroup('web', \App\Http\Middleware\StudentViewMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\LocaleMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\PreventBackHistory::class);
    })
    ->withSingletons([
        App\Services\Plagiarism\FingerprintService::class,
        App\Services\Plagiarism\AIDetectionService::class,
        App\Services\Plagiarism\PlagiarismService::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
