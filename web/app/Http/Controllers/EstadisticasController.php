<?php

namespace App\Http\Controllers;

use App\Models\StatUser;
use App\Models\StatUserRoute;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class EstadisticasController extends Controller
{
    public function index(Request $request)
    {
        $ipuser     = $request->ip();
        $today      = Carbon::today();
        $agent      = new Agent();

        if (!is_null($ipuser) && !is_null($request->city)) {
            $statUser = StatUser::where("ipuser", "=", $ipuser)
                ->where("dateaccess", "=", $today)
                ->where("cityname", "=", $request->city)
                ->where("countryname", "=", $request->contry_code)
                ->first();

            if (empty($statUser)) {
                $statUser = new StatUser();
            }

            if (is_null($statUser->id)) {
                $statUser->ipuser = $ipuser;
                $statUser->dateaccess = $today;
                $statUser->cityname = $request->city;
                $statUser->countryname = $request->contry_code;
            }

            $statUser->latitude = $request->latitude;
            $statUser->longitude = $request->longitude;
            $statUser->browser = $agent->browser();
            $statUser->is_mobile = $agent->isPhone();
            $statUser->is_login = (Auth::guest()) ? false : true;
            $statUser->save();
        }

        if (!is_null($request->route) && !is_null($request->page_title)) {
            $statUserRoute = StatUserRoute::where("ipuser", "=", $ipuser)
                ->where("dateaccess", "=", $today)
                ->where("route", "=", $request->route)
                ->first();

            if (empty($statUserRoute)) {
                $statUserRoute = new StatUserRoute();
            }

            if (is_null($statUserRoute->id)) {
                $statUserRoute->ipuser = $ipuser;
                $statUserRoute->dateaccess = $today;
                $statUserRoute->route = $request->route;
            }

            $statUserRoute->titulo = $request->page_title;
            $statUserRoute->save();
        }
    }
}
