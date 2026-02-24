<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        $middleware->api(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        $middleware->alias([
            'isAdmin' => \App\Http\Middleware\IsAdmin::class,
            'isCustomer' => \App\Http\Middleware\IsCustomer::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                $status = 500;
                $message = $e->getMessage() ?: 'Server Error';

                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    $status = 422;
                    $message = __('Validation failed.');
                    return response()->json([
                        'error' => true,
                        'message' => $message,
                        'data' => $e->errors(),
                    ], $status);
                }

                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    $status = $e->getStatusCode();
                } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    $status = 401;
                    $message = __('Unauthenticated.');
                } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    $status = 403;
                    $message = __('Forbidden.');
                }

                // Map 408 Timeout if caught
                if ($status == 408) {
                    $message = __('Request timeout.');
                }

                return response()->json([
                    'error' => true,
                    'message' => $message,
                    'data' => null,
                ], $status);
            }
        });
    })->create();
