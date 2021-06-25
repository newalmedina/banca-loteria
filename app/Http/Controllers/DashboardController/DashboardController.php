<?php

namespace App\Http\Controllers\DashboardController;

use Carbon\Carbon;
use App\Models\StatUser;
use App\Models\UserConfig;
use Illuminate\Http\Request;
use App\Models\StatUserRoute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DashboardController extends AdminController
{
    public function index()
    {
        if (isset($_REQUEST["date_ini"])) {
            $date_ini = $_REQUEST["date_ini"];
            $a_date_for_session = explode("/", $date_ini);
            $strDate = $a_date_for_session[2]."-".$a_date_for_session[1]."-".$a_date_for_session[0];
            $date_to_work = new Carbon($strDate);
        } else {
            $date_to_work = Carbon::today();
            $date_to_work = $date_to_work->addDays(-3);
            $date_ini = $date_to_work->format("d/m/Y");
        }

        $page_title = trans("dashboard/admin_lang.Dashboard");
        $page_description = trans("dashboard/admin_lang.estadisticas_info");

        $seconds = 3600;
        $a_contadores = Cache::remember(
            'a_contadores_'.$date_to_work->format('Ymd'),
            $seconds,
            function () use ($date_to_work) {
                $a_contadores = [];
                $a_contadores["today"] = StatUser::distinct("ipuser")
                ->where("dateaccess", "=", Carbon::today())->get()->count();
                $a_contadores["anything"] = StatUser::distinct("ipuser")
                ->where("dateaccess", ">=", $date_to_work)->get()->count();
                $a_contadores["moreday"] = StatUser::select("ipuser")
                ->where("dateaccess", ">=", $date_to_work)
                ->groupBy("ipuser")
                ->havingRaw("count(ipuser)>1")
                ->get()
                ->count();
                $a_contadores["onlyday"] = StatUser::select("ipuser")
                ->where("dateaccess", ">=", $date_to_work)
                ->groupBy("ipuser")
                ->havingRaw("count(ipuser)=1")
                ->get()
                ->count();
                $a_contadores["registers"] = $this->porcentajeRegistrados(
                    '>=',
                    $a_contadores["anything"],
                    $date_to_work
                );
                $a_contadores["anonimus"] = $this->porcentajeRegistrados(
                    '<',
                    $a_contadores["anything"],
                    $date_to_work
                );
                $a_contadores["total_semana"] = $this->visitasUltimaSemana();
                $a_contadores["mapa"] = $this->obtenInfoMapa($date_to_work);
                $a_contadores["mapa_country"] = $this->obtenInfoMapaCountry($date_to_work);
                $a_contadores["browser"] = $this->getBrowsers($date_to_work);
                $a_contadores["routes"] = $this->getRoutes($date_to_work);

                return $a_contadores;
            }
        );

        return view(
            'modules.home.admin_index',
            compact(
                'page_title',
                'page_description',
                'a_contadores',
                'date_ini'
            )
        );
    }

    private function porcentajeRegistrados($operator, $totalUsurios, $date_to_work)
    {
        $total_login = StatUser::select("ipuser")
            ->where("dateaccess", ">=", $date_to_work)
            ->groupBy("ipuser")
            ->havingRaw("SUM(is_login) ".$operator."1")
            ->get()
            ->count();
        if ($totalUsurios>0) {
            return round(($total_login * 100) / $totalUsurios);
        } else {
            return 0;
        }
    }

    private function visitasUltimaSemana()
    {
        $a_visitas = [];
        $date_ini = Carbon::today();
        $date_ini = $date_ini->addDays(-6);

        for ($j=0; $j<=6; $j++) {
            // Obtengo el total para el día
            $a_visitas["total"][$j] =
                StatUser::select("ipuser")
                    ->distinct("ipuser")
                    ->where("dateaccess", "=", new Carbon($date_ini))
                    ->groupBy("ipuser")->get()->count();
            // Obtengo el total para el día de logins
            $a_visitas["registrados"][$j] =
                StatUser::select("ipuser")
                    ->distinct("ipuser")
                    ->where("dateaccess", "=", new Carbon($date_ini))
                    ->groupBy("ipuser")
                    ->havingRaw("SUM(is_login) >= 1")
                    ->get()
                    ->count();
            // Obtengo el total para el día de anónimos
            $a_visitas["anonimos"][$j] =
                StatUser::select("ipuser")
                    ->distinct("ipuser")
                    ->where("dateaccess", "=", new Carbon($date_ini))
                    ->groupBy("ipuser")
                    ->havingRaw("SUM(is_login) = 0")
                    ->get()
                    ->count();
            $date_ini->addDay();
        }

        return $a_visitas;
    }

    private function obtenInfoMapa($date_to_work)
    {
        $a_lalgmapa = [];

        $stats = StatUser::select(array(
            'longitude',
            'latitude',
            'cityname',
            'countryname',
            DB::raw("count(ipuser) AS total")
        ))
            ->where("dateaccess", ">=", $date_to_work)
            ->groupBy('longitude', 'latitude', 'cityname', 'countryname')
            ->orderBy('total', 'ASC')
            ->get();

        $j = 0;
        foreach ($stats as $value) {
            $a_lalgmapa[$j]["lon"] = $value->longitude;
            $a_lalgmapa[$j]["lat"] = $value->latitude;
            $a_lalgmapa[$j]["name"] = $value->cityname." (".$value->countryname.")";
            $a_lalgmapa[$j]["total"] = $value->total;
            $j++;
        }

        return $a_lalgmapa;
    }

    private function obtenInfoMapaCountry($date_to_work)
    {
        $a_lalgmapa = [];

        $stats = StatUser::select(array(
            'countryname',
            DB::raw("count(ipuser) AS total")
        ))
            ->where("dateaccess", ">=", $date_to_work)
            ->groupBy('countryname')
            ->get();

        $j = 0;
        foreach ($stats as $value) {
            $a_lalgmapa[$j]["name"] = $value->countryname;
            $a_lalgmapa[$j]["total"] = $value->total;
            $j++;
        }

        return $a_lalgmapa;
    }

    private function getBrowsers($date_to_work)
    {
        $a_browser = [];
        $a_colors = array("#f56954", "#00a65a", "#f39c12", "#00c0ef", "#3c8dbc", "#d2d6de");

        $stats = StatUser::select(array(
            'browser',
            'is_mobile',
            DB::raw("count(id) as total")
        ))
            ->where("dateaccess", ">=", $date_to_work)
            ->groupBy("browser", 'is_mobile')
            ->get();

        $j = 0;
        foreach ($stats as $value) {
            $a_browser[$j]["name"] = (is_null($value->browser)) ? "Navigator" : $value->browser;
            $a_browser[$j]["total"] = $value->total;
            $a_browser[$j]["is_mobile"] = $value->is_mobile;
            $a_browser[$j]["color"] = $a_colors[$j%6];
            $j++;
        }

        return $a_browser;
    }

    private function getRoutes($date_to_work)
    {
        $a_routes = [];

        $routes = StatUserRoute::select(array(
            'route',
            'titulo',
            DB::raw("count(*) as totalclicks")
        ))
            ->where("dateaccess", ">=", $date_to_work)
            ->groupBy("route", 'titulo')
            ->orderBy("totalclicks", "DESC")
            ->get();

        foreach ($routes as $value) {
            $data = [];
            $data["titulo"] = $value->titulo;
            $data["route"] = $value->route;
            $data["clicks"] = $value->totalclicks;

            // Compruebo que la ruta siga activa
            $routes_check = Route::getRoutes();
            $request = Request::create($value->route);
            try {
                $routes_check->match($request);
                $data["status"] = 1;
            } catch (NotFoundHttpException $e) {
                $data["status"] = 0;
            }

            $data["anteriores"] = $this->getSevenDays($value->route);

            $a_routes[] = $data;
        }

        return $a_routes;
    }

    private function getSevenDays($url)
    {
        $a_sevendays = [];
        $date_ini = Carbon::today();
        $date_ini = $date_ini->addDays(-6);

        for ($j=0; $j<=6; $j++) {
            // Obtengo el total para el día
            $a_sevendays[$j] = StatUserRoute::select('ipuser')
                ->distinct('ipuser')
                ->where("dateaccess", "=", new Carbon($date_ini))
                ->where("route", "=", $url)
                ->groupBy("ipuser")
                ->get()
                ->count();
            $date_ini->addDay();
        }

        return $a_sevendays;
    }

    public function saveState()
    {
        // Verificamos que sea usuario
        if (!auth()->guest()) {
            $config = UserConfig::where("user_id", auth()->user()->id)->first();
            if (empty($config)) {
                $config = new UserConfig();
                $config->user_id = auth()->user()->id;
                $config->sidebar = false;
            }
            $config->sidebar = !$config->sidebar;
            $config->save();
        }
    }

    public function changeSkin(\Illuminate\Http\Request $request)
    {
        $skin = $request->get("skin", 'skin-blue');
        // Verificamos que sea usuario
        if (!auth()->guest()) {
            $config = UserConfig::where("user_id", auth()->user()->id)->first();
            if (empty($config)) {
                $config = new UserConfig();
                $config->user_id = auth()->user()->id;
            }
            $config->skin = $skin;
            $config->save();
        }
    }
}
