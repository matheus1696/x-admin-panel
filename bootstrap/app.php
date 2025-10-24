<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\ActivityLogger::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Captura "403 | Ação não autorizada"
        $exceptions->render(function (Throwable $e, Illuminate\Http\Request $request) {
            // Caso seja uma exceção de autorização (Gate / Policy)
            if ($e instanceof AuthorizationException) {
                return redirect()
                    ->route('dashboard')
                    ->with('error', 'Usuário sem acesso para esta ação.');
            }

            // Caso seja uma exceção genérica HTTP 403
            if ($e instanceof HttpExceptionInterface && $e->getStatusCode() === 403) {
                return redirect()
                    ->route('dashboard')
                    ->with('error', 'Usuário sem acesso para esta página.');
            }

            return null; // deixa o Laravel cuidar do resto
        });
    })->create();
