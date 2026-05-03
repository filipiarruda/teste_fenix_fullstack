<?php

namespace App\Http\Middleware;

use Closure;

class TrustProxies
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
