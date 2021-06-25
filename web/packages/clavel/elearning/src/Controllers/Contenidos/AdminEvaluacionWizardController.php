<?php

namespace Clavel\Elearning\Controllers\Contenidos;

use Clavel\Elearning\Models\Contenido;
use Clavel\Elearning\Models\Pregunta;
use Clavel\Elearning\Models\PreguntaTranslation;
use Clavel\Elearning\Models\Respuesta;
use Clavel\Elearning\Models\RespuestaTranslation;
use Clavel\Elearning\Models\TipoPregunta;
use App\Services\GetLanguage;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Redirect;

class AdminEvaluacionWizardController extends AdminController
{
    protected $page_title_icon = '<i class="fa  fa-file-image-o"></i>';

    public function __construct()
    {
        parent::__construct();

        $this->access_permission = 'admin-contenidos';
    }

    public function index($contenido_id)
    {
        if (!Auth::user()->can('admin-contenidos-create')) {
            abort(404);
        }
        $contenido = Contenido::findOrFail($contenido_id);
        $tipos = TipoPregunta::activas()->whereIn('slug', ['unica', 'multiple'])->get();

        // Idioma
        $serviceTranslation = new GetLanguage(config('app.default_locale'));
        $a_trans = $serviceTranslation->getTranslations(null, false);

        $page_title = trans("elearning::preguntas/admin_lang.wizard_creacion_rapida") . " " . $contenido->nombre;
        $form_data = array(
            'url' => array(
                'admin/contenidos/preguntas/generate'
            ),
            'method' => 'POST', 'id' => 'formData', 'class' => 'form-horizontal'
        );

        return view(
            'elearning::preguntas.wizard.admin_index',
            compact(
                'page_title',
                'contenido',
                'a_trans',
                'tipos',
                'form_data'
            )
        )
            ->with('page_title_icon', $this->page_title_icon);
    }

    public function generate(Request $request)
    {
        $contenidoId = $request->get("contenido_id", 0);

        $contadorPreguntas = Pregunta::where('contenido_id', $contenidoId)
            ->max('orden');

        $a_response = $this->arrayForSave($request->input("userlang"), $contenidoId, $contadorPreguntas + 1);

        foreach ($a_response as $pregunta) {
            $mPregunta = new Pregunta();
            $mPregunta->contenido_id = $pregunta["contenido_id"];
            $mPregunta->orden = $pregunta["orden"];
            $mPregunta->activa = $pregunta["activa"];
            $mPregunta->obligatoria = $pregunta["obligatoria"];
            $mPregunta->tipo_pregunta_id = $pregunta["tipo_pregunta_id"];
            $mPregunta->save();

            foreach ($pregunta["idiomas"] as $pregunta_idioma) {
                $mPreguntaTrans = new PreguntaTranslation();
                $mPreguntaTrans->pregunta_id = $mPregunta->id;
                $mPreguntaTrans->nombre = (empty(trim($pregunta_idioma["nombre"])) ?
                    "Empty Question" :
                    $pregunta_idioma["nombre"]);
                $mPreguntaTrans->locale = $pregunta_idioma["locale"];
                $mPreguntaTrans->save();
            }

            foreach ($pregunta["respuestas"] as $respuestas) {
                $mRespuestas = new Respuesta();
                $mRespuestas->pregunta_id = $mPregunta->id;
                $mRespuestas->orden = $respuestas["orden"];
                $mRespuestas->correcta = $respuestas["correcta"];
                $mRespuestas->puntos_correcta = $respuestas["puntos_correcta"];
                $mRespuestas->activa = $respuestas["activa"];
                $mRespuestas->save();

                foreach ($respuestas["idiomas"] as $respuesta_idioma) {
                    $mRespuestaTrans = new RespuestaTranslation();
                    $mRespuestaTrans->respuesta_id = $mRespuestas->id;
                    $mRespuestaTrans->nombre = (empty(trim($respuesta_idioma["nombre"])) ?
                        "[Empty Answer]" :
                        $respuesta_idioma["nombre"]);
                    $mRespuestaTrans->locale = $respuesta_idioma["locale"];
                    $mRespuestaTrans->comentario = "";
                    $mRespuestaTrans->save();
                }
            }
        }
        return Redirect::to('admin/contenidos/' . $request->input("contenido_id") . '/preguntas/');
    }

    private function arrayForSave($userlang, $contenido_id, $nOrden = 1)
    {
        $array_return = array();
        foreach ($userlang as $idioma => $contenido) {
            foreach ($contenido as $key => $value) {
                if (!isset($array_return[$key])) {
                    $array_return[$key]["contenido_id"] = $contenido_id;
                    $array_return[$key]["orden"] = $nOrden;
                    $array_return[$key]["activa"] = true;
                    $array_return[$key]["obligatoria"] = true;
                    $array_return[$key]["tipo_pregunta_id"] = $value["tipo"];
                    $nOrden++;
                }

                $array_return[$key]["idiomas"][$idioma]["nombre"] = $value["pregunta"];
                $array_return[$key]["idiomas"][$idioma]["locale"] = $idioma;

                $nOrdenres = 1;
                foreach ($value["responses"] as $key_res => $respuesta) {
                    if (!isset($array_return[$key]["respuestas"][$key_res])) {
                        $array_return[$key]["respuestas"][$key_res]["orden"] = $nOrdenres;
                        $array_return[$key]["respuestas"][$key_res]["correcta"] =
                            (isset($respuesta["correcta"])) ? true : false;
                        $array_return[$key]["respuestas"][$key_res]["puntos_correcta"] =
                            ($respuesta["puntos"] != '') ? $respuesta["puntos"] : 0;
                        $array_return[$key]["respuestas"][$key_res]["activa"] = true;

                        $nOrdenres++;
                    }
                    $array_return[$key]["respuestas"][$key_res]["idiomas"][$idioma]["nombre"] = $respuesta["respuesta"];
                    $array_return[$key]["respuestas"][$key_res]["idiomas"][$idioma]["locale"] = $idioma;
                }
            }
        }
        return $array_return;
    }
}
