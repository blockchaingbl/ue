<?php

namespace App\Http\Middleware;

use App\FanweErrcode;
use Closure;
use Laravel\Lumen\Http\Middleware\VerifyCsrfToken;
use Symfony\Component\HttpFoundation\Cookie;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\Security\Core\Util\StringUtils;

class FanwePageCsrfToken extends VerifyCsrfToken
{

    public function handle($request, Closure $next)
    {
        if ($this->isReading($request) || $this->tokensMatch($request)) {
            return $this->addCookieToResponse($request, $next($request));
        }

        return FanweErrcode::SYSTEM_CSRF_TOKEN_MISMATCH;
    }
}
