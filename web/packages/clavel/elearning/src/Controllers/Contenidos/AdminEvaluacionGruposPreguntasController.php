<?php

namespace Clavel\Elearning\Controllers\Contenidos;

use App\Http\Controllers\AdminController;
use Clavel\Elearning\Models\Contenido;
use Clavel\Elearning\Models\GrupoPregunta;
use Clavel\Elearning\Requests\GrupoPreguntaRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\DataTables;

class AdminEvaluacionGruposPreguntasController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if (Auth::user() != null && (!Auth::user()->can('admin-contenidos'))) {
            abort(404);
        }
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

        $page_title = trans("elearning::grupos_preguntas/admin_lang.titulo") . " " . $contenido->nombre;

        return view('elearning::grupos_preguntas.admin_index', compact('page_title', 'contenido'));
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
        $grupoPegunta = new GrupoPregunta();
        $grupoPegunta->contenido_id = $contenido->id;

        //Creamos el formulario con la ruta a la que vamos a apuntar y todos sus datos.
        $form_data = array(
            'url' => "admin/contenidos/$contenido_id/grupos_preguntas",
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal',
            'files' => false
        );
        $page_title = trans("elearning::grupos_preguntas/admin_lang.nuevo_grupo");

        return view(
            'elearning::grupos_preguntas.admin_edit',
            compact(
                'page_title',
                'grupoPegunta',
                'form_data',
                'contenido'
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GrupoPreguntaRequest $request)
    {
        if (!Auth::user()->can('admin-contenidos-create')) {
            abort(404);
        }

        $save_and_new = $request->get("save_and_new", false);

        $grupoPregunta = new GrupoPregunta();
        if (!$this->saveContenido($request, $grupoPregunta)) {
            return redirect()->to('admin/contenidos/' . $grupoPregunta->contenido_id .
                '/grupos_preguntas/')
                ->with('error', trans('general/admin_lang.save_ko'));
        }

        if ($save_and_new) {
            return Redirect::to('admin/contenidos/' . $grupoPregunta->contenido_id .
                '/grupos_preguntas/create')
                ->with('success', trans('general/admin_lang.save_ok'));
        } else {
            return Redirect::to('admin/contenidos/' . $grupoPregunta->contenido_id .
                '/grupos_preguntas/' . $grupoPregunta->id . '/edit')
                ->with('success', trans('general/admin_lang.save_ok'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($contenido_id, $id)
    {
        //Comprobamos si tiene acceso a crear contenidos, si no devolvemos error 404.
        if (!Auth::user()->can('admin-contenidos-update')) {
            abort(404);
        }

        //Cargamos el módulo
        $contenido = Contenido::findOrFail($contenido_id);

        //Creamos el contenido y asignamos el modulo al que pertenece.
        $grupoPegunta = GrupoPregunta::findOrFail($id);

        //Creamos el formulario con la ruta a la que vamos a apuntar y todos sus datos.
        $form_data = array(
            'url' => "admin/contenidos/$contenido_id/grupos_preguntas/$grupoPegunta->id",
            'method' => 'PUT',
            'id' => 'formData',
            'class' => 'form-horizontal',
            'files' => false
        );
        $page_title = trans("elearning::grupos_preguntas/admin_lang.update_grupo");

        return view(
            'elearning::grupos_preguntas.admin_edit',
            compact(
                'page_title',
                'grupoPegunta',
                'form_data',
                'contenido'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GrupoPreguntaRequest $request, $contenido_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-update')) {
            abort(404);
        }

        $save_and_new = $request->get("save_and_new", false);

        $grupoPregunta = GrupoPregunta::findOrFail($id);
        if (!$this->saveContenido($request, $grupoPregunta)) {
            return redirect()->to('admin/contenidos/' . $grupoPregunta->contenido_id .
                '/grupos_preguntas/' . $grupoPregunta->id . '/edit')
                ->with('error', trans('general/admin_lang.save_ko'));
        }

        if ($save_and_new) {
            return Redirect::to('admin/contenidos/' . $grupoPregunta->contenido_id .
                '/grupos_preguntas/create')
                ->with('success', trans('general/admin_lang.save_ok'));
        } else {
            return Redirect::to('admin/contenidos/' . $grupoPregunta->contenido_id .
                '/grupos_preguntas/' . $grupoPregunta->id . '/edit')
                ->with('success', trans('general/admin_lang.save_ok'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($contenido_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-delete')) {
            abort(404);
        }

        $grupoPegunta = GrupoPregunta::findOrFail($id);
        $grupoPegunta->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Pregunta eliminada',
            'id' => $grupoPegunta->id
        ));
    }

    public function getData($contenido_id)
    {
        $grupos = GrupoPregunta::select(
            array(
                'id',
                'color',
                'titulo',
            )
        )
            ->delContenido($contenido_id);

        return Datatables::of($grupos)
            ->addColumn('actions', function ($data) use ($contenido_id) {
                $actions = '';
                if (auth()->user()->can("admin-contenidos-update")) {
                    $actions .= '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                        url('admin/contenidos/' . $contenido_id . '/grupos_preguntas/' . $data->id . '/edit') . '\';"
                        data-content="' .
                        trans('general/admin_lang.modificar') . '" data-placement="right" data-toggle="popover">
                        <i class="fa fa-pencil" aria-hidden="true"></i></button> ';
                }
                if (auth()->user()->can("admin-contenidos-delete")) {
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="javascript:deleteElement(\'' .
                        url('admin/contenidos/' . $contenido_id . '/grupos_preguntas/' .
                        $data->id) . '\');" data-content="' .
                        trans('general/admin_lang.borrar') . '" data-placement="left" data-toggle="popover">
                        <i class="fa fa-trash" aria-hidden="true"></i></button>';
                }
                return $actions;
            })
            ->editColumn('color', function ($data) {
                return '<span
                    style="display: block; margin: 4px; border-radius: 3px;'.
                    ' height: 24px; width: 24px; background-color: ' .
                    $data->color . ' !important;">'
                    . '</span>';
            })

            ->removeColumn('id')
            ->rawColumns(['color', 'actions'])
            ->make();
    }


    public function saveContenido(Request $request, $grupoPegunta)
    {
        $grupoPegunta->titulo = $request->input("titulo");
        $grupoPegunta->color = $request->input("color");
        $grupoPegunta->contenido_id = $request->input("contenido_id");

        return $grupoPegunta->save();
    }
}
