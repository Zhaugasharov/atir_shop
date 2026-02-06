<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class AjaxLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Для AJAX запросов проверяем язык в заголовке
        if ($request->ajax() || $request->wantsJson()) {
            $locale = $this->getLocaleFromRequest($request);
            App::setLocale($locale);
            Session::put('locale', $locale);
        }

        return $next($request);
    }

    private function getLocaleFromRequest(Request $request): string
    {
        // 1. Из заголовка X-Locale
        if ($request->header('X-Locale')) {
            $locale = $request->header('X-Locale');
            if (in_array($locale, ['en', 'ru', 'kk'])) {
                return $locale;
            }
        }

        // 2. Из заголовка Accept-Language
        if ($request->header('Accept-Language')) {
            $locale = substr($request->header('Accept-Language'), 0, 2);
            if (in_array($locale, ['en', 'ru', 'kk'])) {
                return $locale;
            }
        }

        // 3. Из сессии
        if (Session::has('locale')) {
            return Session::get('locale');
        }

        // 4. По умолчанию
        return config('app.locale', 'kk');
    }
}
