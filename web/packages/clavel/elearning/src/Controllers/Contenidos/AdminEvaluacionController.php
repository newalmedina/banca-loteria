<?php

namespace Clavel\Elearning\Controllers\Contenidos;

use App\Http\Controllers\AdminController;
use Clavel\Elearning\Models\Contenido;
use Clavel\Elearning\Models\GrupoPregunta;
use Clavel\Elearning\Models\Pregunta;
use Clavel\Elearning\Models\PreguntaTranslation;
use Clavel\Elearning\Models\Respuesta;
use Clavel\Elearning\Models\RespuestaTranslation;
use Clavel\Elearning\Models\TipoPregunta;
use App\Services\GetLanguage;
use Clavel\Elearning\Models\ContenidoEvaluacion;

use Clavel\Elearning\Requests\PreguntaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\DataTables;

class AdminEvaluacionController extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-contenidos';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($contenido_id)
    {
        if (!Auth::user()->can('admin-contenidos-list')) {
            abort(404);
        }
        $contenido = Contenido::findOrFail($contenido_id);

        $page_title = trans("elearning::contenidos/admin_lang.questions") . " " . $contenido->nombre;

        return view('elearning::preguntas.admin_index', compact('page_title', 'contenido'));
    }

    public function getData($contenido_id)
    {
        $locale = config('app.default_locale');
        $contenidos = Pregunta::select(
            array(
                'preguntas.id',
                'preguntas.contenido_id',
                'preguntas.tipo_pregunta_id',
                'preguntas.activa',
                'preguntas.obligatoria',
                'pregunta_translations.nombre',
                DB::raw("COALESCE(" . env('DB_PREFIX') . "grupos_preguntas.titulo, '') AS grupo"),
                DB::raw("COALESCE(" . env('DB_PREFIX') . "grupos_preguntas.color, '') AS color")
            )
        )->join('pregunta_translations', function ($join) use ($locale) {
            $join->on('pregunta_translations.pregunta_id', '=', 'preguntas.id');
            $join->on('pregunta_translations.locale', '=', DB::raw("'" . $locale . "'"));
        })
            ->leftJoin('grupos_preguntas', 'preguntas.grupo_pregunta_id', '=', 'grupos_preguntas.id')
            ->where("preguntas.contenido_id", $contenido_id)->orderBy('orden', 'asc');

        return Datatables::of($contenidos)
            ->editColumn(
                'activa',
                '@if(Auth::user()->can("admin-contenidos-create"))
                    @if($activa)
                        <button class="btn btn-success btn-sm"
                        onclick="javascript:changeStatus
                        (\'{{ url(\'admin/contenidos/\'.$contenido_id.\'/preguntas/cambiar_estado/\'.
                            $id.\'\') }}\');"
                        data-content="' . trans('general/admin_lang.descativa') . '"
                        data-placement="right"
                        data-toggle="popover">
                            <i class="fa fa-eye"></i>
                        </button>
                    @else
                        <button class="btn btn-danger btn-sm"
                        onclick="javascript:changeStatus
                        (\'{{ url(\'admin/contenidos/\'.$contenido_id.\'/preguntas/cambiar_estado/\'.
                            $id.\'\') }}\');"
                        data-content="' . trans('general/admin_lang.activa') . '"
                        data-placement="right" data-toggle="popover">
                            <i class="fa fa-eye-slash"></i>
                        </button>
                    @endif
                @else
                    @if($activa)
                        <button class="btn btn-success btn-sm disabled" data-placement="right">
                            <i class="fa fa-eye"></i>
                        </button>
                    @else
                        <button class="btn btn-danger btn-sm disabled" data-placement="right">
                            <i class="fa fa-eye"></i>
                        </button>
                    @endif
                @endif'
            )
            ->editColumn(
                'obligatoria',
                '@if(Auth::user()->can("admin-contenidos-create"))
                    @if($obligatoria)
                        <button class="btn btn-success btn-sm"
                        onclick="javascript:changeStatus
                        (\'{{ url(\'admin/contenidos/\'.$contenido_id.\'/preguntas/cambiar_estado_obligatorio/\'.
                            $id.\'\') }}\');" data-content="' . trans('general/admin_lang.descativa') . '"
                            data-placement="right" data-toggle="popover">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                    @else
                        <button class="btn btn-danger btn-sm"
                        onclick="javascript:changeStatus
                        (\'{{ url(\'admin/contenidos/\'.$contenido_id.\'/preguntas/cambiar_estado_obligatorio/\'.
                            $id.\'\') }}\');" data-content="' . trans('general/admin_lang.activa') . '"
                            data-placement="right" data-toggle="popover">
                            <i class="fa fa-eye-slash" aria-hidden="true"></i>
                        </button>
                    @endif
                @else
                    @if($obligatoria)
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
            ->addColumn('actions', '
                @if(Auth::user()->can("admin-contenidos-update"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/contenidos/\'.$contenido_id.\'/preguntas/\'.
                        $id.\'/edit\') }}\';" data-content="' . trans('general/admin_lang.modificar') . '"
                        data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button>
                @endif
                @if(Auth::user()->can("admin-contenidos-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement(\'{{ url(\'admin/contenidos/\'.$contenido_id.\'/preguntas/\'.
                        $id.\'/destroy\') }}\');" data-content="' . trans('general/admin_lang.borrar') . '"
                        data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                @if(Auth::user()->can("admin-contenidos-create"))
                    <button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'{{ url(\'admin/preguntas/\'.$id.\'/respuestas/\') }}\';"
                    data-content="' . trans('elearning::preguntas/admin_lang.respuestas') . '"
                    data-placement="left" data-toggle="popover">
                    <i class="fa fa-list-ul"></i></button>
                @endif
                ')
            ->editColumn('grupo_pregunta_id', function ($data) {
                $color = empty($data->color) ? "" : 'style="background-color: ' . $data->color . '!important;"';
                return '<a href="#" onclick="javascript:openGrupoPreguntas(\'' .
                    url('admin/contenidos/' . $data->contenido_id . '/preguntas/grupo/' . $data->id) . '\')">'
                    . '<span class="label label-primary" ' . $color . '>'
                    . (empty($data->grupo) ? "?" : $data->grupo)
                    . '</span>'
                    . '</a>';
            })
            ->removeColumn('id')
            ->removeColumn('contenido_id')
            ->removeColumn('locale')
            ->removeColumn('tipo_pregunta_id')
            ->removeColumn('orden')
            ->removeColumn('translations')
            ->removeColumn('grupo')
            ->removeColumn('color')
            ->rawColumns(['activa', 'obligatoria', 'actions', 'nombre', 'grupo_pregunta_id'])
            ->make();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($contenido_id)
    {
        //Comprobamos si tiene acceso a crear contenidos, si no devolvemos error 404.
        if (!Auth::user()->can('admin-contenidos-create')) {
            abort(404);
        }
        //Cargamos el módulo
        $contenido = Contenido::findOrFail($contenido_id);

        //Creamos el contenido y asignamos el modulo al que pertenece.
        $pregunta = new Pregunta();
        $pregunta->contenido_id = $contenido->id;

        $tipos = TipoPregunta::activas()->get();

        $gruposList = GrupoPregunta::where("contenido_id", $contenido_id)
            ->orderBy("titulo")
            ->get();

        //Creamos el formulario con la ruta a la que vamos a apuntar y todos sus datos.
        $form_data = array(
            'route' => array(
                'admin.contenidos.preguntas.store', $contenido_id
            ),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal',
            'files' => true
        );
        $page_title = trans("elearning::contenidos/admin_lang.nuevo_contenido");

        // Idioma
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getTranslations($pregunta);

        return view(
            'elearning::preguntas.admin_edit',
            compact(
                'page_title',
                'pregunta',
                'form_data',
                'contenido',
                'a_trans',
                'tipos',
                'gruposList'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int $contenido_id
     * @return mixed
     */
    public function store(PreguntaRequest $request)
    {
        if (!Auth::user()->can('admin-contenidos-create')) {
            abort(404);
        }

        $pregunta = new Pregunta();

        if ($this->saveContenido($request, $pregunta) == -2) {
            return Redirect::to('admin/contenidos/' . $pregunta->contenido_id .
                '/preguntas/' . $pregunta->id . '/edit')
                ->with('error', trans('elearning::preguntas/admin_lang.preguntas_visibles_error'));
        }

        return Redirect::to('admin/contenidos/' . $pregunta->contenido_id . '/preguntas/' . $pregunta->id . '/edit')
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function saveContenido(Request $request, $pregunta)
    {
        $pregunta->activa = $request->input("activa", "0");
        $pregunta->tipo_pregunta_id = $request->input("tipo_pregunta_id");
        $pregunta->contenido_id = $request->input("contenido_id");
        $pregunta->orden = is_null($request->input("orden")) ? 0 : $request->input("orden", 0);
        $pregunta->grupo_pregunta_id = $request->input("grupo", 0);
        $pregunta->obligatoria = $request->input("obligatoria", "0");
        $pregunta->save();

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = PreguntaTranslation::findOrNew($value["id"]);

            $itemTrans->pregunta_id = $pregunta->id;
            $itemTrans->locale = $key;
            $itemTrans->nombre = empty($value["nombre"]) ? "" : $value["nombre"];
            $itemTrans->save();
        }

        if ($pregunta->obligatoria != 0) {
            $evaluacion = ContenidoEvaluacion::where("contenido_id", '=', $pregunta->contenido_id)->first();
            if (empty($evaluacion)) {
                $evaluacion = new ContenidoEvaluacion();
                $evaluacion->contenido_id = $pregunta->contenido_id;
                $evaluacion->save();
            }

            $preguntas_obligatorias = Pregunta::where("contenido_id", "=", $pregunta->contenido_id)
                ->where("obligatoria", "=", 1)
                ->count();
            if ($preguntas_obligatorias >= $evaluacion->numero_preguntas_visibles &&
                ($evaluacion->numero_preguntas_visibles != 0)
            ) {
                return -2;
            }
        }

        // Ahora si es de tipo texto debemos crear una "respuesta" virtual si no existiese
        $respuesta = Respuesta::where('pregunta_id', '=', $pregunta->id)->first();
        if (empty($respuesta)) {
            $respuesta = new Respuesta();
            $respuesta->activa = true;
            $respuesta->correcta = true;
            $respuesta->pregunta_id = $pregunta->id;
            $respuesta->orden = 0;
            $respuesta->puntos_correcta = 0;
            $respuesta->puntos_incorrecta = 0;
            $respuesta->save();

            foreach ($request->input('userlang') as $key => $value) {
                $itemTrans = RespuestaTranslation::findOrNew($value["id"]);

                $itemTrans->respuesta_id = $respuesta->id;
                $itemTrans->locale = $key;
                $itemTrans->nombre = '';
                $itemTrans->comentario = '';
                $itemTrans->save();
            }
        }

        return 1;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($contenido_id, $pregunta_id)
    {
        //Comprobamos si tiene acceso a crear contenidos, si no devolvemos error 404.
        if (!Auth::user()->can('admin-contenidos-create')) {
            abort(404);
        }
        //Cargamos el módulo
        $contenido = Contenido::findOrFail($contenido_id);

        //Creamos el contenido y asignamos el modulo al que pertenece.
        $pregunta = Pregunta::findOrFail($pregunta_id);

        $tipos = TipoPregunta::activas()->get();

        $gruposList = GrupoPregunta::where("contenido_id", $contenido_id)
            ->orderBy("titulo")
            ->get();

        //Creamos el formulario con la ruta a la que vamos a apuntar y todos sus datos.
        $form_data = array(
            'route' => array(
                'admin.contenidos.preguntas.update',
                $contenido_id, $pregunta->id
            ),
            'method' => 'PATCH', 'id' => 'formData',
            'class' => 'form-horizontal',
            'files' => true
        );
        $page_title = trans("elearning::preguntas/admin_lang.modificar_contenido");

        // Idioma
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getTranslations($pregunta);

        return view(
            'elearning::preguntas.admin_edit',
            compact(
                'page_title',
                'pregunta',
                'form_data',
                'contenido',
                'a_trans',
                'tipos',
                'gruposList'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(PreguntaRequest $request, $contenido_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-update')) {
            abort(404);
        }

        $pregunta = Pregunta::findOrFail($id);

        if ($this->saveContenido($request, $pregunta) == -2) {
            return Redirect::to('admin/contenidos/' . $pregunta->contenido_id .
                '/preguntas/' . $pregunta->id . '/edit')
                ->with('error', trans('elearning::preguntas/admin_lang.preguntas_visibles_error'));
        }

        return Redirect::to('admin/contenidos/' . $contenido_id . '/preguntas/' . $id . '/edit')
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($contenido_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-delete')) {
            abort(404);
        }

        $pregunta = Pregunta::findOrFail($id);
        $pregunta->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Pregunta eliminada',
            'id' => $pregunta->id
        ));
    }

    public function setChangeState($contenido_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-update')) {
            abort(404);
        }

        $pregunta = Pregunta::findOrFail($id);
        $pregunta->activa = !$pregunta->activa;
        return $pregunta->save() ? 1 : 0;
    }

    public function setObligatorio($contenido_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-update')) {
            abort(404);
        }

        $pregunta = Pregunta::findOrFail($id);
        if ($pregunta->obligatoria == 1) {
            $pregunta->obligatoria = !$pregunta->obligatoria;
            return $pregunta->save() ? 1 : 0;
        } else {
            $evaluacion = ContenidoEvaluacion::where("contenido_id", '=', $contenido_id)
                ->first();
            $preguntas_obligatorias = Pregunta::where("contenido_id", "=", $contenido_id)
                ->where("obligatoria", "=", 1)
                ->get();
            if (sizeof($preguntas_obligatorias) >= $evaluacion->numero_preguntas_visibles
            && ($evaluacion->numero_preguntas_visibles != 0)) {
                return "Mas obligatorias que fijas";
            } else {
                $pregunta->obligatoria = !$pregunta->obligatoria;
                return $pregunta->save() ? 1 : 0;
            }
        }
    }


    public function getGruposPreguntas(Request $request, $contenido_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-update')) {
            abort(404);
        }

        $contenido = Contenido::findOrFail($contenido_id);
        $pregunta = Pregunta::findOrFail($id);
        $gruposList = GrupoPregunta::where("contenido_id", $contenido_id)
            ->orderBy("titulo")
            ->get();

        $form_data = array(
            'route' => array('admin.contenidos.preguntas.grupo', $contenido->id, $pregunta->id),
            'method' => 'POST',
            'id' => 'frmDataGrupoPregunta',
            'class' => 'form-horizontal'
        );

        return view(
            'elearning::preguntas.admin_change_group',
            compact(
                'contenido',
                'pregunta',
                'form_data',
                'gruposList'
            )
        );
    }



    public function setGruposPreguntas(Request $request, $contenido_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-update')) {
            app()->abort(403);
        }

        $contenido = Contenido::findOrFail($contenido_id);
        $pregunta = Pregunta::findOrFail($id);

        if (!empty($pregunta)) {
            $pregunta->grupo_pregunta_id = $request->get("grupo", 0);
            return $pregunta->save() ? 1 : 0;
        }

        return 0;
    }
}
