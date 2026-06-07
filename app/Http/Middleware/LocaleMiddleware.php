<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = 'en';

        if ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        } elseif ($request->user() && $request->user()->locale) {
            $locale = $request->user()->locale;
        }

        if (in_array($locale, ['en', 'ar'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
