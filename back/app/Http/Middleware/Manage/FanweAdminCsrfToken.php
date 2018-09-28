<?php

namespace App\Http\Middleware\Manage;

use App\FanweErrcode;
use Closure;
use Laravel\Lumen\Http\Middleware\VerifyCsrfToken;


class FanweAdminCsrfToken extends VerifyCsrfToken
{

    public function handle($request, Closure $next)
    {
        if ($this->isReading($request) || $this->tokensMatch($request)) {
            return $this->addCookieToResponse($request, $next($request));
        }

        return redirect(url("login")) ;
    }
}
