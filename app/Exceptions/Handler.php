<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function unauthenticated($request, AuthenticationException $exception)
{
    // Check if the request expects a JSON response (for API requests)
    if ($request->expectsJson()) {

        // Check if the exception is caused by missing token or expired token
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => 'You are not authenticated or your token has expired.',
                'error' => 'Unauthenticated'
            ], 401);
        }

        // Fallback for other cases like MissingAbilityException
        if ($exception instanceof MissingAbilityException) {
            return response()->json([
                'message' => 'You do not have permission to access this resource.',
                'error' => 'Forbidden'
            ], 403);
        }

        // Return default unauthenticated message for any other cases
        return response()->json([
            'message' => 'You are not authenticated.',
            'error' => 'Unauthenticated'
        ], 401);
    }

    // Fallback if the request doesn't expect JSON (for web routes)
    return redirect()->guest(route('login'));
}
}
