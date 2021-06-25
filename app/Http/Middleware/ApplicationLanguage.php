<?php namespace App\Http\Middleware;

use Closure;
use App\Models\Idioma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class ApplicationLanguage
{
    /**
     * Procesa una peticion (Request) de entrada
     * En concreto establece el idioma en función de las preferencias del usuario
     *
     * @param Request $request
     * @param callable $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Establecemos el idioma del usuario segun la variable de sesión establecida durante el login o en
        // el idioma por defecto de la aplicación

        Config::set('app.default_locale', Config::get('app.locale'));

        // Comprobamos la variable de idioma por Session o por Cookie. Como la Cookie tiene mayor persistencia buscamos
        // primero la de session que si esta seteada debe ser más próxima en el tiempo
        $language = Config::get('app.fallback_locale');
        $active_languages = Idioma::select('code')->active()->pluck('code')->toArray();
        if (Session::has('lang') &&
            in_array(Session::get('lang'), $active_languages)) {
            $language = Session::get('lang');
        } elseif (Cookie::has('lang') &&
            in_array(Crypt::decryptString(Cookie::get('lang')), $active_languages)) {
            $language = Crypt::decryptString(Cookie::get('lang'));
        } else {
            $activo_default = Idioma::active()->default()->get();
            $activo = Idioma::active()->get();

            if ($activo_default->count() == 1) {
                $language = $activo_default[0]->code;
            } elseif ($activo->count() == 1) {
                $language = $activo[0]->code;
            }
        }

        // Seteamos el idioma en todas las variables de sistema
        app()->setLocale($language);
        Session::put('lang', $language);
        Cookie::queue('lang', $language, time() + (365 * 24 * 60 * 60)); // 1 Año

        return $next($request);
    }
}
