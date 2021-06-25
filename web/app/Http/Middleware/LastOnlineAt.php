<?php

namespace App\Http\Middleware;

use App\Models\OnlineSession;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LastOnlineAt
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Si es usuario registrado borramos sus sesiones anteriores para saber si esta online
        if (auth()->check()) {
            OnlineSession::where('user_id', auth()->user()->id)
                ->where('id', '<>', session()->getId())
                ->delete();
        }

        // Guardamos la ultima sesion de cualquier usuario
        OnlineSession::updateOrCreate(
            ['id' => session()->getId()],
            [
                'last_online_at' => Carbon::now(),
                'user_id' => auth()->check() ? auth()->user()->id : null
            ]
        );

        // Si es invitado no hacemos nada (De momento)
        if (auth()->guest()) {
            return $next($request);
        }

        // Si es usuario registrado guardamos tambien su ultima entrada en la tabla usuario.
        try {
            if (auth()->check() &&
                auth()->user()->last_online_at < Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s')) {
                $user = auth()->user();
                $user->last_online_at = Carbon::now();
                $user->timestamps = false;
                $user->save();
            }
        } catch (\Exception $ex) {
        }


        return $next($request);
    }
}
