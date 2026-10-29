<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDemoMode
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.demo_mode', false)) {
            abort(403, 'Demo authentication features are disabled in production mode (DEMO_MODE=false).');
        }

        return $next($request);
    }
}

