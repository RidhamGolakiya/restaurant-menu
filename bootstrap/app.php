<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {})
    ->withExceptions(function (Exceptions $exceptions): void {
        Spatie\LaravelFlare\Facades\Flare::handles($exceptions);
        $exceptions->render(function (Throwable $e, $request) {
            // API request handling
            if ($request->expectsJson() || $request->is('api/*')) {
                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ||
                    $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found',
                    ], 404);
                }

                // Other exceptions (optional)
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500);
            }

            // Web request handling
            // if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ||
            //     $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            //     return redirect()->route('custom.404'); // Define this route
            // }

            // Fallback: use default Laravel rendering
            return null;
        });
    })
    ->withSchedule(function (Schedule $schedule) {
        // Add any other scheduled commands here
    })
    ->create();