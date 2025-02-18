<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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

    //La siguiente funcíón forza la traducción de los mensajes de acción no autorizada
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthorizationException) {
            abort(403, __('auth.unauthorized')); // Usa la traducción en español
        }

        return parent::render($request, $exception);
    }


}
