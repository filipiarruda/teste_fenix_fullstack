<?php

namespace App\Http\Middleware;

use Closure;

class TrimStrings
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
