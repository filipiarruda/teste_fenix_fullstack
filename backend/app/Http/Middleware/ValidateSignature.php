<?php

namespace App\Http\Middleware;

use Closure;

class ValidateSignature
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
