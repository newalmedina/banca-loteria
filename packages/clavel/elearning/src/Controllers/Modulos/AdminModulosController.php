<?php

namespace Clavel\Elearning\Controllers\Modulos;

use Clavel\Elearning\Requests\ModuloRequest;
use Clavel\Elearning\Models\Asignatura;

use App\Http\Controllers\AdminController;
use Clavel\Elearning\Models\Modulo;
use Clavel\Elearning\Models\ModuloTranslation;
use App\Models\StatUserRoute;
use Clavel\Elearning\Models\TipoModulo;
use App\Services\GetLanguage;
use App\Services\StoragePathWork;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yajra\DataTables\DataTables;

class AdminModulosController extends AdminController
{
    private $myServiceSPW;

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-modulos';

        $this->myServiceSPW = new StoragePathWork("modulos");
    }

    public function index($asignatura_id)
    {
        if (!Auth::user()->can('admin-modulos-list')) {
            abort(404);
        }
        $asignatura = Asignatura::findOrFail($asignatura_id);
        $page_title = trans("elearning::modulos/admin_lang.modulos") . " " . $asignatura->titulo;
        return view('elearning::modulos.admin_index', compact('page_title', 'asignatura'));
    }

    public function getData($asignatura_id)
    {
        $locale = config('app.default_locale');

        $modulos = Modulo::select(
            array(
                'modulos.id',
                'modulos.asignatura_id',
                'modulos.orden',
                'modulos.activo',
                'modulo_translations.nombre',
                'modulo_translations.url_amigable'
            )
        )->join('modulo_translations', function ($join) use ($locale) {
            $join->on('modulo_translations.modulo_id', '=', 'modulos.id');
            $join->on('modulo_translations.locale', '=', DB::raw("'" . $locale . "'"));
        })->where("asignatura_id", $asignatura_id)
            ->orderBy("modulos.orden");

        return Datatables::of($modulos)
            ->editColumn('orden', function ($row) {
                return "<div style='text-align:center;'
                class='info-move' data-value='" . $row->id . "'>
                <i class='fa fa-arrows text-primary' style='font-size: 20px;'></i></div>";
            })
            ->editColumn('nombre', function ($row) {
                return $row->getTranslatedNombre();
            })
            ->editColumn(
                'activo',
                '@if(Auth::user()->can("admin-modulos-update"))
                        @if($activo)
                            <button class="btn btn-success btn-sm"
                            onclick="javascript:changeStatus
                            (\'{{ url(\'admin/asignaturas/modulos/cambiar_estado/\'.$id.\'\') }}\');"
                            data-content="' . trans('general/admin_lang.descativa') . '" data-placement="right"
                            data-toggle="popover">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm"
                            onclick="javascript:changeStatus
                            (\'{{ url(\'admin/asignaturas/modulos/cambiar_estado/\'.$id.\'\') }}\');"
                            data-content="' . trans('general/admin_lang.activa') . '" data-placement="right"
                            data-toggle="popover">
                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                            </button>
                        @endif
                    @else
                        @if($activo)
                            <button class="btn btn-success btn-sm disabled" data-placement="right">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm disabled" data-placement="right">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @endif
                    @endif'
            )
            ->editColumn('url_amigable', function ($row) {
                return "<a href='/modulos/detalle_modulo/" . $row->getTranslatedURL() . "/" .
                    $row->id . "' target='_blank'>/modulos/detalle_modulo/" .
                    $row->url_amigable . "/" . $row->id . "</a>";
            })
            ->addColumn('grafica', function ($row) {
                $strInfo = '<div class="sparkline" data-color="#00a65a" data-height="20">';
                $strInfo .= implode(",", $this->getSevenDays("/modulos/detalle_modulo/" .
                    $row->url_amigable . "/" . $row->id));
                $strInfo .= '</div>';
                return $strInfo;
            })
            ->addColumn('actions', '
                @if(Auth::user()->can("admin-modulos"))
                    <button class="btn bg-maroon btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/modulos/\'.$id.\'/contenidos\') }}\';"
                    data-content="' . trans('elearning::modulos/admin_lang.contenidos') . '" data-placement="right"
                    data-toggle="popover"><i class="glyphicon glyphicon-th-list"></i></button>
                @endif
                @if(Auth::user()->can("admin-modulos-update"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/asignaturas/\'.
                        $asignatura_id.\'/modulos/\'.$id.\'/edit\') }}\';"
                    data-content="' . trans('general/admin_lang.modificar') . '"
                    data-placement="right" data-toggle="popover">
                    <i class="fa fa-pencil" aria-hidden="true"></i></button>
                @endif
                @if(Auth::user()->can("admin-modulos-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement
                    (\'{{ url(\'admin/asignaturas/modulos/\'.$id.\'/destroy\') }}\');"
                    data-content="' . trans('general/admin_lang.borrar') . '"
                    data-placement="left" data-toggle="popover">
                    <i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                ')
            ->removeColumn('id')
            ->removeColumn('asignatura_id')
            ->removeColumn('descripcion')
            ->removeColumn('coordinacion')
            ->rawColumns(['orden', 'activo', 'nombre', 'translations', 'url_amigable', 'grafica', 'actions'])
            ->make();
    }

    private function getSevenDays($url)
    {
        $a_sevendays = [];
        $date_ini = Carbon::today();
        $date_ini = $date_ini->addDays(-6);

        for ($nX = 0; $nX <= 6; $nX++) {
            // Obtengo el total para el día
            $a_sevendays[$nX] =  StatUserRoute::select('ipuser')
                ->distinct('ipuser')
                ->where("dateaccess", "=", new Carbon($date_ini))
                ->where("route", "=", $url)
                ->get()
                ->count();
            $date_ini->addDay();
        }

        return $a_sevendays;
    }

    public function create($asignatura_id)
    {
        if (!Auth::user()->can('admin-modulos-create')) {
            abort(404);
        }
        $asignatura = Asignatura::findOrFail($asignatura_id);
        $modulo = new Modulo();
        $modulo->asignatura_id = $asignatura->id;
        $modulos = Modulo::activos()->where("asignatura_id", $asignatura_id)->get();
        $form_data = array(
            'route' => array(
                'admin.asignaturas.modulos.store', $asignatura->id
            ),
            'method' => 'POST', 'id' => 'formData', 'class' => 'form-horizontal', 'files' => true
        );
        $page_title = trans("elearning::modulos/admin_lang.nuevo_modulos");

        $tipo_modulos = TipoModulo::all();

        // Idioma
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getTranslations($modulo);

        return view('elearning::modulos.admin_edit', compact(
            'page_title',
            'modulo',
            'form_data',
            'asignatura',
            'modulos',
            'a_trans',
            'tipo_modulos'
        ));
    }

    public function store(ModuloRequest $request, $asignatura_id)
    {
        if (!Auth::user()->can('admin-modulos-create')) {
            abort(404);
        }

        $modulo = new Modulo();
        $this->saveModulo($request, $modulo);

        return Redirect::to('admin/asignaturas/' . $asignatura_id . "/modulos/" . $modulo->id . "/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function edit($asignatura_id, $id)
    {
        if (!Auth::user()->can('admin-modulos-update')) {
            abort(404);
        }
        $asignatura = Asignatura::findOrFail($asignatura_id);
        $modulo = Modulo::findOrFail($id);
        $modulos = Modulo::activos()->where("asignatura_id", $asignatura_id)
            ->where("id", '<>', $id)->get();
        $form_data = array(
            'route' => array(
                'admin.asignaturas.modulos.update',
                $asignatura->id, $modulo->id
            ),
            'method' => 'PATCH', 'id' => 'formData', 'class' => 'form-horizontal', 'files' => true
        );
        $page_title = trans("elearning::modulos/admin_lang.modify_modulos");

        $tipo_modulos = TipoModulo::all();

        // Idioma
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getTranslations($modulo);

        return view('elearning::modulos.admin_edit', compact(
            'page_title',
            'modulo',
            'form_data',
            'asignatura',
            'modulos',
            'a_trans',
            'tipo_modulos'
        ));
    }

    public function update(ModuloRequest $request, $asignatura_id, $id)
    {
        if (!Auth::user()->can('admin-modulos-create')) {
            abort(404);
        }

        $modulo = Modulo::findOrFail($id);
        $this->saveModulo($request, $modulo);

        return Redirect::to('admin/asignaturas/' . $modulo->asignatura_id . "/modulos/" . $modulo->id . "/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('admin-modulos-delete')) {
            abort(404);
        }

        $modulo = Modulo::findOrFail($id);
        $modulo->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Módulo eliminada',
            'id' => $modulo->id
        ));
    }

    public function setChangeState($id)
    {
        if (!Auth::user()->can('admin-modulos-update')) {
            abort(404);
        }

        $modulo = Modulo::findOrFail($id);
        $modulo->activo = !$modulo->activo;
        return $modulo->save() ? 1 : 0;
    }

    public function saveModulo(Request $request, Modulo $modulo)
    {
        $modulo->activo = $request->input("activo");
        $modulo->fondo = $request->input("fondo");
        $modulo->asignatura_id = $request->input("asignatura_id");
        $modulo->obligatorio_id = ($request->input("obligatorio_id") != '') ?
            $request->input("obligatorio_id") : null;
        $modulo->tipo_modulo_id = ($request->input("tipo_modulo_id") != '') ?
            $request->input("tipo_modulo_id") : null;
        $modulo->puntua = $request->input("puntua");
        $modulo->peso = $request->input("peso");
        $modulo->orden = ($request->input("orden") != '') ? $request->input("orden") : 999;
        $modulo->save();

        $files = $request->file('myfile');

        if ($request->input("delete_photo") == '1') {
            $modulo->image = "";
            if ($modulo->image != '') {
                $this->myServiceSPW->deleteFile($modulo->image, "/" . $modulo->asignatura_id);
            }
        }

        if (!is_null($files)) {
            foreach ($files as $file) {
                try {
                    if (!is_null($file)) {
                        $filename = $this->myServiceSPW->saveFile($file, "/" . $modulo->asignatura_id);
                        $modulo->image = $filename;
                    }
                } catch (NotFoundHttpException $e) {
                    return redirect("admin/asignaturas/" .
                        $modulo->asignatura_id . "/modulos/create")
                        ->with('error', trans('menu/admin_lang.errorediciion'));
                }
            }
        }

        $modulo->save();

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = ModuloTranslation::findOrNew($value["id"]);

            $itemTrans->modulo_id = $modulo->id;
            $itemTrans->locale = $key;
            $itemTrans->nombre = $value["nombre"];
            $itemTrans->url_amigable = str_slug($value["nombre"]);
            $itemTrans->descripcion = $value["descripcion"];
            $itemTrans->coordinacion = $value["coordinacion"];
            $itemTrans->save();
        }
    }

    public function reordenar(Request $request, $asignatura_id)
    {
        $array = explode(",", $request->input("orden", ''));

        if (!is_array($array)) {
            return "NOK";
        }

        try {
            $nX = 1;
            foreach ($array as $modulo_id) {
                $modulo = Modulo::findOrFail($modulo_id);
                $modulo->orden = $nX;
                $modulo->save();
                $nX++;
            }

            return "OK";
        } catch (\Exception $e) {
            return "NOK";
        }
    }
}
