<?php

namespace Clavel\Elearning\Controllers\Asignaturas;

use App\Models\User;
use Clavel\Elearning\Requests\AsignaturaRequest;
use Clavel\Elearning\Models\Asignatura;
use Clavel\Elearning\Models\AsignaturaTranslation;
use Clavel\Elearning\Models\Curso;
use App\Models\StatUserRoute;

use App\Http\Controllers\AdminController;
use Clavel\Elearning\Services\CloneAsignaturaService;
use App\Services\GetLanguage;
use App\Services\StoragePathWork;
use Carbon\Carbon;
use Clavel\Elearning\Services\ExportImportAsignaturaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Yajra\DataTables\DataTables;

class AdminAsignaturasController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa fa-leanpub" aria-hidden="true"></i>';

    private $myServiceSPW;

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-asignaturas';
        $this->myServiceSPW = new StoragePathWork("asignaturas");
    }

    public function index()
    {
        if (!Auth::user()->can('admin-asignaturas-list')) {
            abort(404);
        }

        $page_title = trans("elearning::asignaturas/admin_lang.asignaturas");

        return view('elearning::asignaturas.admin_index', compact('page_title'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function getData()
    {
        $locale = config('app.default_locale');

        $asignaturas = Asignatura::select(
            array(
                'asignaturas.id',
                'asignaturas.activo',
                'asignaturas.orden',
                'asignatura_translations.titulo',
                'asignatura_translations.url_amigable',
            )
        )->join('asignatura_translations', function ($join) use ($locale) {
            $join->on('asignatura_translations.asignatura_id', '=', 'asignaturas.id');
            $join->on('asignatura_translations.locale', '=', DB::raw("'" . $locale . "'"));
        })->orderBy("orden");

        return Datatables::of($asignaturas)
            ->editColumn('orden', function ($row) {
                return "<div style='text-align:center;' class='info-move' data-value='" . $row->id
                    . "'><i class='fa fa-arrows text-primary' style='font-size: 20px;'></i></div>";
            })
            ->editColumn(
                'activo',
                '@if(Auth::user()->can("admin-asignaturas-update"))
                        @if($activo)
                            <button class="btn btn-success btn-sm"
                            onclick="javascript:changeStatus
                                (\'{{ url(\'admin/asignaturas/cambiar_estado/\'.$id.\'\') }}\');"
                            data-content="' . trans('general/admin_lang.descativa') . '"
                            data-placement="right"
                            data-toggle="popover">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        @else
                            <button class="btn btn-danger btn-sm"
                            onclick="javascript:changeStatus
                            (\'{{ url(\'admin/asignaturas/cambiar_estado/\'.$id.\'\') }}\');"
                            data-content="' . trans('general/admin_lang.activa') . '"
                            data-placement="right" data-toggle="popover">
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
                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                            </button>
                        @endif
                    @endif'
            )
            ->editColumn('url_amigable', function ($row) {
                return "<a href='/asignaturas/detalle/" . $row->getTranslatedURL() . "/" .
                    $row->id . "' target='_blank'>/asignaturas/detalle/" . $row->url_amigable . "</a>";
            })
            ->editColumn('titulo', function ($row) {
                return $row->getTranslatedTitulo();
            })
            ->addColumn('grafica', function ($row) {
                $strInfo = '<div class="sparkline" data-color="#00a65a" data-height="20">';
                $strInfo .= implode(",", $this->getSevenDays("/asignaturas/detalle/" .
                    $row->url_amigable . "/" . $row->id));
                $strInfo .= '</div>';
                return $strInfo;
            })
            ->addColumn('actions', '
                @if(Auth::user()->can("admin-modulos"))
                    <button class="btn bg-maroon btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/asignaturas/\'.$id.\'/modulos\') }}\';"
                    data-content="' . trans('elearning::asignaturas/admin_lang.modulos') . '"
                    data-placement="right" data-toggle="popover">
                    <i class="glyphicon glyphicon-th-list" aria-hidden="true"></i></button>
                @endif
                @if(Auth::user()->can("admin-asignaturas-update"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/asignaturas/\'.$id.\'/edit\') }}\';"
                    data-content="' . trans('general/admin_lang.modificar') . '"
                    data-placement="left"
                    data-toggle="popover"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                @endif
                @if(Auth::user()->can("admin-asignaturas-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement
                    (\'{{ url(\'admin/asignaturas/\'.$id.\'/destroy\') }}\');"
                    data-content="' . trans('general/admin_lang.borrar') . '"
                    data-placement="left" data-toggle="popover">
                    <i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                @if(Auth::user()->can("admin-asignaturas-create"))
                    <button class="btn bg-purple btn-sm"
                    onclick="javascript:cloneElement(\'{{ url(\'admin/asignaturas/\'.$id.\'/clonelesson\') }}\');"
                    data-content="' . trans('elearning::asignaturas/admin_lang.cloneasignatura') . '"
                    data-placement="left" data-toggle="popover">
                    <i class="fa fa-copy" aria-hidden="true"></i></button>
                    <button class="btn bg-aqua btn-sm"
                    onclick="javascript:exportElement
                    (\'{{ url(\'admin/asignaturas/\'.$id.\'/exportarasignatura\') }}\');"
                    data-content="' . trans('elearning::asignaturas/admin_lang.exportarasignatura') . '"
                    data-placement="left" data-toggle="popover">
                    <i class="fa fa-download" aria-hidden="true"></i></button>
                @endif

                ')
            ->removeColumn('id')
            ->removeColumn('image')
            ->removeColumn('breve')
            ->removeColumn('descripcion')
            ->removeColumn('creditos')
            ->removeColumn('academico')
            ->removeColumn('caracteristica')
            ->removeColumn('plazas')
            ->removeColumn('admision')
            ->removeColumn('coordinacion')
            ->removeColumn('estudiantes')
            ->rawColumns(['orden', 'activo', 'url_amigable', 'grafica', 'actions'])
            ->make();
    }

    private function getSevenDays($url)
    {
        $a_sevendays = [];
        $date_ini = Carbon::today();
        $date_ini = $date_ini->addDays(-6);

        for ($nX = 0; $nX <= 6; $nX++) {
            // Obtengo el total para el día
            $a_sevendays[$nX] = StatUserRoute::select('ipuser')
                ->distinct('ipuser')
                ->where("dateaccess", "=", new Carbon($date_ini))
                ->where("route", "=", $url)
                ->get()
                ->count();

            $date_ini->addDay();
        }

        return $a_sevendays;
    }

    public function create()
    {
        if (!Auth::user()->can('admin-asignaturas-create')) {
            abort(404);
        }
        $asignatura = new Asignatura();
        $form_data = array(
            'route' => array('admin.asignaturas.store'),
            'method' => 'POST', 'id' => 'formData', 'class' => 'form-horizontal', 'files' => true
        );
        $page_title = trans("elearning::asignaturas/admin_lang.nuevo_pages");

        // Todas las asignaturas
        $asignaturas = Asignatura::active()->get();
        // Idioma
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getTranslations($asignatura);

        // Cursos
        $cursos = Curso::active()->get();

        // Leemos los usuarios que pueden ser profesores
        $permission_name = "admin";
        $profesores = User::whereHas('roles', function ($q) use ($permission_name) {
            $q->whereHas('permissions', function ($q) use ($permission_name) {
                $q->where('name', $permission_name);
            });
        })->get();

        return view('elearning::asignaturas.admin_edit', compact(
            'page_title',
            'asignatura',
            'asignaturas',
            'form_data',
            'a_trans',
            'cursos',
            'profesores'
        ));
    }

    public function store(AsignaturaRequest $request)
    {
        if (!Auth::user()->can('admin-asignaturas-create')) {
            abort(404);
        }

        $asignatura = new Asignatura();
        $this->saveAsignatura($request, $asignatura);

        $sel_cursos = $request->input('sel_cursos');
        $asignatura->cursoPivot()->detach();
        if (!is_null($request->input('sel_cursos'))) {
            $asignatura->cursoPivot()->sync($sel_cursos);
        }

        // Actulizamos los profesores vinculados a esta asignatura
        $sel_profesores = $request->input('sel_profesores');
        $asignatura->profesorPivot()->detach();
        if (!is_null($request->input('sel_profesores'))) {
            $asignatura->profesorPivot()->sync($sel_profesores);
        }

        return Redirect::to('admin/asignaturas/' . $asignatura->id . "/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function edit($id)
    {
        if (!Auth::user()->can('admin-asignaturas-update')) {
            abort(404);
        }

        $asignatura = Asignatura::find($id);
        $form_data = array('route' => array(
            'admin.asignaturas.update',
            $asignatura->id
        ), 'method' => 'PATCH', 'id' => 'formData', 'class' => 'form-horizontal', 'files' => true);
        $page_title = trans("elearning::asignaturas/admin_lang.modify_page");

        // Idioma
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getTranslations($asignatura);

        // Todas las asignaturas
        $asignaturas = Asignatura::active()->where("id", '<>', $id)->get();

        // Cursos
        $cursos = Curso::active()->get();

        // Leemos los usuarios que pueden ser profesores
        $permission_name = "admin";
        $profesores = User::whereHas('roles', function ($q) use ($permission_name) {
            $q->whereHas('permissions', function ($q) use ($permission_name) {
                $q->where('name', $permission_name);
            });
        })->get();

        return view('elearning::asignaturas.admin_edit', compact(
            'page_title',
            'asignatura',
            'asignaturas',
            'form_data',
            'a_trans',
            'cursos',
            'profesores'
        ));
    }

    public function update(AsignaturaRequest $request, $id)
    {
        if (!Auth::user()->can('admin-asignaturas-update')) {
            abort(404);
        }

        $asignatura = Asignatura::findOrFail($id);
        $this->saveAsignatura($request, $asignatura);

        $sel_cursos = $request->input('sel_cursos');
        $asignatura->cursoPivot()->detach();
        if (!is_null($request->input('sel_cursos'))) {
            $asignatura->cursoPivot()->sync($sel_cursos);
        }

        // Actulizamos los profesores vinculados a esta asignatura
        $sel_profesores = $request->input('sel_profesores');
        $asignatura->profesorPivot()->detach();
        if (!is_null($request->input('sel_profesores'))) {
            $asignatura->profesorPivot()->sync($sel_profesores);
        }

        return Redirect::to('admin/asignaturas/' . $asignatura->id . "/edit")
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('admin-asignaturas-delete')) {
            abort(404);
        }

        $asignatura = Asignatura::findOrFail($id);
        $asignatura->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Asignatura eliminada',
            'id' => $asignatura->id
        ));
    }

    public function setChangeState($id)
    {
        if (!Auth::user()->can('admin-asignaturas-update')) {
            abort(404);
        }

        $asignatura = Asignatura::findOrFail($id);
        $asignatura->activo = !$asignatura->activo;
        return $asignatura->save() ? 1 : 0;
    }

    private function saveAsignatura(Request $request, Asignatura $asignatura)
    {
        $asignatura->activo = $request->input("activo", false);
        $asignatura->requiere_codigo = $request->input("requiere_codigo", false);
        $asignatura->obligatorio_id = $request->input("obligatorio_id", null);
        $asignatura->save();

        $files = $request->file('myfile');

        if ($request->input("delete_photo") == '1') {
            $asignatura->image = "";
            if ($asignatura->image != '') {
                $this->myServiceSPW->deleteFile($asignatura->image, "/" . $asignatura->id);
            }
        }

        if (!is_null($files)) {
            foreach ($files as $file) {
                try {
                    if (!is_null($file)) {
                        $filename = $this->myServiceSPW->saveFile($file, "/" . $asignatura->id);
                        $asignatura->image = $filename;
                    }
                } catch (NotFoundHttpException $e) {
                    return redirect("admin/asignaturas/create")
                        ->with('error', trans('basic::menu/admin_lang.errorediciion'));
                }
            }
        }

        $asignatura->save();

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = AsignaturaTranslation::findOrNew($value["id"]);
            $itemTrans->asignatura_id = $asignatura->id;
            $itemTrans->locale = $key;
            $itemTrans->titulo = $value["titulo"];
            $itemTrans->url_amigable = str_slug($value["titulo"]);
            $itemTrans->breve = $value["breve"];
            $itemTrans->descripcion = $value["descripcion"];
            $itemTrans->creditos = $value["creditos"];
            $itemTrans->academico = $value["academico"];
            $itemTrans->caracteristica = $value["caracteristica"];
            $itemTrans->plazas = $value["plazas"];
            $itemTrans->admision = $value["admision"];
            $itemTrans->coordinacion = $value["coordinacion"];
            $itemTrans->estudiantes = $value["estudiantes"];
            $itemTrans->save();
        }
    }

    public function clonelesson($id)
    {
        if (!Auth::user()->can('admin-asignaturas-create')) {
            app()->abort(403);
        }
        $sessionClone = new CloneAsignaturaService($id);
        if (!$sessionClone->clonar()) {
            return Response::json(array('success' => false, 'msg' => 'Error clonada', 'id' => $id));
        }
        return Response::json(array('success' => true, 'msg' => 'Asignatura clonada', 'id' => $id));
    }

    public function exportarAsignatura($id)
    {
        if (!Auth::user()->can('admin-asignaturas-create')) {
            app()->abort(403);
        }
        $exportAsignatura = new ExportImportAsignaturaService();

        return $exportAsignatura->export($id);
    }

    public function importarAsignatura(Request $request)
    {
        if (!Auth::user()->can('admin-asignaturas-create')) {
            app()->abort(403);
        }

        $file = $request->file('plantilla');
        $res = ["result" => false];

        if (!empty($file)) {
            $exportAsignatura = new ExportImportAsignaturaService();

            $res["result"] =  $exportAsignatura->import($file);
        }

        return response()->json($res);
    }

    public function reordenar(Request $request)
    {
        $array = explode(",", $request->input("orden", ''));

        if (!is_array($array)) {
            return "NOK";
        }

        try {
            $nX = 1;
            for ($i = 0; $i < count($array); $i++) {
                $asinatura = Asignatura::findOrFail($array[$i]);
                $asinatura->orden = $nX;
                $asinatura->save();
                $nX++;
            }

            return "OK";
        } catch (\Exception $e) {
            return "NOK";
        }
    }
}
