<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;

class AdminMiddleware
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;


    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Si somos invitados
        if ($this->auth->guest()) {
            // Y estamos intentando acceder a administracion => forzamos el login en administracion
            if ($request->is('admin/*') || $request->route()->getPrefix() == "/admin" ||
                substr($request->route()->getPrefix(), 0, 6) === "admin/") {
                return redirect()->guest('admin/login');
            } else {
                return redirect()->guest('login');
            }
        }

        // Si NO tenemos permiso de acceso a administración vamos a front
        if (!$this->auth->user()->can('admin')) {
            if (config("general.only_backoffice", false)) {
                app()->abort(403);
            } else {
                return redirect()->guest('/');
            }
        }

        return $next($request);
    }
}
