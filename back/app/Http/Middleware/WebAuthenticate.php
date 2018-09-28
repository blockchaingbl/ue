<?php

namespace App\Http\Middleware;

use Illuminate\Auth\SessionGuard;
use Closure;
use Illuminate\Support\Facades\Auth;

class WebAuthenticate
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
            $jumpUrl = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
            $jumpUrl .= $_SERVER['HTTP_HOST'];
            $jumpUrl .= isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : urlencode($_SERVER['PHP_SELF']) . '?' . urlencode($_SERVER['QUERY_STRING']);

            return redirect(url("/")."?jumpUrl=".$jumpUrl) ;
        }
        return $next($request);
    }
}
