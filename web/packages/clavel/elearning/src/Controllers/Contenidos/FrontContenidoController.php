<?php

namespace Clavel\Elearning\Controllers\Contenidos;

use App\Http\Controllers\FrontController;
use Carbon\Carbon;
use Clavel\Elearning\Models\Asignatura;
use Clavel\Elearning\Models\Contenido;
use App\Models\Media;
use Clavel\Elearning\Models\ContenidoEvaluacion;
use Clavel\Elearning\Models\Modulo;
use Clavel\Elearning\Models\RespuestaResultado;
use Clavel\Elearning\Models\TipoContenido;
use App\Services\StoragePathWork;
use Clavel\Elearning\Models\TrackContenido;
use Clavel\Elearning\Models\TrackVideo;
use Clavel\Elearning\Services\ElearningHelper;
use Clavel\Elearning\Services\TrackContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class FrontContenidoController extends FrontController
{
    protected $tracking;
    protected $myServiceSPW;

    public function __construct(Request $request)
    {
        parent::__construct();

        $this->myServiceSPW = new StoragePathWork($request->id);
        /*if(Auth::user() != null && explode("/", $request->path())[2]!='openPDF') {
            $this->tracking = new TrackContent($request->id,"","");
            $this->tracking->trackingContenido($request->id,1);
        }
        */
    }

    public function track(Request $request)
    {
        return "";
    }

    public function detalle(Request $request)
    {
        $this->tracking = new TrackContent(
            $request->id,
            "",
            "",
            auth()->user()->can('frontend-asignaturas-convocatoria-premium')
        );

        // Buscamos el contenido en la base de datos
        $contenido = Contenido::whereTranslation('url_amigable', $request->slug)->where("id", $request->id)->first();
        if (empty($contenido)) {
            abort(404);
        }
        $contenido->tracking = $this->tracking;

        // Obenemos también el listado de contenidos del módulo
        $sortContenidos = $contenido->modulo->contenidos()->activos()->get()->sortBy('nombre');
        $completados = $contenido->modulo->getComplete();

        //Una vez entramos aquí comprobamos si tiene que acceder algún tipo de contenido previo
        $url = $this->tracking->completeAsignatura($contenido->modulo->asignatura);
        if ($url !== false) {
            return Redirect::to($url);
        }

        if ($contenido->tipo_contenido_id != 4) {
            $this->tracking->trackingContenido($request->id, 1);
        }

        $page_title = $contenido->modulo->getTranslatedNombre(); //.". ".strip_tags($contenido->modulo->descripcion);
        $anterior = $this->getNavegacion($contenido, "<", "DESC");
        $siguiente = $this->getNavegacion($contenido, ">", "ASC");

        $asig_id = $contenido->modulo->asignatura->id;
        $dif = new ElearningHelper();

        // Obtenemos los minutos que nos quedan para acabar el curso/examen
        $timeToFinishAssignatura = $dif->timeToFinishAssignatura(
            $asig_id,
            $this->tracking->convocatoria_id,
            Auth::user()
        );

        // Verificamos si hemos llegado a 0 y por lo tanto NO permitiremos modificar el examen
        $isClosed = false;

        // Indicamos el nivel del mensaje a mostrar, es decir, cuando nos queda para que el usuario se espabile a
        // acabar el curso donde 0 es no mostrar nada
        $alertLevel = 0;
        $alertColor = "info";

        // Hay control de tiempo?
        if ($timeToFinishAssignatura >= 0) {
            // Hemos llegado al final del tiempo de control
            if ($timeToFinishAssignatura == 0) {
                $isClosed = true;
                $alertLevel = 1;
                $alertColor = "danger";
            } else {
                // Menor a 2 días
                if ($timeToFinishAssignatura < (2 * 24 * 60)) {
                    $alertLevel = 2;
                    $alertColor = "danger";
                } elseif ($timeToFinishAssignatura < (10 * 24 * 60)) { // Menor a 10 días
                    $alertLevel = 3;
                    $alertColor = "warning";
                } else { // Superior a 10 días
                    $alertLevel = 4;
                }
            }
        }
        $tiempo_restante = Carbon::now()->diff(\Carbon\Carbon::now()->addMinutes($timeToFinishAssignatura));
        /*
        $isClosed = true;
        $alertLevel = 1;
        $alertColor = "danger";
        $tiempo_restante = Carbon::now()->diff(Carbon::now());
        */

        return view(
            'elearning::contenidos.front_index',
            compact(
                'page_title',
                'contenido',
                'completados',
                'sortContenidos',
                'siguiente',
                'anterior',
                'isClosed',
                'alertLevel',
                'timeToFinishAssignatura',
                'tiempo_restante',
                'alertColor'
            )
        );
    }

    private function getNavegacion($contenido, $op, $orden)
    {
        $tipo_tema = TipoContenido::where("slug", "=", "tema")->first();
        return $contenido->modulo->contenidos()->activos()
            ->where("lft", $op, $contenido->lft)
            ->where("tipo_contenido_id", "<>", $tipo_tema->id)
            ->orderBy('lft', $orden)
            ->first();
    }

    public function store(Request $request)
    {
        $this->tracking = new TrackContent(
            $request->id,
            "",
            "",
            auth()->user()->can('frontend-asignaturas-convocatoria-premium')
        );
        $this->tracking->trackingContenido($request->id, 1);

        $contenido = Contenido::findorFail($request->input('contenido_id'));
        if (empty($contenido)) {
            abort(404);
        }
        //Solo trackearemos el contenido si tiene preguntas activas.
        if ($contenido->preguntas()->activas()->count() > 0) {
            //Debemos borrar todos los datos de este usuario para este examen.
            $track_eval = RespuestaResultado::where("contenido_id", "=", $contenido->id)
                ->where("user_id", "=", Auth::user()->id)
                ->where("convocatoria_id", "=", $this->tracking->convocatoria_id)
                ->delete();

            //Se supone que hemos venido aqui por lo tanto debemos recorrer todas las respuestas
            foreach ($contenido->preguntas()->activas()->get() as $pregunta) {
                if (isset($request->input('answers')[$pregunta->id])) {
                    // Si la pregunta es de tipo radio(unica) o checkbox(multiple), recorremos las respuestas y las
                    // grabamos
                    if ($pregunta->tipo->slug == "unica" || $pregunta->tipo->slug == "multiple") {
                        //Recorremos las preguntas que tenemos activas y a su vez su respuestas
                        foreach ($pregunta->respuestas()->activas()->get() as $respuesta) {
                            //Aqui debemos insertar esta información en los resultados
                            $respuesta_usuario = new RespuestaResultado();
                            $respuesta_usuario->respuesta_id = $respuesta->id;
                            $respuesta_usuario->pregunta_id = $pregunta->id;
                            $respuesta_usuario->user_id = Auth::user()->id;
                            $respuesta_usuario->contenido_id = $request->input('contenido_id');
                            $respuesta_usuario->modulo_id = $request->input('modulo_id');
                            $respuesta_usuario->asignatura_id = $request->input('asignatura_id');
                            $respuesta_usuario->convocatoria_id = $this->tracking->convocatoria_id;
                            if (isset($request->input('answers')[$pregunta->id][$respuesta->id])) {
                                $respuesta_usuario->marcada = $request->input('answers')[$pregunta->id][$respuesta->id];
                                $respuesta_usuario->observaciones = null;
                            } else {
                                if ($pregunta->tipo_pregunta_id == 1) {
                                    $respuesta_usuario->marcada = (isset($request->input('answers')[$pregunta->id]) &&
                                        $request->input('answers')[$pregunta->id] == $respuesta->id) ? true : false;
                                    $respuesta_usuario->observaciones = null;
                                } elseif ($pregunta->tipo_pregunta_id == 3) {
                                    $respuesta_usuario->marcada = true;
                                    $respuesta_usuario->observaciones = $request->input('answers')[$pregunta->id];
                                } else {
                                    $respuesta_usuario->marcada = (isset($request->input('answers')[$pregunta->id]) &&
                                        $request->input('answers')[$pregunta->id] == $respuesta->id) ? true : false;
                                }
                            }
                            $respuesta_usuario->correcta = $respuesta->correcta;
                            $respuesta_usuario->puntos_correcta = $respuesta->puntos_correcta;
                            $respuesta_usuario->puntos_incorrecta = $respuesta->puntos_incorrecta;
                            $respuesta_usuario->puntos_obtenidos =
                                ($respuesta_usuario->marcada == $respuesta->correcta) ?
                                $respuesta->puntos_correcta :
                                $respuesta->puntos_incorrecta;
                            $respuesta_usuario->save();
                        }
                    } else {
                        // En tipo texto solo hay una respuesta posible
                        $respuesta = $pregunta->respuestas()->first();

                        // Es respuesta de texto
                        //Aqui debemos insertar esta información en los resultados
                        $respuesta_usuario = new RespuestaResultado();
                        $respuesta_usuario->respuesta_id = $respuesta->id;
                        $respuesta_usuario->pregunta_id = $pregunta->id;
                        $respuesta_usuario->user_id = Auth::user()->id;
                        $respuesta_usuario->contenido_id = $request->input('contenido_id');
                        $respuesta_usuario->modulo_id = $request->input('modulo_id');
                        $respuesta_usuario->asignatura_id = $request->input('asignatura_id');
                        $respuesta_usuario->convocatoria_id = $this->tracking->convocatoria_id;
                        $respuesta_usuario->marcada = true;
                        $respuesta_usuario->observaciones = $request->input('answers')[$pregunta->id];
                        $respuesta_usuario->correcta = $respuesta->correcta;
                        $respuesta_usuario->puntos_correcta = $respuesta->puntos_correcta;
                        $respuesta_usuario->puntos_incorrecta = $respuesta->puntos_incorrecta;
                        $respuesta_usuario->puntos_obtenidos =
                            ($respuesta_usuario->marcada == $respuesta->correcta) ?
                            $respuesta->puntos_correcta : $respuesta->puntos_incorrecta;
                        $respuesta_usuario->save();
                    }
                }
            }
            //Debemos calcular la nota que quedara en el examen.
            $this->tracking->calcularContenido($request->id);

            //Debemos trackear que hemos completado la evaluación.
            $this->tracking->trackingContenido($request->id, 2);
        }

        //Lo devolvemos a la misma página.
        return Redirect::to('contenido/detalle-contenido/' . $request->slug . '/' . $request->id);
    }

    public function destroy(Request $request)
    {
        $this->tracking = new TrackContent(
            $request->id,
            "",
            "",
            auth()->user()->can('frontend-asignaturas-convocatoria-premium')
        );
        $this->tracking->trackingContenido($request->id, 1);

        $contenido = Contenido::findorFail($request->id);
        if (empty($contenido)) {
            abort(404);
        }

        $this->tracking->resetearContenido($contenido);

        //Lo devolvemos a la misma página.
        return Redirect::to('contenido/detalle-contenido/' . $request->slug . '/' . $request->id);
    }

    public function openPDF($contenido_id)
    {
        $contenido = Contenido::findOrFail($contenido_id);

        if (empty($contenido)) {
            abort(404);
        }
        // Si no tiene descarga y ha accedido directamente aquí o no esta registrado y pertenece al curso, lo echamos
        if (Auth::user() == null || $contenido->descargar_pdf == '0') {
            abort(404);
        }

        // Si tiene pdf y no se genera, lo descargamos
        if ($contenido->generar_pdf == '0' && $contenido->pdf_archivo != '') {
            return $this->myServiceSPW->showFile($contenido->pdf_archivo, '/' . $contenido->id);
        }

        $page_title = $contenido->nombre;

        $contenido->contenido = $this->changeImgContent($contenido->contenido);

        $view = View::make('elearning::contenidos.printer_pdf', compact("page_title", 'contenido'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('a4');
        $pdf->setWarnings(false);

        return $pdf->download($contenido->url_amigable . '.pdf');
    }

    private function changeImgContent($htmlContent)
    {
        // leemos todas las imágenes del contenido y lo metemos en un array
        preg_match_all('/<img[^>]+>/i', $htmlContent, $imgTags);

        for ($i = 0; $i < count($imgTags[0]); $i++) {
            // cogemos el source string (src)
            preg_match('/src="([^"]+)/i', $imgTags[0][$i], $imgage);
            preg_match('/align="([^"]+)/i', $imgTags[0][$i], $align);
            // Quitamos el tag SRC
            $media_id = str_ireplace('src="/media/getAnnex/', '', $imgage[0]);
            $position = (isset($align[0])) ? str_ireplace('align="', '', $align[0]) : "";
            // Obtenemos su media file
            $media = Media::findOrFail($media_id);
            $htmlContent = str_replace($imgage[0], 'src="' .
                storage_path('app' . $media->path . '/' . $media->filename), $htmlContent);
            if ($position != '') {
                $htmlContent = str_replace($align[0], 'style="float:' . $position . ";", $htmlContent);
            }
        }

        return $htmlContent;
    }

    public function trackVideo(Request $request)
    {
        $user_id = auth()->user()->id;
        $trackVideo = TrackVideo::where("contenido_id", $request->contenido_id)
            ->where("user_id", $user_id)->first();

        if (empty($trackVideo)) {
            $trackVideo = new TrackVideo();
            $total_video_seconds = $request->get("total_video_seconds", 0);
            if (is_nan($total_video_seconds)) {
                $total_video_seconds = 0;
            }
            $trackVideo->total_video_seconds = $total_video_seconds;
            $trackVideo->contenido_id = $request->contenido_id;
            $trackVideo->modulo_id = $request->modulo_id;
            $trackVideo->asignatura_id = $request->asignatura_id;
            $trackVideo->user_id = $user_id;
        }

        // user_stop es el punto en el que el usuario ha dejado de ver el video,
        // se puede restablecer si el usuario elige ver el video nuevamente.
        // user_progress es el punto de visualización máximo del usuario, lo usamos para calcular
        // el porcentaje que cada usuario ha visto.
        $trackVideo->user_stop = $request->user_stop;
        $trackVideo->user_progress = ($request->user_stop > $trackVideo->user_progress) ?
            $request->user_stop :
            $trackVideo->user_progress;

        $trackVideo->save();
        return 1;
    }

    public function trackGaleria($id)
    {
        try {
            $this->tracking = new TrackContent(
                $id,
                "",
                "",
                auth()->user()->can('frontend-asignaturas-convocatoria-premium')
            );
            $this->tracking->trackingContenido($id, 1);
            return 1;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
