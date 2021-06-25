<?php

namespace Clavel\Elearning\Controllers\Profesor;

use App\Http\Controllers\AdminController;
use App\Models\User;
use Carbon\Carbon;
use Clavel\Elearning\Models\Asignatura;
use Clavel\Elearning\Models\Contenido;
use Clavel\Elearning\Models\Curso;
use Clavel\Elearning\Models\Modulo;
use Clavel\Elearning\Models\RespuestaResultado;
use Clavel\Elearning\Models\TrackAsignatura;
use Clavel\Elearning\Models\TrackContenido;
use Clavel\Elearning\Models\TrackContenidoEvaluacion;
use Clavel\Elearning\Models\TrackModulo;
use Clavel\Elearning\Models\TrackVideo;
use Clavel\Elearning\Services\ProfesorHelper;
use Clavel\Elearning\Services\TrackContent;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\DataTables\DataTables;
use ExcelHelper;

class AdminProfesorController extends AdminController
{
    protected $page_title_icon = '<i class="fa  fa-file-image-o"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-profesor';
    }

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        $cursos_categoria = Curso::active()->get();
        $asignaturas = Asignatura::active()
            ->select('asignaturas.*')
            ->orderBy('orden', 'ASC');

        if (!auth()->user()->can("admin-asignaturas-all")) {
            $asignaturas
                ->join('asignatura_profesor', "asignatura_profesor.asignatura_id", "=", "asignaturas.id")
                ->where("asignatura_profesor.user_id", auth()->user()->id);
        }

        $asignaturas = $asignaturas->paginate(6);


        $page_title = trans('elearning::profesor/admin_lang.zona_profesor');

        if (Auth::user() != null && (!Auth::user()->can('admin-profesor'))) {
            abort(404);
        }

        //SI tenemos una sola asignatura redirigmos directamente a ella.
        if (!config("elearning.cursos.mostrar_asignaturas") && $asignaturas->count() == 1) {
            return Redirect::to('admin/profesor/detalle/asignatura/' . $asignaturas[0]->id);
        }

        return view("elearning::profesor.admin_listado", compact('page_title', 'cursos_categoria', 'asignaturas'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function detalle(Request $request)
    {
        $asignatura = Asignatura::active()->findorFail($request->id);
        if (empty($asignatura)) {
            abort(404);
        }
        $page_title = $asignatura->titulo;

        //Sacamos el tracking de esta asignatura
        //TODO filtrar datos de curso??
        $stats_asignatura = $asignatura->getStats();

        $a_tracking = $this->reloadStatsBase($request, $stats_asignatura);

        $convocatorias = $asignatura->convocatorias()->paginate(4);

        return view(
            "elearning::profesor.admin_detalle",
            compact('page_title', 'a_tracking', 'asignatura', 'stats_asignatura', 'convocatorias')
        );
    }

    public function reloadStatsBase(Request $request, $stats)
    {
        // Preparamos la informacion de la asignatura
        $a_tracking = array(
            array(
                "total" => $stats["pendientes"],
                "state" => 0, "color" => "#f39c12",
                "name" => trans("elearning::profesor/admin_lang.pendientes"),
                "bg" => "bg-yellow", "fa" => "fa-clock-o",
                "porcentaje" => (($stats["pendientes"] * 100) / $stats["total_usuarios"])
            ),
            array(
                "total" => $stats["aprobados"],
                "state" => 1, "color" => "#00a65a",
                "name" => trans("elearning::profesor/admin_lang.aprobadas") . " / " .
                    trans("elearning::profesor/admin_lang.completados"),
                "bg" => "bg-green", "fa" => "fa-thumbs-o-up",
                "porcentaje" => (($stats["aprobados"] * 100) / $stats["total_usuarios"])
            ),
            array(
                "total" => $stats["suspendidos"],
                "state" => 2, "color" => "#dd4b39",
                "name" => trans("elearning::profesor/admin_lang.denegadas"),
                "bg" => "bg-red", "fa" => "fa-thumbs-o-down",
                "porcentaje" => (($stats["suspendidos"] * 100) / $stats["total_usuarios"])
            )
        );

        return $a_tracking;
    }

    public function reloadStats(Request $request)
    {
        $asignatura = Asignatura::active()->findorFail($request->asignatura_id);
        if (empty($asignatura)) {
            abort(404);
        }

        //Sacamos el tracking de esta asignatura
        $stats_asignatura = $asignatura->getStats();

        $a_tracking = $this->reloadStatsBase($request, $stats_asignatura);

        return view("elearning::profesor.admin_detalle_partial")
            ->with(array('a_tracking' => $a_tracking, 'stats' => $stats_asignatura));
    }

    public function reloadStatsModulo(Request $request)
    {
        $modulo = Modulo::activos()->findorFail($request->modulo_id);
        if (empty($modulo)) {
            abort(404);
        }

        //Sacamos el tracking de este módulo
        $stats_modulo = $modulo->getStats();

        $a_tracking = $this->reloadStatsBase($request, $stats_modulo);

        return view("elearning::profesor.admin_detalle_partial")
            ->with(array('a_tracking' => $a_tracking, 'stats' => $stats_modulo));
    }

    public function detalleModulo(Request $request)
    {
        $modulo = Modulo::findorFail($request->id);
        if (empty($modulo)) {
            abort(404);
        }
        $page_title = trans("elearning::profesor/admin_lang.modulo_title") . " '$modulo->nombre'";

        //Sacamos el tracking de esta asignatura
        $stats_modulo = $modulo->getStats();

        $a_tracking = $this->reloadStatsBase($request, $stats_modulo);

        $convocatorias = $modulo->asignatura->convocatorias()->paginate(4);
        return view(
            "elearning::profesor.admin_detalle_modulo",
            compact('page_title', 'a_tracking', 'modulo', 'stats_modulo', 'convocatorias')
        );
    }

    /**
     * Método para mostrar el listado de usuarios que han accedido a un contenido
     *
     * @param Request $request
     *  id: Id del contenido del cual mostraremos la información
     * @return View
     */
    public function detalleContenido(Request $request)
    {
        // Obtenemos el contenido
        $contenido = Contenido::where("id", $request->id)->first();
        if (empty($contenido)) {
            abort(404);
        }

        // Obtenemos el móduo al cual pertenece el contenido
        $modulo = Modulo::where("id", $contenido->modulo_id)->first();
        if (empty($modulo)) {
            abort(404);
        }
        $page_title = trans("elearning::profesor/admin_lang.contenido_title") . " '$contenido->nombre'";

        // Sacamos el tracking de este contenido
        $stats_contenido = null;
        $a_tracking = null;
        $this->generateStatsContenido($contenido, $stats_contenido, $a_tracking);

        // Obtenemos la convocatorias en las cuales se ha realizado este contenido
        $convocatorias = $modulo->asignatura->convocatorias()->paginate(4);
        return view(
            "elearning::profesor.admin_detalle_contenido",
            compact(
                'page_title',
                'a_tracking',
                'contenido',
                'stats_modulo',
                'modulo',
                'stats_contenido',
                'convocatorias'
            )
        );
    }

    /**
     * Método para mostrar el detalle del examen realizado por el usuario
     *
     * @param Request $request
     *  id: Id del contenido del cual mostraremos la información
     *  user_id: Id del usuario del cual vamos a visualizar el examen
     * @return View
     */
    public function detalleContenidoExamen(Request $request)
    {
        // Obtenemos el contenido
        $contenido = Contenido::where("id", $request->id)->first();
        if (empty($contenido)) {
            abort(404);
        }

        // Obtenemos el móduo al cual pertenece el contenido
        $modulo = Modulo::where("id", $contenido->modulo_id)->first();
        if (empty($modulo)) {
            abort(404);
        }

        // Obtenemos el usuario
        $user = User::where("id", $request->user_id)->first();
        if (empty($user)) {
            abort(404);
        }

        $track = TrackContenido::where("id", $request->track_id)
            ->where("user_id", $user->id)
            ->where("contenido_id", $contenido->id)
            ->first();

        $evalQuery = $contenido->trackEvalByUserConvocatoria($user->id, $track->convocatoria_id);
        // Esta variable es un Collection de instancias del TrackContenidoEvaluacion,
        // que son todos los intentos del usuario a este examen.
        $intentos = $evalQuery->get();
        $validados = $evalQuery->validados()->get();

        $page_title = trans("elearning::profesor/admin_lang.examen") . " '$contenido->nombre'";

        return view(
            "elearning::profesor.admin_detalle_contenido_examen",
            compact(
                'page_title',
                'contenido',
                'modulo',
                'user',
                'intentos',
                'validados'
            )
        );
    }

    public function detalleContenidoExamenStore(Request $request)
    {
        dd($request);
    }

    /**
     * Método que retorna las estatidisticas del contenido que aparececen en la Views del detalle del contenido
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function reloadStatsContenido(Request $request)
    {
        $contenido = Contenido::activos()->findorFail($request->contenido_id);
        if (empty($contenido)) {
            abort(404);
        }

        // Sacamos el tracking de este contenido
        $stats_contenido = null;
        $a_tracking = null;
        $this->generateStatsContenido($contenido, $stats_contenido, $a_tracking);

        return view("elearning::profesor.admin_detalle_partial")
            ->with(array('a_tracking' => $a_tracking, 'stats' => $stats_contenido));
    }


    /**
     * Dado un contenido obtenemos sus estadisticas
     *  Pasamos por referencia los valores
     * @param Contenido $contenido
     * @param $stats_contenido
     * @param $a_tracking
     */
    private function generateStatsContenido(Contenido $contenido, &$stats_contenido, &$a_tracking)
    {
        // Sacamos el tracking de este contenido
        $stats_contenido = $contenido->getStats();

        $a_tracking = array(
            array(
                "total" => $stats_contenido["pendientes"],
                "state" => 0,
                "color" => "#f39c12",
                "name" => trans("elearning::profesor/admin_lang.pendientes"),
                "bg" => "bg-yellow",
                "fa" => "fa-clock-o",
                "porcentaje" => ($stats_contenido["total_usuarios"] > 0 ?
                    (($stats_contenido["pendientes"] * 100) / $stats_contenido["total_usuarios"]) : 0)
            ),
            array(
                "total" => $stats_contenido["aprobados"],
                "state" => 1, "color" => "#00a65a",
                "name" => trans("elearning::profesor/admin_lang.aprobadas") . " / " .
                    trans("elearning::profesor/admin_lang.completados"),
                "bg" => "bg-green",
                "fa" => "fa-thumbs-o-up",
                "porcentaje" => ($stats_contenido["total_usuarios"] > 0 ?
                    (($stats_contenido["aprobados"] * 100) / $stats_contenido["total_usuarios"]) : 0)
            ),
            array(
                "total" => $stats_contenido["suspendidos"],
                "state" => 2, "color" => "#dd4b39",
                "name" => trans("elearning::profesor/admin_lang.denegadas"),
                "bg" => "bg-red",
                "fa" => "fa-thumbs-o-down",
                "porcentaje" => ($stats_contenido["total_usuarios"] > 0 ?
                    (($stats_contenido["suspendidos"] * 100) / $stats_contenido["total_usuarios"]) : 0)
            )
        );
    }

    public function getDataUsers(Request $request)
    {
        if (isset($request->modulo_id)) {
            return $this->getDataUsersModulo($request->modulo_id);
        } else {
            return $this->getDataUsersAsignatura($request->asignatura_id);
        }
    }


    /**
     * Método que devuelve el contenido del Datatable con los usuarios que han visitado el contenido
     *
     * @param Request $request
     *  Recibe como parametro del contenido_id del cual se consultaran todos los usuarios que lo han visitado
     * @return mixed
     * @throws \Exception
     */
    public function getDataUsersContenido(Request $request)
    {
        $misAlumnosSession = (Session::has('todo_los_alumnos')) ? true : false;

        // Leemos el contenido que vamos a trabajar
        $contenido = Contenido::find($request->contenido_id);

        $elementos = TrackContenido::select(
            array(
                'track_contenido.id',
                'trackContenido.user_id',
                DB::raw('CONCAT(' . env('DB_PREFIX') . 'user_profiles.first_name, " ", ' .
                    env('DB_PREFIX') . 'user_profiles.last_name) AS first_name'),
                'track_contenido.fecha_lectura',
                'asignatura_convocatorias.nombre as convocatoria',
                'asignatura_convocatorias.id as idconvocatoria',
                'track_contenido.obligatorio'
            )
        )->join('user_profiles', function ($join) {
            $join->on('user_profiles.user_id', '=', 'track_contenido.user_id');
        })->join('asignatura_convocatorias', function ($join) {
            $join->on('asignatura_convocatorias.id', '=', 'track_contenido.convocatoria_id');
        })->where("track_contenido.contenido_id", "=", $contenido->id);

        if (!auth()->user()->can("admin-alumnos-all") || !$misAlumnosSession) {
            $elementos
                ->join('grupo_users', 'grupo_users.user_id', "=", "user_profiles.user_id")
                ->join('grupo_profesor', "grupo_profesor.grupo_id", "=", "grupo_users.grupo_id")
                ->where("grupo_profesor.user_id", auth()->user()->id);
        }

        return Datatables::of($elementos)
            ->addColumn('actions', function ($row) use ($contenido) {
                $actions = '
                    <button class="btn bg-maroon btn-sm"
                    onclick="javascript:Reset(\'' . url('admin/profesor/contenido/' .
                    $row->id . '/reset/' . $row->user_id) . '\');"
                    data-content="' . trans('elearning::profesor/admin_lang.reset') . '"
                    data-placement="right" data-toggle="popover">
                    <i class="fa fa-repeat" aria-hidden="true"></i>
                    </button>
                ';
                if ($contenido->evaluacion !== null) {
                    $actions .= '<a style="margin-left: 3px"
                    href="' . url("admin/profesor/detalle/modulo/contenido/" . $contenido->id .
                        "/examen/" . $row->id . "/" . $row->user_id) . '"
                    class="btn bg-aqua btn-sm">
                    <i class="fa fa-list" aria-hidden="true"></i>
                    </a>';
                }

                return $actions;
            })
            ->editColumn('obligatorio', function ($row) {
                $txt = trans("general/admin_lang.no");
                if ($row->obligatorio == 1) {
                    $txt = trans("general/admin_lang.yes");
                }
                return $txt;
            })
            ->editColumn('fecha_lectura', function ($row) {
                return $row->fecha_lectura_formatted;
            })
            ->filterColumn('first_name', function ($query, $keyword) {
                $query->whereRaw("CONCAT(" . env('DB_PREFIX') . "user_profiles.first_name, ' ', " .
                    env('DB_PREFIX') . "_user_profiles.last_name) like ?", ["%{$keyword}%"]);
            })
            ->removeColumn('id')
            ->removeColumn('user_id')
            ->removeColumn('idconvocatoria')
            ->rawColumns(['actions'])
            ->make();
    }

    public function getDataUsersModulo($modulo_id)
    {
        $misAlumnosSession = (Session::has('todo_los_alumnos')) ? true : false;

        $url_to_reset = "admin/profesor/modulo/resetear";
        $elementos = TrackModulo::select(
            array(
                'track_modulo.id',
                'track_modulo.user_id',
                DB::raw('CONCAT(' . env('DB_PREFIX') . 'user_profiles.first_name, " ", ' .
                    env('DB_PREFIX') . 'user_profiles.last_name) AS first_name'),
                'track_modulo.fecha_inicio',
                'track_modulo.fecha_fin',
                'asignatura_convocatorias.nombre as convocatoria',
                'asignatura_convocatorias.id as idconvocatoria',
                'track_modulo.aprobado',
                'track_modulo.nota',
            )
        )->join('user_profiles', function ($join) {
            $join->on('user_profiles.user_id', '=', 'track_modulo.user_id');
        })->join('asignatura_convocatorias', function ($join) {
            $join->on('asignatura_convocatorias.id', '=', 'track_modulo.convocatoria_id');
        })->where("track_modulo.modulo_id", "=", $modulo_id);

        if (!auth()->user()->can("admin-alumnos-all") || !$misAlumnosSession) {
            $elementos
                ->join('grupo_users', 'grupo_users.user_id', "=", "user_profiles.user_id")
                ->join('grupo_profesor', "grupo_profesor.grupo_id", "=", "grupo_users.grupo_id")
                ->where("grupo_profesor.user_id", auth()->user()->id);
        }

        return Datatables::of($elementos)
            ->addColumn('actions', function ($row) use ($url_to_reset, $modulo_id) {
                return '
                    <button class="btn bg-maroon btn-sm"
                    onclick="javascript:Reset(\'' . url($url_to_reset . '/' . $row->id) . '\');"
                    data-content="' . trans('elearning::profesor/admin_lang.reset') . '"
                    data-placement="right" data-toggle="popover">
                    <i class="fa fa-repeat" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'' .
                    url('admin/profesor/modulo/' . $modulo_id . '/user-stats/' . $row->user_id) . '\';"
                    data-content="' . trans('elearning::profesor/admin_lang.stats') . '"
                    data-placement="left" data-toggle="popover">
                    <i class="fa fa-bar-chart" aria-hidden="true"></i>
                    </button>
                ';
            })
            ->editColumn('aprobado', function ($row) {
                $txt = trans("general/admin_lang.no");
                if ($row->aprobado == 1) {
                    $txt = trans("general/admin_lang.yes");
                }
                return $txt;
            })
            ->editColumn('fecha_inicio', function ($row) {
                //$fecha_inicio = new \Carbon\Carbon($row->fecha_inicio);
                //return $fecha_inicio->format('d/m/Y H:i:s');
                return $row->fecha_inicio_formatted;
            })
            ->editColumn('fecha_fin', function ($row) {
                //$fecha_fin = new \Carbon\Carbon($row->fecha_fin);
                //return $fecha_fin->format('d/m/Y H:i:s');
                return $row->fecha_fin_formatted;
            })
            ->filterColumn('first_name', function ($query, $keyword) {
                $query->whereRaw("CONCAT(" . env('DB_PREFIX') . "user_profiles.first_name, ' ', " .
                    env('DB_PREFIX') . "_user_profiles.last_name) like ?", ["%{$keyword}%"]);
            })
            ->removeColumn('id')
            ->removeColumn('user_id')
            ->removeColumn('idconvocatoria')
            ->rawColumns(['actions'])
            ->make();
    }

    public function getDataUsersAsignatura($asignatura_id)
    {
        $misAlumnosSession = (Session::has('todo_los_alumnos')) ? true : false;

        $url_to_reset = "admin/profesor/asignatura/resetear";
        $elementos = TrackAsignatura::select(
            array(
                'track_asignatura.id',
                'track_asignatura.user_id',
                DB::raw('CONCAT(' . env('DB_PREFIX') . 'user_profiles.first_name, " ", ' .
                    env('DB_PREFIX') . 'user_profiles.last_name) AS first_name'),
                'track_asignatura.fecha_inicio',
                'track_asignatura.fecha_fin',
                'asignatura_convocatorias.nombre as convocatoria',
                'asignatura_convocatorias.id as idconvocatoria',
                'track_asignatura.aprobado',
                'track_asignatura.nota',
            )
        )->join('user_profiles', function ($join) {
            $join->on('user_profiles.user_id', '=', 'track_asignatura.user_id');
        })->join('asignatura_convocatorias', function ($join) {
            $join->on('asignatura_convocatorias.id', '=', 'track_asignatura.convocatoria_id');
        })->where("track_asignatura.asignatura_id", "=", $asignatura_id);

        if (!auth()->user()->can("admin-alumnos-all") || !$misAlumnosSession) {
            $elementos
                ->join('grupo_users', 'grupo_users.user_id', "=", "user_profiles.user_id")
                ->join('grupo_profesor', "grupo_profesor.grupo_id", "=", "grupo_users.grupo_id")
                ->where("grupo_profesor.user_id", auth()->user()->id);
        }

        return Datatables::of($elementos)
            ->addColumn('actions', function ($row) use ($url_to_reset, $asignatura_id) {
                return '
                    <button class="btn bg-maroon btn-sm"
                    onclick="javascript:Reset(\'' . url($url_to_reset . '/' . $row->id) . '\');"
                    data-content="' . trans('elearning::profesor/admin_lang.reset') . '"
                    data-placement="right"
                    data-toggle="popover">
                    <i class="fa fa-repeat" aria-hidden="true"></i>
                    </button>
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'' .
                    url('admin/profesor/asignatura/' . $asignatura_id . '/user-stats/' . $row->user_id) . '\';"
                    data-content="' . trans('elearning::profesor/admin_lang.stats') . '"
                    data-placement="left"
                    data-toggle="popover">
                    <i class="fa fa-bar-chart" aria-hidden="true"></i>
                    </button>
                ';
            })
            ->editColumn('aprobado', function ($row) {
                $txt = trans("general/admin_lang.no");
                if ($row->aprobado == 1) {
                    $txt = trans("general/admin_lang.yes");
                }
                return $txt;
            })
            ->editColumn('fecha_inicio', function ($row) {
                //$fecha_inicio = new \Carbon\Carbon($row->fecha_inicio);
                //return $fecha_inicio->format('d/m/Y H:i:s');
                return $row->fecha_inicio_formatted;
            })
            ->editColumn('fecha_fin', function ($row) {
                //$fecha_fin = new \Carbon\Carbon($row->fecha_fin);
                //return $fecha_fin->format('d/m/Y H:i:s');
                return $row->fecha_fin_formatted;
            })
            ->filterColumn('first_name', function ($query, $keyword) {
                $query->whereRaw("CONCAT(" . env('DB_PREFIX') . "user_profiles.first_name, ' ', " .
                    env('DB_PREFIX') . "user_profiles.last_name) like ?", ["%{$keyword}%"]);
            })
            ->removeColumn('id')
            ->removeColumn('user_id')
            ->removeColumn('idconvocatoria')
            ->rawColumns(['actions'])
            ->make();
    }

    public function getDataModules(Request $request)
    {
        $locale = config('app.default_locale');

        $modulos = Modulo::select(
            array(
                'modulos.id',
                'modulos.asignatura_id',
                'modulos.activo',
                'modulo_translations.nombre',
                'modulo_translations.url_amigable'
            )
        )->join('modulo_translations', function ($join) use ($locale) {
            $join->on('modulo_translations.modulo_id', '=', 'modulos.id');
            $join->on('modulo_translations.locale', '=', DB::raw("'" . $locale . "'"));
        })->where("asignatura_id", $request->asignatura_id);

        return Datatables::of($modulos)
            ->editColumn('url_amigable', function ($row) {
                return "<a href='/modulos/detalle_modulo/" .
                    $row->url_amigable . "/" . $row->id . "' target='_blank'>/modulos/detalle_modulo/" .
                    $row->url_amigable . "/" . $row->id . "</a>";
            })
            ->addColumn('grafica', function ($row) {
                $stats = $row->getStats();
                $array_stats = array($stats["pendientes"], $stats["aprobados"], $stats["suspendidos"]);
                $strInfo = '<div class="sparkline">';
                $strInfo .= implode(",", $array_stats);
                $strInfo .= '</div>';
                return $strInfo;
            })
            ->addColumn('actions', '
                   <button class="btn btn-primary btn-sm"
                   onclick="javascript:window.location=\'{{ url(\'admin/profesor/detalle/modulo/\'.$id.\'\') }}\';"
                   data-content="' . trans('elearning::profesor/admin_lang.stats') . '"
                   data-placement="left" data-toggle="popover">
                   <i class="fa fa-bar-chart" aria-hidden="true"></i></button>
                ')
            ->removeColumn('activo')
            ->removeColumn('id')
            ->removeColumn('asignatura_id')
            ->removeColumn('descripcion')
            ->removeColumn('coordinacion')
            ->rawColumns(['url_amigable', 'grafica', 'actions'])
            ->make();
    }

    public function getDataContenidos(Request $request)
    {
        $locale = config('app.default_locale');

        $modulo_id = $request->modulo_id;
        $asignatura_id = $request->asignatura_id;

        $contenidos = Contenido::select(
            array(
                'contenidos.id',
                'contenidos.modulo_id',
                'contenidos.activo',
                'contenidos_translations.nombre',
                'contenidos_translations.url_amigable',
                'tipo_contenidos.slug'
            )
        )->join('contenidos_translations', function ($join) use ($locale) {
            $join->on('contenidos_translations.contenido_id', '=', 'contenidos.id');
            $join->on('contenidos_translations.locale', '=', DB::raw("'" . $locale . "'"));
        })
            ->join('tipo_contenidos', 'tipo_contenidos.id', "contenidos.tipo_contenido_id")
            ->where("modulo_id", $modulo_id)
            ->orderBy('contenidos.lft', 'ASC');

        return Datatables::of($contenidos)
            ->editColumn('url_amigable', function ($row) {
                return "<a href='/contenido/detalle-contenido/" . $row->url_amigable . "/" .
                    $row->id . "' target='_blank'>/modulos/detalle_contenido/" .
                    $row->url_amigable . "/" . $row->id . "</a>";
            })
            ->addColumn('grafica', function ($row) {
                $stats = $row->getStats();
                $array_stats = array($stats["pendientes"], $stats["aprobados"], $stats["suspendidos"]);
                $strInfo = '<div class="sparkline">';
                $strInfo .= implode(",", $array_stats);
                $strInfo .= '</div>';
                return $strInfo;
            })
            ->addColumn('actions', function ($row) use ($asignatura_id, $modulo_id) {
                $str = "";
                if ($row->evaluacion !== null) {
                    $str .= '<button style="margin-left: 3px"
                    onclick="recalcular(\'' . url("admin/profesor/contenido/recalcular/general/" . $row->id) . '\')"
                    class="btn btn-success btn-sm"><i class="fa fa-repeat" aria-hidden="true"></i></button>';
                    $str .= '<a style="margin-left: 3px" href="' . url("admin/profesor/asignatura/" .
                        $asignatura_id . "/generateExcelPreguntas/" . $modulo_id . "/contenido/" . $row->id) . '"
                    class="btn bg-purple btn-sm"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>';
                }
                $str .= '<a style="margin-left: 3px" href="' .
                    url("admin/profesor/detalle/modulo/contenido/" . $row->id) .
                    '" class="btn btn-primary btn-sm"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>';
                return $str;
            })
            ->editColumn('slug', function ($row) {
                switch ($row->slug) {
                    case 'tema':
                        $tipo = 'fa-bookmark-o';
                        break;
                    case 'pagina':
                        $tipo = 'fa-edit';
                        break;
                    case 'eval':
                        $tipo = 'fa-question';
                        break;
                    case 'galeria':
                        $tipo = 'fa-picture-o';
                        break;
                    case 'video':
                        $tipo = 'fa-file-video-o';
                        break;
                    default:
                        $tipo = 'fa-file-o';
                }
                $str = "";
                $str .= '<a href="#" class="btn btn-default btn-sm disabled"><i class="fa ' .
                    $tipo . '" aria-hidden="true"></i></a>';
                return $str;
            })
            ->removeColumn('activo')
            ->removeColumn('id')
            ->removeColumn('modulo_id')
            ->rawColumns(['url_amigable', 'grafica', 'actions', 'slug'])
            ->make();
    }

    public function reseterAsignatura(Request $request)
    {
        $tracking = TrackAsignatura::findorFail($request->id);
        if (empty($tracking)) {
            abort(404);
        }

        $asignatura = Asignatura::findorFail($tracking->asignatura_id);
        if (empty($asignatura)) {
            abort(404);
        }

        //Para resetear la asigantura tenemos que borrar todos los contenidos de este usuario,
        // los modulos y la asignatura
        RespuestaResultado::where("user_id", "=", $tracking->user_id)
            ->where("convocatoria_id", "=", $tracking->convocatoria_id)
            ->where("asignatura_id", "=", $tracking->asignatura_id)->delete();
        TrackContenido::where("user_id", "=", $tracking->user_id)
            ->where("convocatoria_id", "=", $tracking->convocatoria_id)
            ->where("asignatura_id", "=", $tracking->asignatura_id)->delete();
        TrackVideo::where("user_id", "=", $tracking->user_id)
            ->where("asignatura_id", "=", $tracking->asignatura_id)->delete();
        TrackContenidoEvaluacion::where("user_id", "=", $tracking->user_id)
            ->where("convocatoria_id", "=", $tracking->convocatoria_id)
            ->where("asignatura_id", "=", $tracking->asignatura_id)->delete();
        TrackModulo::where("user_id", "=", $tracking->user_id)
            ->where("convocatoria_id", "=", $tracking->convocatoria_id)
            ->where("asignatura_id", "=", $tracking->asignatura_id)->delete();
        $tracking->delete();
    }

    public function resetearModulo(Request $request)
    {
        $tracking = TrackModulo::findorFail($request->id);
        if (empty($tracking)) {
            abort(404);
        }

        $modulo = Modulo::findorFail($tracking->modulo_id);
        if (empty($modulo)) {
            abort(404);
        }

        //En este caso tenemos que desmarcar como completada la asignatura en el caso que lo estuviera.
        TrackContent::desmarcarAsignatura(
            $tracking->asignatura_id,
            $tracking->user_id,
            $tracking->convocatoria_id
        );

        // Para resetear la asigantura tenemos que borrar todos los contenidos de este usuario,
        // los modulos y la asignatura
        RespuestaResultado::where("user_id", "=", $tracking->user_id)
            ->where("convocatoria_id", "=", $tracking->convocatoria_id)
            ->where("modulo_id", "=", $tracking->modulo_id)
            ->delete();
        TrackContenido::where("user_id", "=", $tracking->user_id)
            ->where("convocatoria_id", "=", $tracking->convocatoria_id)
            ->where("modulo_id", "=", $tracking->modulo_id)
            ->delete();
        TrackVideo::where("user_id", "=", $tracking->user_id)
            ->where("modulo_id", "=", $tracking->modulo_id)
            ->delete();
        TrackContenidoEvaluacion::where("user_id", "=", $tracking->user_id)
            ->where("convocatoria_id", "=", $tracking->convocatoria_id)
            ->where("modulo_id", "=", $tracking->modulo_id)
            ->delete();

        //Borramos la entrada del módulo
        $tracking->delete();
    }

    /**
     * Método que hace un reset de la visita y ejecución de un contenido para un usuario
     *
     * El reset se hace para un usuario concreto y quita la visita del contenido y si tuviese un examen lo elimina
     * también. Se debe pasar de manera obligatoria un id de contenido y un id de usuario.
     *
     * @param Request $request
     *    debe tener $request->id con el id del contenido y $request->user_id con id de usuario
     *
     * @return void
     */
    public function resetContenido(Request $request)
    {
        $tracking = TrackContenido::find($request->id);
        if (empty($tracking)) {
            abort(404);
        }

        $contenido = Contenido::find($tracking->contenido_id);
        if (empty($contenido)) {
            abort(404);
        }

        $user = User::find($request->user_id);
        if (empty($user) || $user->id !== $tracking->user_id) {
            abort(404);
        }

        //En este caso tenemos que desmarcar como completada la asignatura en el caso que lo estuviera.
        TrackContent::desmarcarAsignatura($tracking->asignatura_id, $tracking->user_id, $tracking->convocatoria_id);

        //Para resetear el contenido tenemos que borrar:
        //  1.- los resultados del examen en caso de que el contenido lo fuese
        //  2.- el contenido visitado en sí
        //  3.- si el contenido es de tipo video tambien borramos el seguimiento
        //  4.- Los intentos de realizar el examen
        //  5.- Borramos el tracking de este contenido
        RespuestaResultado::where("user_id", "=", $tracking->user_id)
            ->where("convocatoria_id", "=", $tracking->convocatoria_id)
            ->where("contenido_id", "=", $tracking->contenido_id)
            ->delete();

        TrackContenido::where("user_id", "=", $tracking->user_id)
            ->where("convocatoria_id", "=", $tracking->convocatoria_id)
            ->where("contenido_id", "=", $tracking->contenido_id)
            ->delete();


        TrackVideo::where("user_id", "=", $tracking->user_id)
            ->where("contenido_id", "=", $tracking->contenido_id)
            ->delete();

        TrackContenidoEvaluacion::where("user_id", "=", $tracking->user_id)
            ->where("convocatoria_id", "=", $tracking->convocatoria_id)
            ->where("contenido_id", "=", $tracking->contenido_id)
            ->delete();

        //Borramos la entrada del módulo
        $tracking->delete();
    }

    public function recalcularNota(TrackContenidoEvaluacion $track_eval)
    {
        $profesorHelper = new ProfesorHelper();
        $track_eval = $profesorHelper->recalcularNota($track_eval);
        return response()->json($track_eval);
    }

    public function recalcularNotaGeneral(Contenido $contenido)
    {
        $profesorHelper = new ProfesorHelper();

        $res = array("status" => "success");

        foreach ($contenido->trackEvaluacion as $track_eval) {
            $track_eval = $profesorHelper->recalcularNota($track_eval);
            if ($track_eval === null) {
                $res["status"] = "error";
            }
        }

        return response()->json($res);
    }

    public function generateExcel($asignatura_id, $modulo_id = "")
    {
        $asignatura = Asignatura::active()->findorFail($asignatura_id);

        if (empty($asignatura)) {
            abort(404);
        }

        $misAlumnosSession = (Session::has('todo_los_alumnos')) ? true : false;

        $modulo = Modulo::activos()->find($modulo_id);
        if (!empty($modulo)) {
            $contenidosActivos = $modulo->contenidos()->activos()->where("tipo_contenido_id", "<>", 1)->count();
            $elementos = TrackModulo::where("track_modulo.modulo_id", "=", $modulo->id);

            if (!auth()->user()->can("admin-alumnos-all") || !$misAlumnosSession) {
                $elementos
                    ->join('grupo_users', 'grupo_users.user_id', "=", "track_modulo.user_id")
                    ->join('grupo_profesor', "grupo_profesor.grupo_id", "=", "grupo_users.grupo_id")
                    ->where("grupo_profesor.user_id", auth()->user()->id);
            }
            $elementos = $elementos
                ->select('track_modulo.*')
                ->get();
        } else {
            $elementos = TrackAsignatura::where("track_asignatura.asignatura_id", "=", $asignatura->id);

            if (!auth()->user()->can("admin-alumnos-all") || !$misAlumnosSession) {
                $elementos
                    ->join('grupo_users', 'grupo_users.user_id', "=", "track_asignatura.user_id")
                    ->join('grupo_profesor', "grupo_profesor.grupo_id", "=", "grupo_users.grupo_id")
                    ->where("grupo_profesor.user_id", auth()->user()->id);
            }
            $elementos = $elementos
                ->select('track_asignatura.*')
                ->get();
        }

        ini_set('memory_limit', '300M');

        if (ob_get_contents()) {
            ob_end_clean();
        }
        set_time_limit(1000);

        $spreadsheet = new Spreadsheet();
        $spreadsheet
            ->getProperties()
            ->setCreator(config('app.name', ''))
            ->setLastModifiedBy(config('app.name', '')) // última vez modificado por
            ->setTitle(trans('elearning::profesor/admin_lang.list_export_modulos'))
            ->setSubject(trans('elearning::profesor/admin_lang.list_export_modulos'))
            ->setDescription(trans('elearning::profesor/admin_lang.list_export_modulos'))
            ->setKeywords(trans('elearning::profesor/admin_lang.list_export_modulos'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(trans('elearning::profesor/admin_lang.data_excel_modulos'));

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        $sheet->getHeaderFooter()->setOddHeader('Zona profesor - Módulos');
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');

        $row = 1;

        /*
        // Ponemos algunos títulos a modo de ejemplo
        $sheet->setCellValueByColumnAndRow(1, $row, "Proyectos de:");
        $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);
        ExcelHelper::cellColor($sheet, 'A'.$row.':B'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(3, $row, " Aduxia");
        $sheet->mergeCellsByColumnAndRow(3, $row, 8, $row);
        ExcelHelper::cellColor($sheet, 'C'.$row.':G'.$row, '92d050');

        $sheet->getStyle('A'.$row.':D'.$row)->getFont()->setSize(14);

        $row++;

        // Segunda linea de información
        $sheet->setCellValueByColumnAndRow(1, $row, "Fecha inicio:");
        $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);

        ExcelHelper::cellColor($sheet, 'A'.$row.':G'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(3, $row, Carbon::now()->format("d/m/Y"));
        $sheet->mergeCellsByColumnAndRow(3, $row, 8, $row);
        ExcelHelper::cellColor($sheet, 'C'.$row.':G'.$row, '92d050');

        $sheet->getStyle('A'.$row.':D'.$row)->getFont()->setSize(14);

        $row++;


        //Styles
        $style = array(
            'font' => array('bold' => true,),
            'alignment' => array('horizontal' =>  Alignment::HORIZONTAL_CENTER,),
            'borders' => array(
                'top' => array(
                    'style' => Border::BORDER_THIN,
                ),
            ),
            'fill' => array(
                'type' => Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startcolor' => array(
                    'argb' => 'FFA0A0A0',
                ),
            )
        );
        //Bolds
        $sheet
            ->getStyle('A'.$row.':G'.$row)
            ->applyFromArray($style);

        ExcelHelper::cellColor($sheet,'A'.$row, 'ffc000');

        $sheet->setCellValueByColumnAndRow(1, $row, "Filiación");
        $sheet->mergeCellsByColumnAndRow(1,$row,2,$row);
        ExcelHelper::cellColor($sheet,'B'.$row.':C'.$row, '92d050');

        $sheet->setCellValueByColumnAndRow(3, $row, "Datos basales");
        $sheet->mergeCellsByColumnAndRow(3,$row,6,$row);
        ExcelHelper::cellColor($sheet,'D'.$row.':G'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(7, $row, "Valoración basal");
        $sheet->mergeCellsByColumnAndRow(7,$row,17,$row);
        ExcelHelper::cellColor($sheet,'H'.$row.':R'.$row, '92d050');

        $sheet->setCellValueByColumnAndRow(18, $row, "Complicaciones");
        $sheet->mergeCellsByColumnAndRow(18,$row,27,$row);
        ExcelHelper::cellColor($sheet,'S'.$row.':AB'.$row, '00b050');

        $row++;
        */

        // Ponemos las cabeceras
        if (!empty($modulo)) {
            $cabeceras = array(
                trans('users/lang.identificador'),
                trans('users/lang.usuario'),
                trans('users/lang._NOMBRE_USUARIO'),
                trans('users/lang._APELLIDOS_USUARIO'),
                trans('elearning::profesor/admin_lang.asignatura'),
                trans('elearning::profesor/admin_lang.modulo'),
                trans('elearning::profesor/admin_lang.fecha_inicio'),
                trans('elearning::profesor/admin_lang.fecha_fin'),
                trans('elearning::profesor/admin_lang.convocatoria'),
                trans('elearning::profesor/admin_lang.aprobado'),
                trans('elearning::profesor/admin_lang.porcentaje'),
                trans('elearning::profesor/admin_lang.nota'),
            );
        } else {
            $cabeceras = array(
                trans('users/lang.identificador'),
                trans('users/lang.usuario'),
                trans('users/lang._NOMBRE_USUARIO'),
                trans('users/lang._APELLIDOS_USUARIO'),
                trans('elearning::profesor/admin_lang.asignatura'),
                trans('elearning::profesor/admin_lang.fecha_inicio'),
                trans('elearning::profesor/admin_lang.fecha_fin'),
                trans('elearning::profesor/admin_lang.convocatoria'),
                trans('elearning::profesor/admin_lang.aprobado'),
                trans('elearning::profesor/admin_lang.porcentaje'),
                trans('elearning::profesor/admin_lang.nota'),
            );
        }

        ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');
        $row++;

        // Ahora los registros

        foreach ($elementos as $key => $value) {
            $fisrt_name = $value->user->userProfile->first_name;
            $last_name = $value->user->userProfile->last_name;
            if (!empty($modulo)) {
                $completados = $modulo->getCompleteUser($value->user->id);
                $avance = number_format((count($completados) * 100) / $contenidosActivos, 2);

                $valores = array(
                    $value->user->id,
                    $value->user->username,
                    $fisrt_name,
                    $last_name,
                    $value->asignatura->titulo,
                    $value->modulo->nombre,
                    $value->fecha_inicio_formatted,
                    $value->fecha_fin_formatted,
                    $value->convocatoria->nombre,
                    ($value->aprobado == '1') ? trans('general/admin_lang.yes') : trans('general/admin_lang.no'),
                    $avance . "%",
                    $value->nota
                );
            } else {
                $avance = $asignatura->getPercentContents($value->convocatoria_id);

                $valores = array(
                    $value->user->id,
                    $value->user->username,
                    $fisrt_name,
                    $last_name,
                    $value->asignatura->titulo,
                    $value->fecha_inicio_formatted,
                    $value->fecha_fin_formatted,
                    $value->convocatoria->nombre,
                    ($value->aprobado == '1') ? trans('general/admin_lang.yes') : trans('general/admin_lang.no'),
                    $avance . "%",
                    $value->nota
                );
            }


            $j = 1;
            foreach ($valores as $valor) {
                $sheet->setCellValueByColumnAndRow($j++, $row, $valor);
            }
            $row++;
        }

        ExcelHelper::autoSizeCurrentRow($sheet);

        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);


        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $file_name = trans('elearning::profesor/admin_lang.list_export_modulos') .
            "_" . Carbon::now()->format('YmdHis');
        $outPath = storage_path("app/exports/");
        if (!file_exists($outPath)) {
            mkdir($outPath, 0777, true);
        }
        $writer->save($outPath . $file_name . '.xlsx');

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx' . '"');
        header('Cache-Control: max-age=0');

        /*
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        */

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function generateExcelExamen($asignatura_id, $modulo_id, $contenido_id)
    {
        $asignatura = Asignatura::active()->findorFail($asignatura_id);

        if (empty($asignatura)) {
            abort(404);
        }

        $modulo = Modulo::activos()->findorFail($modulo_id);
        if (empty($modulo)) {
            abort(404);
        }

        ini_set('memory_limit', '300M');

        if (ob_get_contents()) {
            ob_end_clean();
        }
        set_time_limit(1000);

        $spreadsheet = new Spreadsheet();
        $spreadsheet
            ->getProperties()
            ->setCreator(config('app.name', ''))
            ->setLastModifiedBy(config('app.name', '')) // última vez modificado por
            ->setTitle(trans('elearning::profesor/admin_lang.list_export_preguntas'))
            ->setSubject(trans('elearning::profesor/admin_lang.list_export_preguntas'))
            ->setDescription(trans('elearning::profesor/admin_lang.list_export_preguntas'))
            ->setKeywords(trans('elearning::profesor/admin_lang.list_export_preguntas'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(trans('elearning::profesor/admin_lang.data_excel_preguntas'));

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        $sheet->getHeaderFooter()->setOddHeader('Zona profesor - Preguntas');
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');

        $row = 1;

        /*
        // Ponemos algunos títulos a modo de ejemplo
        $sheet->setCellValueByColumnAndRow(1, $row, "Proyectos de:");
        $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);
        ExcelHelper::cellColor($sheet, 'A'.$row.':B'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(3, $row, " Aduxia");
        $sheet->mergeCellsByColumnAndRow(3, $row, 8, $row);
        ExcelHelper::cellColor($sheet, 'C'.$row.':G'.$row, '92d050');

        $sheet->getStyle('A'.$row.':D'.$row)->getFont()->setSize(14);

        $row++;

        // Segunda linea de información
        $sheet->setCellValueByColumnAndRow(1, $row, "Fecha inicio:");
        $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);

        ExcelHelper::cellColor($sheet, 'A'.$row.':G'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(3, $row, Carbon::now()->format("d/m/Y"));
        $sheet->mergeCellsByColumnAndRow(3, $row, 8, $row);
        ExcelHelper::cellColor($sheet, 'C'.$row.':G'.$row, '92d050');

        $sheet->getStyle('A'.$row.':D'.$row)->getFont()->setSize(14);

        $row++;


        //Styles
        $style = array(
            'font' => array('bold' => true,),
            'alignment' => array('horizontal' =>  Alignment::HORIZONTAL_CENTER,),
            'borders' => array(
                'top' => array(
                    'style' => Border::BORDER_THIN,
                ),
            ),
            'fill' => array(
                'type' => Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startcolor' => array(
                    'argb' => 'FFA0A0A0',
                ),
            )
        );
        //Bolds
        $sheet
            ->getStyle('A'.$row.':G'.$row)
            ->applyFromArray($style);

        ExcelHelper::cellColor($sheet,'A'.$row, 'ffc000');

        $sheet->setCellValueByColumnAndRow(1, $row, "Filiación");
        $sheet->mergeCellsByColumnAndRow(1,$row,2,$row);
        ExcelHelper::cellColor($sheet,'B'.$row.':C'.$row, '92d050');

        $sheet->setCellValueByColumnAndRow(3, $row, "Datos basales");
        $sheet->mergeCellsByColumnAndRow(3,$row,6,$row);
        ExcelHelper::cellColor($sheet,'D'.$row.':G'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(7, $row, "Valoración basal");
        $sheet->mergeCellsByColumnAndRow(7,$row,17,$row);
        ExcelHelper::cellColor($sheet,'H'.$row.':R'.$row, '92d050');

        $sheet->setCellValueByColumnAndRow(18, $row, "Complicaciones");
        $sheet->mergeCellsByColumnAndRow(18,$row,27,$row);
        ExcelHelper::cellColor($sheet,'S'.$row.':AB'.$row, '00b050');

        $row++;
        */

        $cabeceras = array(
            trans('users/lang.identificador'),
            trans('users/lang.usuario'),
            trans('users/lang._NOMBRE_USUARIO'),
            trans('users/lang._APELLIDOS_USUARIO'),
            trans('elearning::profesor/admin_lang.nombre_pregunta'),
            trans('elearning::profesor/admin_lang.nombre_respuesta'),
            trans('elearning::profesor/admin_lang.correcta'),
            trans('elearning::profesor/admin_lang.valor_respuesta'),
            trans('elearning::profesor/admin_lang.tipo_respuesta'),
            trans('elearning::profesor/admin_lang.fecha_lectura'),
            trans('elearning::profesor/admin_lang.test'),
            trans('elearning::profesor/admin_lang.asignatura')
        );

        ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');
        $row++;

        // Ahora los registros
        $contenido = Contenido::findOrFail($contenido_id);
        $respuestas = DB::table('respuesta_resultados')
            ->join("track_contenido_evaluacion AS tce", function ($join) {
                $join->on('tce.contenido_id', '=', 'respuesta_resultados.contenido_id')
                    ->on('tce.modulo_id', '=', 'respuesta_resultados.modulo_id')
                    ->on('tce.asignatura_id', '=', 'respuesta_resultados.asignatura_id')
                    ->on('tce.convocatoria_id', '=', 'respuesta_resultados.convocatoria_id')
                    ->on('tce.user_id', '=', 'respuesta_resultados.user_id');
            })
            ->join('users', 'respuesta_resultados.user_id', "users.id")
            ->join('user_profiles', 'user_profiles.user_id', "users.id")
            ->join('respuestas', 'respuesta_resultados.respuesta_id', "=", "respuestas.id")
            ->join('respuesta_translations as rt', function ($join) {
                $join->on("respuestas.id", "=", "rt.respuesta_id")
                    ->on("rt.locale", "=", "user_profiles.user_lang");
            })
            ->join('preguntas as p', 'respuestas.pregunta_id', "=", "p.id")
            ->join('tipo_preguntas as tp', 'p.tipo_pregunta_id', "=", "tp.id")
            ->join('pregunta_translations as pt', function ($join) {
                $join->on("respuestas.pregunta_id", "=", "pt.pregunta_id")
                    ->on("pt.locale", "=", "user_profiles.user_lang");
            })
            ->join('contenidos_translations as ct', function ($join) {
                $join->on("respuesta_resultados.contenido_id", "=", "ct.contenido_id")
                    ->on("ct.locale", "=", "user_profiles.user_lang");
            })
            ->join(
                'contenidos_evaluacion',
                'respuesta_resultados.contenido_id',
                "=",
                "contenidos_evaluacion.contenido_id"
            )
            ->join('modulos', 'contenidos_evaluacion.modulo_id', "=", "modulos.id")
            ->join('asignatura_translations as ast', function ($join) {
                $join->on("modulos.asignatura_id", "=", "ast.asignatura_id")
                    ->on("ast.locale", "=", "user_profiles.user_lang");
            })
            ->where("respuesta_resultados.contenido_id", "=", "$contenido->id")
            ->where("respuesta_resultados.marcada", "=", 1)
            ->where("tce.validado", "=", 1)
            ->orderBy("user_profiles.user_id")
            ->select(
                "user_profiles.id as user_id",
                "users.username as username",
                "user_profiles.first_name",
                "user_profiles.last_name",
                "pt.nombre as pregunta",
                "rt.nombre as respuesta",
                "respuesta_resultados.marcada",
                "respuesta_resultados.correcta",
                "respuesta_resultados.puntos_correcta",
                "respuesta_resultados.puntos_incorrecta",
                "tce.fecha_intento",
                "ct.nombre as nombre_test",
                "ast.titulo",
                "respuesta_resultados.observaciones",
                "tp.slug"
            );

        $misAlumnosSession = (Session::has('todo_los_alumnos')) ? true : false;
        if (!auth()->user()->can("admin-alumnos-all") || !$misAlumnosSession) {
            $respuestas
                ->join('grupo_users', 'grupo_users.user_id', "=", "user_profiles.user_id")
                ->join('grupo_profesor', "grupo_profesor.grupo_id", "=", "grupo_users.grupo_id")
                ->where("grupo_profesor.user_id", auth()->user()->id);
        }

        $respuestas = $respuestas->get();

        foreach ($respuestas as $respuesta) {
            if ($respuesta->correcta == 0) {
                $respuesta->correcta = "No";
                $valor = $respuesta->puntos_incorrecta;
            } else {
                $respuesta->correcta = "Sí";
                $valor = $respuesta->puntos_correcta;
            }
            $respuesta->pregunta = strip_tags($respuesta->pregunta);
            $respuesta->respuesta = strip_tags($respuesta->respuesta);

            // Si es tipo texto la respuesta son las observaciones
            if ($respuesta->slug == 'texto') {
                $respuesta->respuesta = strip_tags($respuesta->observaciones);
            }

            $valores = array(
                $respuesta->user_id,
                $respuesta->username,
                $respuesta->first_name,
                $respuesta->last_name,
                html_entity_decode(strip_tags($respuesta->pregunta)),
                $respuesta->respuesta,
                $respuesta->correcta,
                $valor,
                $respuesta->slug,
                $respuesta->fecha_intento,
                $respuesta->nombre_test,
                $respuesta->titulo
            );
            $j = 1;
            foreach ($valores as $valor) {
                $sheet->setCellValueByColumnAndRow($j++, $row, $valor);
            }
            $row++;
        }

        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);


        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $file_name = trans('elearning::profesor/admin_lang.list_export_preguntas') .
            "_" . Carbon::now()->format('YmdHis');
        $outPath = storage_path("app/exports/");
        if (!file_exists($outPath)) {
            mkdir($outPath, 0777, true);
        }
        $writer->save($outPath . $file_name . '.xlsx');

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx' . '"');
        header('Cache-Control: max-age=0');

        /*
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        */

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function generateExcelGeneral(Request $request)
    {
        ini_set('memory_limit', '300M');

        if (ob_get_contents()) {
            ob_end_clean();
        }
        set_time_limit(1000);

        $spreadsheet = new Spreadsheet();
        $spreadsheet
            ->getProperties()
            ->setCreator(config('app.name', ''))
            ->setLastModifiedBy(config('app.name', '')) // última vez modificado por
            ->setTitle(trans('elearning::profesor/admin_lang.list_export_asignaturas'))
            ->setSubject(trans('elearning::profesor/admin_lang.list_export_asignaturas'))
            ->setDescription(trans('elearning::profesor/admin_lang.list_export_asignaturas'))
            ->setKeywords(trans('elearning::profesor/admin_lang.list_export_asignaturas'))
            ->setCategory('Informes');

        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle(trans('elearning::profesor/admin_lang.data_excel_asignaturas'));

        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4);

        $sheet->getPageSetup()->setFitToWidth(1);

        $sheet->getHeaderFooter()->setOddHeader('Zona de profesor - Asignaturas');
        $sheet->getHeaderFooter()->setOddFooter('&L&B' .
            $spreadsheet->getProperties()->getTitle() . '&RPágina &P de &N');

        $row = 1;

        /*
        // Ponemos algunos títulos a modo de ejemplo
        $sheet->setCellValueByColumnAndRow(1, $row, "Proyectos de:");
        $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);
        ExcelHelper::cellColor($sheet, 'A'.$row.':B'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(3, $row, " Aduxia");
        $sheet->mergeCellsByColumnAndRow(3, $row, 8, $row);
        ExcelHelper::cellColor($sheet, 'C'.$row.':G'.$row, '92d050');

        $sheet->getStyle('A'.$row.':D'.$row)->getFont()->setSize(14);

        $row++;

        // Segunda linea de información
        $sheet->setCellValueByColumnAndRow(1, $row, "Fecha inicio:");
        $sheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
        $sheet->getStyle('A'.$row.':B'.$row)->getFont()->setBold(true);

        ExcelHelper::cellColor($sheet, 'A'.$row.':G'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(3, $row, Carbon::now()->format("d/m/Y"));
        $sheet->mergeCellsByColumnAndRow(3, $row, 8, $row);
        ExcelHelper::cellColor($sheet, 'C'.$row.':G'.$row, '92d050');

        $sheet->getStyle('A'.$row.':D'.$row)->getFont()->setSize(14);

        $row++;


        //Styles
        $style = array(
            'font' => array('bold' => true,),
            'alignment' => array('horizontal' =>  Alignment::HORIZONTAL_CENTER,),
            'borders' => array(
                'top' => array(
                    'style' => Border::BORDER_THIN,
                ),
            ),
            'fill' => array(
                'type' => Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startcolor' => array(
                    'argb' => 'FFA0A0A0',
                ),
            )
        );
        //Bolds
        $sheet
            ->getStyle('A'.$row.':G'.$row)
            ->applyFromArray($style);

        ExcelHelper::cellColor($sheet,'A'.$row, 'ffc000');

        $sheet->setCellValueByColumnAndRow(1, $row, "Filiación");
        $sheet->mergeCellsByColumnAndRow(1,$row,2,$row);
        ExcelHelper::cellColor($sheet,'B'.$row.':C'.$row, '92d050');

        $sheet->setCellValueByColumnAndRow(3, $row, "Datos basales");
        $sheet->mergeCellsByColumnAndRow(3,$row,6,$row);
        ExcelHelper::cellColor($sheet,'D'.$row.':G'.$row, '00b050');

        $sheet->setCellValueByColumnAndRow(7, $row, "Valoración basal");
        $sheet->mergeCellsByColumnAndRow(7,$row,17,$row);
        ExcelHelper::cellColor($sheet,'H'.$row.':R'.$row, '92d050');

        $sheet->setCellValueByColumnAndRow(18, $row, "Complicaciones");
        $sheet->mergeCellsByColumnAndRow(18,$row,27,$row);
        ExcelHelper::cellColor($sheet,'S'.$row.':AB'.$row, '00b050');

        $row++;
        */

        // Ponemos las cabeceras
        $cabeceras = array(
            trans('users/lang.usuario'),
            trans('users/lang._NOMBRE_USUARIO'),
            trans('users/lang._APELLIDOS_USUARIO'),
            trans('elearning::profesor/admin_lang.asignatura'),
            trans('elearning::profesor/admin_lang.modulo'),
            trans('elearning::profesor/admin_lang.contenido'),
            trans('elearning::profesor/admin_lang.tipo'),
            trans('elearning::profesor/admin_lang.fecha_lectura'),
            trans('elearning::profesor/admin_lang.progress_video'),
            trans('elearning::profesor/admin_lang.nota'),
            trans('elearning::profesor/admin_lang.aprobado'),
        );

        ExcelHelper::autoSizeHeader($sheet, $cabeceras, $row, 'ffc000');
        $row++;

        // Ahora los registros
        $misAlumnosSession = (Session::has('todo_los_alumnos')) ? true : false;

        $elementos = DB::table("track_contenido as tc")
            ->join("contenidos as c", "tc.contenido_id", "c.id")
            ->join("contenidos_translations as ct", function ($join) {
                $join->on('ct.contenido_id', '=', 'c.id')
                    ->where('ct.locale', '=', app()->getLocale());
            })
            ->join("tipo_contenidos_translations as tct", "c.tipo_contenido_id", "tct.tipo_contenido_id")
            ->join("modulos as m", "m.id", "c.modulo_id")
            ->join("modulo_translations as mt", function ($join) {
                $join->on('mt.modulo_id', '=', 'm.id')
                    ->where('mt.locale', '=', app()->getLocale());
            })
            ->join("asignaturas as as", "m.asignatura_id", "as.id")
            ->join("asignatura_translations as a", function ($join) {
                $join->on('m.asignatura_id', '=', 'a.asignatura_id')
                    ->where('a.locale', '=', app()->getLocale());
            })
            ->join("users as u", "tc.user_id", "u.id")
            ->join("user_profiles as up", "u.id", "up.user_id")
            ->leftJoin("track_contenido_evaluacion as tce", function ($join) {
                $join->on('tce.contenido_id', '=', 'c.id');
                $join->on('tce.user_id', '=', 'up.user_id');
            })->leftJoin("track_video as tv", function ($join) {
                $join->on('tv.contenido_id', '=', 'c.id');
                $join->on('tv.user_id', '=', 'up.user_id');
            })->select([
                "u.username", "up.first_name", "up.last_name", "a.titulo as asignatura", "mt.nombre as modulo",
                "as.orden", "m.orden",
                "ct.nombre as contenido", "tct.nombre as tipo", "tc.fecha_lectura as fecha",
                "tv.user_progress as up", "tv.total_video_seconds as tvs", "tce.nota", "tce.aprobado"
            ])
            ->orderBy('u.username', 'ASC')
            ->orderBy('as.orden', 'ASC')
            ->orderBy('asignatura', 'ASC')
            ->orderBy('m.orden', 'ASC')
            ->orderBy('modulo', 'ASC');

        if (!auth()->user()->can("admin-alumnos-all") || !$misAlumnosSession) {
            $elementos
                ->join('grupo_users', 'grupo_users.user_id', "=", "up.user_id")
                ->join('grupo_profesor', "grupo_profesor.grupo_id", "=", "grupo_users.grupo_id")
                ->where("grupo_profesor.user_id", auth()->user()->id);
        }

        if (!auth()->user()->can("admin-asignaturas-all")) {
            $elementos
                ->join('asignatura_profesor', "asignatura_profesor.asignatura_id", "=", "as.id")
                ->where("asignatura_profesor.user_id", auth()->user()->id);
        }


        $elementos = $elementos->get();

        foreach ($elementos as $key => $value) {
            $valores = array(
                $value->username,
                $value->first_name,
                $value->last_name,
                $value->asignatura,
                $value->modulo,
                $value->contenido,
                $value->tipo,
                Carbon::createFromFormat("Y-m-d H:i:s", $value->fecha)->format("d/m/Y H:i:s"),
                (empty($value->up) ? 0 : number_format(($value->up / $value->tvs) * 100)) . "%",
                $value->nota,
                ($value->aprobado == '1') ? trans('general/admin_lang.yes') : trans('general/admin_lang.no')
            );

            $j = 1;
            foreach ($valores as $valor) {
                $sheet->setCellValueByColumnAndRow($j++, $row, $valor);
            }
            $row++;
        }

        ExcelHelper::autoSizeCurrentRow($sheet);

        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);


        // Activamos la primera pestaña
        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);
        $file_name = trans('elearning::profesor/admin_lang.list_export_asignaturas') . "_" . Carbon::now()
            ->format('YmdHis');
        $outPath = storage_path("app/exports/");
        if (!file_exists($outPath)) {
            mkdir($outPath, 0777, true);
        }
        $writer->save($outPath . $file_name . '.xlsx');

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $file_name . '.xlsx' . '"');
        header('Cache-Control: max-age=0');

        /*
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        */

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function userStatsAsignatura(Request $request)
    {
        $user = User::find($request->user_id);
        if (empty($user)) {
            abort(404);
        }

        $asignatura = Asignatura::find($request->asignatura_id);
        if (empty($asignatura)) {
            abort(404);
        }

        return $this->userStats($user, $asignatura, null, 'docencia');
    }

    public function userStatsModulo(Request $request)
    {
        $user = User::find($request->user_id);
        if (empty($user)) {
            abort(404);
        }

        $modulo = Modulo::activos()->find($request->modulo_id);
        if (empty($modulo)) {
            abort(404);
        }

        $asignatura = Asignatura::find($modulo->asignatura_id);
        if (empty($asignatura)) {
            abort(404);
        }

        return $this->userStats($user, $asignatura, $modulo, 'docencia');
    }

    public function userStats(User $user, $asignatura = null, $modulo = null, $path = '')
    {
        // JJ: Utilizamos el path para indicar donde volver. Seguramente hay otra manera mejor de hacerlo
        // pero ahora no se me ocurre. Los dos puntos desde donde podemos venir es Docencia (ya sea por asignatura)
        // o por módulo y desde el listado de alumnos
        if (empty($path)) {
            // Si vemos sin path quiere decir que venimos desde listado de alumnos
            $path = 'alumnos';
        }
        $page_title = trans("elearning::profesor/admin_lang.progress_user") . " - " .
            $user->userProfile->first_name . " " . $user->userProfile->last_name;

        $trackAsignaturas = TrackAsignatura::with("asignatura")
            ->with("convocatoria")
            ->where("track_asignatura.user_id", $user->id);

        if (!auth()->user()->can("admin-asignaturas-all")) {
            $trackAsignaturas
                ->join(
                    'asignatura_profesor',
                    "asignatura_profesor.asignatura_id",
                    "=",
                    "track_asignatura.asignatura_id"
                )
                ->where("asignatura_profesor.user_id", auth()->user()->id);
        }

        $trackAsignaturas = $trackAsignaturas->get();

        return view(
            "elearning::profesor.admin_user_stats",
            compact(
                'page_title',
                'trackAsignaturas',
                'user',
                'asignatura',
                'modulo',
                'path'
            )
        );
    }

    public function userScopeStats(User $user, $scope, $asignatura_id, $convocatoria_id)
    {
        $profesorHelper = new ProfesorHelper();
        $profesorHelper->setInternalVariables($user, $scope, $asignatura_id, $convocatoria_id);
        $stats = $profesorHelper->getStatsPartial();

        return view("elearning::profesor.partials.admin_" . $scope . "_partial", $stats);
    }
}
