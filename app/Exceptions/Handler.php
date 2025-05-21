<?php

namespace App\Exceptions;

use App\Http\Utils\GlobalException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {

        // If the request expects JSON (API request)
        if ($request->expectsJson()) {
            // Handle validation exceptions
            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => 'Doğrulama hatası',
                    'errors' => $e->errors(),
                ], 422);
            }

            // Handle authentication exceptions
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'message' => 'Kimlik doğrulama hatası',
                ], 401);
            }

            // Handle HTTP exceptions
            if ($e instanceof HttpException) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'HTTP hatası',
                ], $e->getStatusCode());
            }

            // Handle GlobalException
            if ($e instanceof GlobalException) {
                return $e->render($request);
            }

            // Handle all other exceptions
            return response()->json([
                'message' => 'Beklenmedik bir hata oluştu',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        return parent::render($request, $e);
    }
}
