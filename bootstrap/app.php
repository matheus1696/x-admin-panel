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
    ->withMiddleware(function (Middleware $middleware): void {})
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($e instanceof AuthorizationException) {
                return redirect()
                    ->route('dashboard')
                    ->with('error', 'Usuario sem acesso para esta acao.');
            }

            if ($e instanceof HttpExceptionInterface && $e->getStatusCode() === 403) {
                return redirect()
                    ->route('dashboard')
                    ->with('error', 'Usuario sem acesso para esta pagina.');
            }

            return null;
        });
    })->create();
