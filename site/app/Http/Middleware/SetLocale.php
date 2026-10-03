<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next, string $locale = 'tr')
    {
        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
