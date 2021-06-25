<?php

namespace App\Http\Controllers\Language;

use App\Models\Idioma;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    public function switchLang($lang)
    {
        $active_languages = Idioma::select('code')->active()->pluck('code')->toArray();
        if (in_array($lang, $active_languages)) {
            app()->setLocale($lang);
            Session::put('lang', $lang);
            Cookie::queue('lang', $lang, time() + (365 * 24 * 60 * 60)); // 1 Año

            // Finalmente si estamos autenticados, cambiamos el idioma del usuario en su perfil.
            if (!Auth::guest()) {
                $user_profile = Auth::user()->userProfile;
                $user_profile->user_lang = $lang;
                $user_profile->save();
            }
        }
        return Redirect::back();
    }
}
