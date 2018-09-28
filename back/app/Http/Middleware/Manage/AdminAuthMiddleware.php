<?php

namespace App\Http\Middleware\Manage;

use Illuminate\Auth\SessionGuard;
use Closure;
use Illuminate\Support\Facades\Auth;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Auth::check()) {
            return redirect(url("login")) ;
        }
        return $next($request);
    }
}
