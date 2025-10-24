<?php

namespace App\Http\Middleware;

use App\Helpers\ActivityLogHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Ignorar logs de assets, API interna etc (opcional)
        if ($request->is('telescope*') || $request->is('horizon*') || $request->is('livewire/*')) {
            return $response;
        }

        ActivityLogHelper::add();

        return $response;
    }
}
