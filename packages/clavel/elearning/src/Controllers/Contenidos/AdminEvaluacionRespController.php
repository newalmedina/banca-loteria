<?php

namespace Clavel\Elearning\Controllers\Contenidos;

use App\Http\Controllers\AdminController;
use Clavel\Elearning\Models\Pregunta;
use Clavel\Elearning\Models\Respuesta;
use Clavel\Elearning\Models\RespuestaTranslation;
use App\Services\GetLanguage;
use Clavel\Elearning\Requests\PreguntaRequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\DataTables;

class AdminEvaluacionRespController extends AdminController
{
    protected $page_title_icon = '<i class="fa  fa-file-image-o" aria-hidden="true"></i>';

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
    public function index($pregunta_id)
    {
        if (!Auth::user()->can('admin-contenidos-list')) {
            abort(404);
        }
        $pregunta = Pregunta::findOrFail($pregunta_id);
        if (empty($pregunta)) {
            abort(404);
        }

        // Si es texto o numero vamos directo a la pregunta ya que no puede haber multiples
        if ($pregunta->tipo->slug == "texto" || $pregunta->tipo->slug == "numero") {
            // Vemos si ya hay respuestas y en funcion vamos a crear o actualizar

            //Creamos el contenido y asignamos el modulo al que pertenece.
            $respuesta = Respuesta::where('pregunta_id', $pregunta_id)->first();

            if (empty($respuesta)) {
                return Redirect::to('admin/preguntas/' . $pregunta_id . '/respuestas/create');
            } else {
                return Redirect::to('admin/preguntas/' . $pregunta_id . '/respuestas/' . $respuesta->id . '/edit');
            }
        }

        $page_title = trans("elearning::preguntas/admin_lang.respuestas") . " " .
            substr(strip_tags($pregunta->nombre), 0, 10);
        return view('elearning::respuestas.admin_index', compact('page_title', 'pregunta'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function getData($pregunta_id)
    {
        $locale = config('app.default_locale');
        $preguntas = Respuesta::select(
            array(
                'respuestas.id',
                'respuestas.pregunta_id',
                'respuestas.activa',
                'respuestas.correcta',
                'respuesta_translations.nombre',
            )
        )->join('respuesta_translations', function ($join) use ($locale) {
            $join->on('respuesta_translations.respuesta_id', '=', 'respuestas.id');
            $join->on('respuesta_translations.locale', '=', DB::raw("'" . $locale . "'"));
        })->where("respuestas.pregunta_id", $pregunta_id);

        return Datatables::of($preguntas)
            ->editColumn(
                'activa',
                '@if(Auth::user()->can("admin-contenidos-create"))
                    @if($activa)
                        <button class="btn btn-success btn-sm"
                        onclick="javascript:changeStatus
                        (\'{{ url(\'admin/preguntas/\'.$pregunta_id.\'/respuestas/cambiar_estado/\'.$id.\'\') }}\');"
                        data-content="' . trans('general/admin_lang.descativa') . '"
                        data-placement="right" data-toggle="popover">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                    @else
                        <button class="btn btn-danger btn-sm"
                        onclick="javascript:changeStatus
                        (\'{{ url(\'admin/preguntas/\'.$pregunta_id.\'/respuestas/cambiar_estado/\'.$id.\'\') }}\');"
                         data-content="' . trans('general/admin_lang.activa') . '"
                         data-placement="right" data-toggle="popover">
                            <i class="fa fa-eye-slash" aria-hidden="true"></i>
                        </button>
                    @endif
                @else
                    @if($activa)
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
            ->editColumn(
                'correcta',
                '@if(Auth::user()->can("admin-contenidos-create"))
                    @if($correcta)
                        <button class="btn btn-success btn-sm"
                        onclick="javascript:changeStatus
                        (\'{{ url(\'admin/preguntas/\'.$pregunta_id.
                            \'/respuestas/cambiar_estado_correcta/\'.$id.\'\') }}\');"
                        data-content="' . trans('elearning::preguntas/admin_lang.marcar_incorrecta') . '"
                        data-placement="right" data-toggle="popover">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                    @else
                        <button class="btn btn-danger btn-sm"
                        onclick="javascript:changeStatus
                        (\'{{ url(\'admin/preguntas/\'.$pregunta_id.
                            \'/respuestas/cambiar_estado_correcta/\'.$id.\'\') }}\');"
                        data-content="' . trans('elearning::preguntas/admin_lang.marcar_correcta') . '"
                         data-placement="right" data-toggle="popover">
                            <i class="fa fa-eye-slash" aria-hidden="true"></i>
                        </button>
                    @endif
                @else
                    @if($correcta)
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
                    onclick="javascript:window.location=\'{{ url(\'admin/preguntas/\'.$pregunta_id.
                        \'/respuestas/\'.$id.\'/edit\') }}\';"
                    data-content="' . trans('general/admin_lang.modificar') . '"
                    data-placement="right" data-toggle="popover">
                    <i class="fa fa-pencil" aria-hidden="true"></i></button>
                @endif
                @if(Auth::user()->can("admin-contenidos-delete"))
                    <button class="btn btn-danger btn-sm"
                    onclick="javascript:deleteElement(\'{{ url(\'admin/preguntas/\'.$pregunta_id.
                        \'/respuestas/\'.$id.\'/destroy\') }}\');"
                    data-content="' . trans('general/admin_lang.borrar') . '"
                    data-placement="left" data-toggle="popover">
                    <i class="fa fa-trash" aria-hidden="true"></i></button>
                @endif
                ')
            ->removeColumn('id')
            ->removeColumn('pregunta_id')
            ->removeColumn('puntos_correcta')
            ->removeColumn('puntos_incorrecta')
            ->removeColumn('locale')
            ->removeColumn('comentario')
            ->removeColumn('tipo_pregunta_id')
            ->removeColumn('orden')
            ->removeColumn('translations')
            ->rawColumns(['activa', 'correcta', 'nombre', 'actions'])
            ->make();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($pregunta_id)
    {
        //Comprobamos si tiene acceso a crear contenidos, si no devolvemos error 404.
        if (!Auth::user()->can('admin-contenidos-create')) {
            abort(404);
        }
        //Cargamos el módulo
        $pregunta = Pregunta::findOrFail($pregunta_id);

        //Creamos el contenido y asignamos el modulo al que pertenece.
        $respuesta = new Respuesta();
        $respuesta->pregunta_id = $pregunta->id;

        //Creamos el formulario con la ruta a la que vamos a apuntar y todos sus datos.
        $form_data = array(
            'route' => array('admin.preguntas.respuestas.store', $pregunta_id),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal',
            'files' => true
        );
        $page_title = trans("elearning::preguntas/admin_lang.nueva_respuesta");

        // Idioma
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getTranslations($respuesta);

        // Si es texto o numero vamos directo a la pregunta ya que no puede haber multiples
        if ($pregunta->tipo->slug == "texto" || $pregunta->tipo->slug == "numero") {
            return view(
                'elearning::respuestas.admin_edit_texto',
                compact(
                    'page_title',
                    'pregunta',
                    'form_data',
                    'respuesta',
                    'a_trans'
                )
            );
        } else {
            return view(
                'elearning::respuestas.admin_edit',
                compact(
                    'page_title',
                    'pregunta',
                    'form_data',
                    'respuesta',
                    'a_trans'
                )
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(Request $request, $pregunta_id)
    {
        if (!Auth::user()->can('admin-contenidos-create')) {
            abort(404);
        }

        $respuesta = new Respuesta();
        $this->saveContenido($request, $respuesta);

        return Redirect::to('admin/preguntas/' . $pregunta_id . '/respuestas/' . $respuesta->id . '/edit')
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function saveContenido(Request $request, $respuesta)
    {
        //Miramos cuantas respuestas tenemos
        if (is_null($respuesta->id)) {
            $cuantas = Respuesta::where('pregunta_id', '=', $request->input("pregunta_id"))->count();
        } else {
            $cuantas = Respuesta::where('pregunta_id', '=', $request->input("pregunta_id"))
                ->where('id', '!=', $respuesta->id)->count();
        }
        $cuantas = $cuantas + 1;

        $respuesta->activa = $request->input("activa");
        $respuesta->correcta = $request->input("correcta");
        $respuesta->pregunta_id = $request->input("pregunta_id");
        $respuesta->orden = ($request->input("orden") != "") ?
            $request->input("orden") : $cuantas;
        $respuesta->puntos_correcta = ($request->input("puntos_correcta") != "") ?
            $request->input("puntos_correcta") : 0;
        $respuesta->puntos_incorrecta = ($request->input("puntos_incorrecta") != "") ?
            $request->input("puntos_incorrecta") : 0;

        $this->setParamQuestion($respuesta);

        $respuesta->save();

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = RespuestaTranslation::findOrNew($value["id"]);

            $itemTrans->respuesta_id = $respuesta->id;
            $itemTrans->locale = $key;
            $itemTrans->nombre = is_null($value["nombre"]) ? "" : $value["nombre"];
            $itemTrans->comentario = is_null($value["comentario"]) ? "" : $value["comentario"];
            $itemTrans->save();
        }
    }

    public function setParamQuestion($respuesta)
    {
        // Que tipo de pregunta es?
        // Si es de tipo 1 y esta marcada como correcta, tenemos que marcar las demás
        // preguntas como incorrectas, porque solo se permite una correcta.
        if ($respuesta->pregunta->tipo_pregunta_id == 1 && $respuesta->correcta) {
            $otras_respuestas = Respuesta::where('pregunta_id', '=', $respuesta->pregunta->id)->get();
            foreach ($otras_respuestas as $otra_respuesta) {
                if ($respuesta->id != $otra_respuesta->id) {
                    $otra_respuesta->correcta = 0;
                    $otra_respuesta->save();
                }
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($pregunta_id, $id)
    {
        //Comprobamos si tiene acceso a crear contenidos, si no devolvemos error 404.
        if (!Auth::user()->can('admin-contenidos-create')) {
            abort(404);
        }
        //Cargamos el módulo
        $pregunta = Pregunta::findOrFail($pregunta_id);

        //Creamos el contenido y asignamos el modulo al que pertenece.
        $respuesta = Respuesta::findOrFail($id);

        //Creamos el formulario con la ruta a la que vamos a apuntar y todos sus datos.
        $form_data = array(
            'route' => array(
                'admin.preguntas.respuestas.update',
                $pregunta_id, $respuesta->id
            ),
            'method' => 'PATCH', 'id' => 'formData',
            'class' => 'form-horizontal', 'files' => true
        );
        $page_title = trans("elearning::preguntas/admin_lang.modificar_contenido");

        // Idioma
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getTranslations($respuesta);

        // Si es texto o numero vamos directo a la pregunta ya que no puede haber multiples
        if ($pregunta->tipo->slug == "texto" || $pregunta->tipo->slug == "numero") {
            return view(
                'elearning::respuestas.admin_edit_texto',
                compact(
                    'page_title',
                    'pregunta',
                    'form_data',
                    'respuesta',
                    'a_trans'
                )
            );
        } else {
            return view(
                'elearning::respuestas.admin_edit',
                compact(
                    'page_title',
                    'pregunta',
                    'form_data',
                    'respuesta',
                    'a_trans'
                )
            );
        }
    }

    /**
     * Update the specified resource in storage.
     *

     */
    public function update(Request $request, $pregunta_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-create')) {
            abort(404);
        }

        $respuesta = Respuesta::findorfail($id);
        $this->saveContenido($request, $respuesta);

        return Redirect::to('admin/preguntas/' . $pregunta_id . '/respuestas/' . $respuesta->id . '/edit')
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($pregunta_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-delete')) {
            abort(404);
        }

        $respuesta = Respuesta::findOrFail($id);
        $respuesta->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Pregunta eliminada',
            'id' => $respuesta->id
        ));
    }

    public function setChangeState($pregunta_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-update')) {
            abort(404);
        }

        $respuesta = Respuesta::findOrFail($id);
        $respuesta->activa = !$respuesta->activa;
        return $respuesta->save() ? 1 : 0;
    }

    public function setChangeCorrect($pregunta_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-update')) {
            abort(404);
        }

        $respuesta = Respuesta::findOrFail($id);
        $respuesta->correcta = !$respuesta->correcta;
        $this->setParamQuestion($respuesta);
        return $respuesta->save() ? 1 : 0;
    }
}
