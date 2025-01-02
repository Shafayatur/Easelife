<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceProviderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if user is authenticated and is a service provider
        if (Auth::check() && Auth::user()->user_type === 'sp') {
            return $next($request);
        }

        // Redirect to login or home page if not a service provider
        return redirect('/')->with('error', 'Unauthorized access');
    }
}
