<?php

use App\Exceptions\ApiExceptionHandler;
use App\Http\Middleware\TransformApiRequestMiddleware;
use App\Http\Middleware\TransformApiResponseMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

$exceptionHandler = new ApiExceptionHandler;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
        $middleware->appendToGroup('api', [
            TransformApiRequestMiddleware::class,
            TransformApiResponseMiddleware::class,
        ]);
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) use ($exceptionHandler) {
        $exceptions->renderable(function (Throwable $e, Request $request) use ($exceptionHandler) {
            return $exceptionHandler->handleException($e, $request);
        });
    })->create();
