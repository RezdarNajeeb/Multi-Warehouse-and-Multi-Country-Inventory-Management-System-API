<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Validation\ValidationException;
use Predis\Connection\Resource\Exception\StreamInitException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            // handle not found exceptions
            if ($e instanceof NotFoundHttpException) {
                // Check if it's a model not found exception
                if ($e->getPrevious() instanceof ModelNotFoundException) {
                    $model = class_basename($e->getPrevious()->getModel());
                    $message = "{$model} not found.";
                } else {
                    $message = 'Resource not found.';
                }

                return response()->json([
                    'message' => $message
                ], 404);
            }

            // handle validation exceptions
            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $e->validator->errors(),
                ], 422);
            }

            // handle unauthenticated requests (authentication failed)
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 401);
            }

            // handle redis connection issues
            if ($e instanceof StreamInitException) {
                return response()->json([
                    'message' => 'Redis server is not running or misconfigured.',
                ], 503);
            }

            // handle database query exceptions
//            if ($e instanceof QueryException) {
//                return response()->json([
//                    'message' => 'Database error occurred.',
//                ], 500);
//            }

            // handle other exceptions, show the exception message if not in production
            return response()->json([
                'message' => app()->environment('production')
                    ? 'An unexpected error occurred.'
                    : $e->getMessage(),
            ], 500);
        });
    })
    ->withSchedule(function (Schedule $schedule): void {
        // this uses schedule_timezone in config/app.php
        $schedule->command('inventory:check-low-stock')->dailyAt('00:00');
    })
    ->create();
