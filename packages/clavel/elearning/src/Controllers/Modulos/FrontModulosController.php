<?php

namespace Clavel\Elearning\Controllers\Modulos;

use App\Http\Controllers\FrontController;
use Carbon\Carbon;
use Clavel\Elearning\Models\Contenido;
use Clavel\Elearning\Models\Convocatoria;
use Clavel\Elearning\Models\Modulo;

use App\Services\StoragePathWork;
use Clavel\Elearning\Models\TrackContenido;
use Clavel\Elearning\Services\ElearningHelper;
use Clavel\Elearning\Services\TrackContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class FrontModulosController extends FrontController
{
    private $myServiceSPW;

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->myServiceSPW = new StoragePathWork("modulos");

        // Esto no funciona en laravel 5.7
        /*
        if(!Auth::user()) abort(404);

        if(Auth::user() != null && explode("/", $request->path())[1]!='openImage') {
        // TODO - probar que funciona el TrackContent <---
            $modulo = Modulo::whereTranslation('url_amigable', $request->slug)->where("id", $request->id)->first();
            $tracking = new TrackContent("",$modulo->id,"");
            $tracking->moduloActivo($modulo);
        }
        */
    }

    public function detalle(Request $request)
    {
        $modulo_id = $request->id;

        $modulo = Modulo::whereTranslation('url_amigable', $request->slug)->where("id", $modulo_id)->first();
        if (empty($modulo)) {
            abort(404);
        }

        if ($modulo->contenidos->count() <= 0) {
            abort(403, "No hay contenidos activos en este módulo");
        }
        $page_title = $modulo->getTranslatedNombre();

        $asig_id=$modulo->asignatura_id;

        $trackContenidos = new TrackContent(
            "",
            $modulo_id,
            $asig_id,
            auth()->user()->can('frontend-asignaturas-convocatoria-premium')
        );

        //$trackContenidos = TrackContenido::findOrFail($modulo_id);
        $convocatoria=$trackContenidos->convocatoria_id;

        $dif = new ElearningHelper();
        // Obtenemos los minutos que nos quedan para acabar el curso/examen
        $timeToFinishAssignatura = $dif->timeToFinishAssignatura($asig_id, $convocatoria, Auth::user());

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



        // Deberiamos mirar cuantos contenidos tiene, si solo tiene uno que tipo de contenido es,
        // y que tipo de módulo es para abrir la vista correspondiente.
        if ($modulo->contenidos()->count() > 1) {
            $completados = $modulo->getComplete();
            return view(
                'elearning::modulos.front_detalle',
                compact(
                    'modulo',
                    'page_title',
                    'completados',
                    'isClosed',
                    'alertLevel',
                    'timeToFinishAssignatura',
                    'tiempo_restante',
                    'alertColor'
                )
            );
        } else {
            return Redirect::to('contenido/detalle-contenido/'.
                $modulo->contenidos[0]->url_amigable.'/'.$modulo->contenidos[0]->id);
        }
    }


    public function openImage($id)
    {
        $modulo = Modulo::findOrFail($id);
        return $this->myServiceSPW->showFile($modulo->image, '/'.$modulo->asignatura_id);
    }
}
