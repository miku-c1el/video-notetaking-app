<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

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
    * エラーハンドリングのカスタマイズ
    */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthorizationException) {
            return Inertia::render('Errors/Forbidden')->toResponse($request)->setStatusCode(403);
        }

        if ($exception instanceof NotFoundHttpException || $exception instanceof ModelNotFoundException) {
            return Inertia::render('Errors/NotFound')->toResponse($request)->setStatusCode(404);
        }

        if ($exception instanceof AuthenticationException) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid credentials.']);
        }

        if ($exception instanceof ValidationException) {
            return back()->withErrors($exception->errors())->withInput();
        }

        return Inertia::render('Errors/Oops')->toResponse($request)->setStatusCode(500);

        // return parent::render($request, $exception);
    }
}
