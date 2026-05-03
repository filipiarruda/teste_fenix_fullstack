<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    protected $except = ['*'];

    public function handle($request, Closure $next)
    {
        \Log::info('CSRF middleware hit - bypassing');
        return $next($request);
    }
}
