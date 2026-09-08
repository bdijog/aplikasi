<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['id', 'en'];
        $locale = session('locale', config('app.locale', 'id'));

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = 'id';
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
