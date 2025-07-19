<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the user is already authenticated, redirect them to the intended route
        if ($request->user()) {
            // Redirect to the intended route or a default route
            return redirect()->intended('dashboard');
        }
        return $next($request);
    }
}
