<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AlertHelper;

class DenyRolesMiddleware
{
    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$deniedRoles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$deniedRoles)
    {
        if (!Auth::check()) {
            return redirect('/login')->with(AlertHelper::error('You must be logged in to access this page.', 'Authentication Required'));
        }

        $userRole = Auth::user()->role;

        if (in_array($userRole, $deniedRoles)) {
            return redirect('/login')->with(AlertHelper::error('Access denied for role: ' . $userRole, 'Access Forbidden'));
        }

        return $next($request);
    }
}