<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'throttle' => \App\Http\Middleware\CustomThrottleRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (Throwable $throwable) {
            $statusCode = 500;
            $message = 'Internal Server Error';
            $errors = [];
            $data = [];

            if ($throwable instanceof HttpException) {
                $statusCode = $throwable->getStatusCode();
                $message = $throwable->getMessage() ?: 'Not Found';
            }

            if ($throwable instanceof ThrottleRequestsException || $throwable instanceof TooManyRequestsHttpException) {
                $statusCode = 429;
                $message = 'Too Many Requests';
                $data = $throwable->getHeaders();
            }

            if ($throwable instanceof AuthenticationException) {
                $statusCode = 401;
                $message = $throwable->getMessage() ?: 'Unauthenticated';
            }

            if ($throwable instanceof ValidationException) {
                $statusCode = 422;
                $message = 'Validation Error';
                $errors = $throwable->errors(); // Get validation errors
            }

            if ($throwable instanceof AccessDeniedHttpException) {
                $statusCode = 403;
                $message = $throwable->getMessage() ?: 'Access Denied';
            }

            $response = [
                'message' => $message,
                'code' => $statusCode,
                'errors' => $errors,
                'data' => $data,
            ];

            Log::channel('api')->error(json_encode(
                array_merge($response, ['instance' => get_class($throwable)])
            ));

            return response()->json($response, $statusCode);
        });
    })->create();
