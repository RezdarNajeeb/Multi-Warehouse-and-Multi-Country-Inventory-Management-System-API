<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
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
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
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
        });
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('inventory:check-low-stock')->dailyAt('00:00');
    })
    ->create();
