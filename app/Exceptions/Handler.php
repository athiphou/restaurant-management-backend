<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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

        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof ValidationException) {
                    return null;
                }

                $status = 500;

                if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                    $status = 404;
                } elseif (method_exists($e, 'getStatusCode') && $e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                    $status = $e->getStatusCode();
                }

                $message = $e->getMessage();
                if (! $message || ($status >= 500 && ! config('app.debug'))) {
                    $message = 'Server Error';
                }

                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], $status);
            }

            return null;
        });
    }

    /**
     * Convert an authentication exception into a JSON response for the API.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        return redirect()->guest($exception->redirectTo() ?? route('login'));
    }
}