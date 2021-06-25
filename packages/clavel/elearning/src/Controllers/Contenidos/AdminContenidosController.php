<?php

namespace Clavel\Elearning\Controllers\Contenidos;

use Clavel\Elearning\Requests\ContenidoRequest;
use Clavel\Elearning\Models\Contenido;
use App\Http\Controllers\AdminController;

use Clavel\Elearning\Models\ContenidoEvaluacion;
use Clavel\Elearning\Models\ContenidoTranslation;
use Clavel\Elearning\Models\Modulo;
use Clavel\Elearning\Models\TipoContenido;
use App\Services\GetLanguage;
use App\Services\StoragePathWork;
use Baum\MoveNotPossibleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminContenidosController extends AdminController
{
    protected $page_title_icon = '<i class="fa fa fa-leanpub"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-contenidos';

        $this->myServiceSPW = new StoragePathWork("");
        $this->myServiceSPW->pathConnection = "custom";
    }

    public function index($modulo_id)
    {
        if (!Auth::user()->can('admin-contenidos-list')) {
            abort(404);
        }
        $modulo = Modulo::findOrFail($modulo_id);


        $contenidos = Contenido::where("modulo_id", $modulo_id)->get()->sortBy("lft");

        $page_title = trans("elearning::contenidos/admin_lang.contenidos") . " " . $modulo->nombre;
        return view('elearning::contenidos.admin_index', compact('page_title', 'modulo', 'contenidos'))
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function create($modulo_id)
    {
        //Comprobamos si tiene acceso a crear contenidos, si no devolvemos error 404.
        if (!Auth::user()->can('admin-contenidos-create')) {
            abort(404);
        }
        //Cargamos el módulo
        $modulo = Modulo::findOrFail($modulo_id);

        //Creamos el contenido y asignamos el modulo al que pertenece.
        $contenido = new Contenido();
        $contenido->modulo_id = $modulo->id;

        //Leemos los tipos de contenidos
        $tipos = TipoContenido::all();

        //$modulos = Modulo::activos()->where("asignatura_id", $asignatura_id)->get();
        $vista_tipo = "";

        //Creamos el formulario con la ruta a la que vamos a apuntar y todos sus datos.
        $form_data = array(
            'route' => array(
                'admin.modulos.contenidos.store', $modulo->id
            ),
            'method' => 'POST', 'id' => 'formData',
            'class' => 'form-horizontal', 'files' => true
        );
        $page_title = trans("elearning::contenidos/admin_lang.nuevo_contenido");

        // Idioma
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getTranslations($contenido);

        return view(
            'elearning::contenidos.admin_edit',
            compact('page_title', 'contenido', 'form_data', 'modulo', 'a_trans', 'tipos', 'vista_tipo')
        );
    }

    public function store(ContenidoRequest $request, $modulo_id)
    {
        if (!Auth::user()->can('admin-contenidos-create')) {
            abort(404);
        }

        $contenido = new Contenido();
        $this->saveContenido($request, $contenido);

        return Redirect::to('admin/modulos/' . $contenido->modulo_id . '/contenidos/' . $contenido->id . '/edit')
            ->with('success', trans('general/admin_lang.save_ok'));
    }

    public function edit($modulo_id, $id)
    {
        //Comprobamos si tiene acceso a crear contenidos, si no devolvemos error 404.
        if (!Auth::user()->can('admin-contenidos-update')) {
            abort(404);
        }

        //Cargamos el módulo
        $modulo = Modulo::findOrFail($modulo_id);

        //Creamos el contenido y asignamos el modulo al que pertenece.
        $contenido = Contenido::findOrFail($id);

        //Leemos los tipos de contenidos
        $tipos = TipoContenido::all();


        $myServiceSPW = new StoragePathWork("media");
        $subfolders = $myServiceSPW->readStorePath();

        //$modulos = Modulo::activos()->where("asignatura_id", $asignatura_id)->get();


        //Creamos el formulario con la ruta a la que vamos a apuntar y todos sus datos.
        $form_data = array(
            'route' => array(
                'admin.modulos.contenidos.update', $modulo->id, $contenido->id
            ),
            'method' => 'PATCH', 'id' => 'formData', 'class' => 'form-horizontal', 'files' => true
        );
        $page_title = trans("elearning::contenidos/admin_lang.modificar_contenido");

        // Idioma
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getTranslations($contenido);

        $vista_tipo = $this->getVista($modulo_id, $id, "");
        return view(
            'elearning::contenidos.admin_edit',
            compact('page_title', 'contenido', 'form_data', 'modulo', 'a_trans', 'tipos', 'vista_tipo', 'subfolders')
        );
    }

    public function update(ContenidoRequest $request, $modulo_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-update')) {
            abort(404);
        }

        // Esta validación es una capullada. Se supone que se tienen que utilizar Form Request. El problema viene
        // de la como esta creada la vista y la carga dinámica del contenido segun el tipo.

        // Validamos a la manera tradicional los contenidos según el tipo
        if (!empty($request->input('evaluacion'))) {
            $rulesEvaluacion = [
                'evaluacion.porcentaje_aprobado' => 'nullable|numeric'
            ];

            $validationErrorMessagesEvaluacion = [
                'evaluacion.porcentaje_aprobado.numeric' => 'El porcentaje para aprobar debe ser numérico'
            ];

            $validator = Validator::make($request->all(), $rulesEvaluacion, $validationErrorMessagesEvaluacion);
            if ($validator->fails()) {
                return Redirect::to('admin/modulos/' . $modulo_id . '/contenidos/' . $id . '/edit')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $contenido = Contenido::findOrFail($id);

        $this->saveContenido($request, $contenido);
        $return = "success";
        $message = trans('general/admin_lang.save_ok');

        // Verificamos si hay cambios en las variables sensibles a las notas
        // en cuyo caso recalculamos las notas del modulo y de la asignatura
        $return_info = "";
        $message_info = "";
        if ($request->input("porcentaje_aprobado_old", "") != $request->input("evaluacion.porcentaje_aprobado", "")
            || $request->input("puntua_old", "") != $request->input("evaluacion.puntua", "")
            || $request->input("peso_old", "") != $request->input("evaluacion.peso", "")
        ) {
            $return_info = "warning";
            $message_info = trans('general/admin_lang.save_recuerda');
        }

        return Redirect::to('admin/modulos/' . $contenido->modulo_id . '/contenidos/' . $contenido->id . '/edit')
            ->with('success', $message)
            ->with($return_info, $message_info);
    }

    public function destroy($modulo_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-delete')) {
            abort(404);
        }

        $contenido = Contenido::findOrFail($id);
        $contenido->delete();

        return Response::json(array(
            'success' => true,
            'msg' => 'Contenido eliminado',
            'id' => $contenido->id
        ));
    }

    public function saveContenido(Request $request, $contenido)
    {
        //Si hay modificacion de padre hacemos el cambio
        $guarded = (($request->input("parent_id") != $contenido->parent_id) || $contenido->id == "") ? true : false;

        $contenido->activo = $request->input("activo");
        $contenido->obligatorio = $request->input("obligatorio");
        $contenido->modulo_id = $request->input("modulo_id");
        $contenido->tipo_contenido_id = $request->input("tipo_contenido_id");
        $contenido->modal = $request->input("modal");
        $contenido->storepath = ($request->input("storepath")) ?
            $request->input("storepath") : null;
        $contenido->media_url = ($request->input("media_url")) ?
            $request->input("media_url") : null;
        $contenido->parent_id = ($request->input("parent_id") != '') ?
            $request->input("parent_id") : null;
        $contenido->pantalla_completa = ($request->input("pantalla_completa") != '') ?
            $request->input("pantalla_completa") : false;
        $contenido->descargar_pdf = ($request->input("descargar_pdf") != '') ?
            $request->input("descargar_pdf") : false;
        $contenido->generar_pdf = ($request->input("generar_pdf") != '') ?
            $request->input("generar_pdf") : false;
        $contenido->save();

        $files = $request->file('myfile');

        if ($request->input("delete_photo") == '1') {
            if ($contenido->pdf_archivo != '') {
                $this->myServiceSPW->deleteFile($contenido->pdf_archivo, "/" . $contenido->id);
            }
            $contenido->pdf_archivo = "";
        }

        if (!is_null($files)) {
            foreach ($files as $file) {
                try {
                    if (!is_null($file)) {
                        $filename = $this->myServiceSPW->saveFile($file, "/" . $contenido->id);
                        $contenido->pdf_archivo = $filename;
                    }
                } catch (NotFoundHttpException $e) {
                }
            }
        }

        $contenido->save();

        if ($guarded) {
            if ($request->input("parent_id") == '') {
                $contenido->makeRoot();
            } else {
                $parent = Contenido::find($request->input("parent_id"));
                try {
                    $contenido->makeLastChildOf($parent);
                } catch (MoveNotPossibleException  $ex) {
                    return "error";
                }
            }
        }

        foreach ($request->input('userlang') as $key => $value) {
            $itemTrans = ContenidoTranslation::findOrNew($value["id"]);
            $itemTrans->contenido_id = $contenido->id;
            $itemTrans->locale = $key;
            $itemTrans->nombre = $value["nombre"];
            $itemTrans->contenido = (isset($value["contenido"])) ? $value["contenido"] : null;
            $itemTrans->contenido_aprobado = (isset($value["contenido_aprobado"])) ?
                $value["contenido_aprobado"] : null;
            $itemTrans->contenido_suspendido = (isset($value["contenido_suspendido"])) ?
                $value["contenido_suspendido"] : null;
            $itemTrans->url_amigable = str_slug($value["nombre"]);
            $itemTrans->save();
        }

        if (!is_null($request->input('evaluacion')) || $contenido->tipo_contenido_id == 3) {
            $itemEvaluacion = ContenidoEvaluacion::findOrNew($request->input('evaluacion.id', 0));
            $itemEvaluacion->contenido_id = $contenido->id;
            $itemEvaluacion->modulo_id = $contenido->modulo_id;
            $itemEvaluacion->mostrar_respuesta = $request->input('evaluacion.mostrar_respuesta', 0);
            $itemEvaluacion->mostrar_resultado = $request->input('evaluacion.mostrar_resultado', 0);
            $itemEvaluacion->preguntas_aleatorias = $request->input('evaluacion.preguntas_aleatorias', 0);
            $itemEvaluacion->respuestas_aleatorias = $request->input('evaluacion.respuestas_aleatorias', 0);
            $itemEvaluacion->presencial = $request->input('evaluacion.presencial', 0);
            $itemEvaluacion->permitir_resetear = $request->input('evaluacion.permitir_resetear', 0);
            $itemEvaluacion->numero_resets = $request->input('evaluacion.numero_resets', 1);
            $itemEvaluacion->porcentaje_aprobado = (empty($request->input('evaluacion.porcentaje_aprobado')) ? 0 :
                $request->input('evaluacion.porcentaje_aprobado'));
            $itemEvaluacion->numero_preguntas_visibles = $request->input('evaluacion.numero_preguntas_visibles', null);
            $itemEvaluacion->puntua = $request->input('evaluacion.puntua', 0);
            $itemEvaluacion->peso = $request->input('evaluacion.peso', null);
            $itemEvaluacion->grupos_preguntas = $request->input('evaluacion.grupos_preguntas', 0);
            $itemEvaluacion->save();
        } else {
            $contenido_para_borrar = ContenidoEvaluacion::where("contenido_id", "=", $contenido->id);
            $contenido_para_borrar->delete();
        }
    }

    public function getVista($modulo_id, $contenido_id, $id)
    {
        $contenido = Contenido::findOrFail($contenido_id);

        $vista = $contenido->tipo->vista;
        if ($id != "") {
            $tipo_contenido = TipoContenido::findOrFail($id);
            $vista = $tipo_contenido->vista;
        }

        return $vista;
    }

    public function setChangeState($modulo_id, $id)
    {
        if (!Auth::user()->can('admin-contenidos-update')) {
            abort(404);
        }

        $contenido = Contenido::findOrFail($id);
        $contenido->activo = !$contenido->activo;
        return $contenido->save() ? 1 : 0;
    }

    public function reordenarArbol($modulo_id, $node_id, $parent_id, $previous)
    {
        $node = Contenido::find($node_id);

        if ($previous != '0') {
            $prev = Contenido::find($previous);
            $node->moveToRightOf($prev);
        } else {
            if ($parent_id != 0) {
                $parent = Contenido::find($parent_id);
                try {
                    $node->makeFirstChildOf($parent);
                } catch (MoveNotPossibleException  $ex) {
                    return "error";
                }
            } else {
                $parent = Contenido::where("modulo_id", "=", $modulo_id)->orderBy('lft')->first();
                if ($parent->id !== $node->id) {
                    $node->moveToLeftOf($parent);
                }
            }
        }
    }
}
