<?php

// app/Http/Controllers/LocaleController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function switch($locale)
    {
        /*if (!in_array($locale, config('app.available_locales', ['en', 'ru', 'kk']))) {
            $locale = config('app.fallback_locale', 'kk');
        }*/

        Session::put('locale', $locale);
        App::setLocale($locale);

        return redirect()->back();
    }
}
