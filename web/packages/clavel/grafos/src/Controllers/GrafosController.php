<?php

namespace Clavel\Grafos\Controllers;

use App\Http\Controllers\AdminController;
use Clavel\Grafos\Models\Module;
use Clavel\Grafos\Models\ModuleField;
use Clavel\Grafos\Requests\ModuleRequest;
use Clavel\Grafos\Services\Grafos;
use Clavel\Grafos\Services\GraphHopperService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class GrafosController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa-connectdevelop" aria-hidden="true"></i>';

    protected const MAPS_API_KEY = 'AIzaSyAbaZEo6E3tLr-eGbndsLgqiJHHm3NrZyU';

    protected const GRAPHHOPPER_KEY = '7be31ab8-6300-4bef-8aca-b4dc02dd66d3';

    protected const MAPBOX_API = 'pk.eyJ1IjoiYWR1eGlhIiwiYSI6ImNrZDJ6dWlwazBiaGgyeG55bjc1aGdmNGQifQ.RXK9_KuUPGES9ZATF6KXWw';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-grafos';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-grafos-list')) {
            app()->abort(403);
        }

        $page_title = trans("grafos::modules/admin_lang.title");

        return view("grafos::modules.admin_index", compact(
            'page_title'
        ))
        ->with('page_title_icon', $this->page_title_icon);
    }

    public function indexJS()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-grafos-list')) {
            app()->abort(403);
        }

        $page_title = trans("grafos::modules/admin_lang.title");

        return view("grafos::modules.admin_index_js", compact(
            'page_title'
        ))
        ->with('page_title_icon', $this->page_title_icon)
        ->with('graphhopper_api_key', self::GRAPHHOPPER_KEY);
    }

    public function indexLatLong()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-grafos-list')) {
            app()->abort(403);
        }

        $page_title = trans("grafos::modules/admin_lang.title");

        return view("grafos::modules.admin_index_latlong", compact(
                'page_title'
            )
        )
        ->with('page_title_icon', $this->page_title_icon)
        ->with('maps_api_key', self::MAPS_API_KEY);
    }

    public function indexLF()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-grafos-list')) {
            app()->abort(403);
        }

        $page_title = trans("grafos::modules/admin_lang.title");

        $gh = new GraphHopperService(self::GRAPHHOPPER_KEY);
        $response = $gh->RoutingAPI();

        $rutas = [];

        if ($response[1] == 200) {
            $rutas = $response[0]['paths'][0];
        }
        //dd($response[0]['paths'][0]['points']['type']);

        return view("grafos::modules.admin_index_lf", compact(
            'page_title',
            'rutas'
        ))
        ->with('page_title_icon', $this->page_title_icon)
        ->with('mapbox_api_key', self::MAPBOX_API)
        ->with('graphhopper_api_key', self::GRAPHHOPPER_KEY);
    }


    public function routingAPI()
    {
        $gh = new GraphHopperService(self::GRAPHHOPPER_KEY);
        dd($gh->RoutingAPI());
    }

    public function routeOptimizationAPIJS()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-grafos-list')) {
            app()->abort(403);
        }

        $page_title = trans("grafos::modules/admin_lang.title");

        return view("grafos::modules.admin_index_js_vrp", compact(
            'page_title'
        ))
        ->with('page_title_icon', $this->page_title_icon)
        ->with('graphhopper_api_key', self::GRAPHHOPPER_KEY);
    }

    public function routeOptimizationAPIPHP()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-grafos-list')) {
            app()->abort(403);
        }

        $page_title = trans("grafos::modules/admin_lang.title");

        $gh = new GraphHopperService(self::GRAPHHOPPER_KEY);
        $rutas = $gh->RouteOptimizationAPI();
        dd($rutas);

        return view("grafos::modules.admin_index_php_vrp", compact(
            'page_title'
        ))
        ->with('page_title_icon', $this->page_title_icon)
        ->with('graphhopper_api_key', self::GRAPHHOPPER_KEY);
    }

    public function geocodingJSAPI()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-grafos-list')) {
            app()->abort(403);
        }

        $page_title = trans("grafos::modules/admin_lang.title");

        return view("grafos::modules.admin_index_js_gc", compact(
            'page_title'
        ))
        ->with('page_title_icon', $this->page_title_icon)
        ->with('graphhopper_api_key', self::GRAPHHOPPER_KEY);
    }

    public function isochroneJSAPI()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-grafos-list')) {
            app()->abort(403);
        }

        $page_title = trans("grafos::modules/admin_lang.title");

        return view("grafos::modules.admin_index_js_is", compact(
            'page_title'
        ))
        ->with('page_title_icon', $this->page_title_icon)
        ->with('graphhopper_api_key', self::GRAPHHOPPER_KEY);
    }





    public function routeOptimizationAPI()
    {
        $gh = new GraphHopperService(self::GRAPHHOPPER_KEY);
        $gh->RouteOptimizationAPI();
    }




    public function indexGoogle()
    {
        // Si no tiene permisos para ver el listado lo echa.
        if (!auth()->user()->can('admin-grafos-list')) {
            app()->abort(403);
        }

        $page_title = trans("grafos::modules/admin_lang.title");

        return view("grafos::modules.admin_index_google", compact(
            'page_title'
        ))
            ->with('page_title_icon', $this->page_title_icon)
            ->with('maps_api_key', self::MAPS_API_KEY);
    }






    public function matrixAPI()
    {
        $gh = new GraphHopperService(self::GRAPHHOPPER_KEY);
        $gh->MatrixAPI();
    }

    public function geocodingAPI()
    {
        $gh = new GraphHopperService(self::GRAPHHOPPER_KEY);
        $gh->GeocodingAPI();
    }

    public function isochroneAPI()
    {
        $gh = new GraphHopperService(self::GRAPHHOPPER_KEY);
        $gh->IsochroneAPI();
    }

    public function clusterAPI()
    {
        $gh = new GraphHopperService(self::GRAPHHOPPER_KEY);
        $gh->ClusterAPI();
    }
}
