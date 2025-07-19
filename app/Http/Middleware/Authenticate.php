<?php

namespace App\Http\Middleware;

use App\Helpers\AlertHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !Auth::guard('web')->check()) {
            return redirect('/')->with(AlertHelper::error('You must be logged in to access this page.', 'Authentication Required'));
        }
        return $next($request);
    }
}
