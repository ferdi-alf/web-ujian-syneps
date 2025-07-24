<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AlertHelper;

class DenyRolesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$deniedRoles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$deniedRoles)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect('/')->with(AlertHelper::error('You must be logged in to access this page.', 'Authentication Required'));
        }

        // Get user role
        $userRole = Auth::user()->role;

        // Check if user's role is in denied roles
        if (in_array($userRole, $deniedRoles)) {
            return redirect('/')->with(AlertHelper::error('Access denied for role: ' . $userRole, 'Access Forbidden'));
        }

        return $next($request);
    }
}