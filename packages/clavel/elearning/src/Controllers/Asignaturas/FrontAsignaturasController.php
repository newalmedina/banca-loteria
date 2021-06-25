<?php

namespace Clavel\Elearning\Controllers\Asignaturas;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\GetLanguage;
use App\Services\StoragePathWork;
use Clavel\Elearning\Models\Curso;
use Illuminate\Support\Facades\DB;
use Clavel\Elearning\Models\Alumno;
use Clavel\Elearning\Models\Codigo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Clavel\Elearning\Models\Asignatura;
use Clavel\Elearning\Models\Certificado;

use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\FrontController;
use Clavel\Elearning\Models\Convocatoria;
use Clavel\Elearning\Services\TrackContent;
use Clavel\Elearning\Models\TrackAsignatura;
use Clavel\Elearning\Services\ElearningHelper;
use Clavel\Elearning\Services\AsignaturaService;
use Clavel\Elearning\Models\CodigoAsignaturaUser;
use Clavel\Elearning\Requests\CodigoAsignaturaRequest;

class FrontAsignaturasController extends FrontController
{
    private $myServiceSPW;
    private $tracking;

    public function __construct(Request $request)
    {
        parent::__construct();
        $this->myServiceSPW = new StoragePathWork("asignaturas");
    }

    public function index($filtro = '')
    {
        $page_title = trans("elearning::asignaturas/front_lang.listado");

        $cursos_categoria = Curso::active()->get();
        $asignaturas = Asignatura::orderBy("orden")->active();

        $total_asignaturas = $asignaturas->count();

        //Para acceder usuario rol front debera introducir la fecha de nacimiento -- usuario-front
        $usuario_front = User::UserProfiles()->withRole('usuario-front')
            ->where('user_id', auth()->user()->id)
            ->first();

        //SI tenemos una sola asignatura redirigmos directamente a ella.
        if (!config("elearning.cursos.mostrar_asignaturas") && $asignaturas->count() == 1) {
            $asignaturas = $asignaturas->first();
            // Si por configuración tenenmos que vaya directo a los módulos de la asignatura y esta esta activa
            if ($asignaturas->getActiva()["activa"] && config("elearning.cursos.ENTRAR_ASIGNATURA")) {
                return Redirect::to('asignaturas/contenido/' . $asignaturas->url_amigable . '/' . $asignaturas->id);
            }
            return Redirect::to('asignaturas/detalle/' . $asignaturas->url_amigable . '/' . $asignaturas->id);
        }

        if ($filtro != '') {
            $curso = Curso::active()->findOrFail($filtro);
            $asignaturas = $curso->asignaturaPivot()->active();
        }

        $asignaturas = $asignaturas->paginate(6);

        return view(
            "elearning::asignaturas.front_listado",
            compact(
                'page_title',
                'cursos_categoria',
                'asignaturas',
                'total_asignaturas',
                'filtro'
            )
        );
    }

    public function detalle($slug, $id)
    {
        $asignatura = Asignatura::active()->findOrFail($id);

        $this->tracking = new TrackContent(
            "",
            "",
            $asignatura->id,
            auth()->user()->can('frontend-asignaturas-convocatoria-premium')
        );

        // Si el usuario ya esta cursando la asignatura y esta está activa, saltamos esta página y vamos directamente
        // al contenido del módulo...
        if ($asignatura->getActiva()["activa"] &&
            $asignatura->track()->where("user_id", "=", Auth::user()->id)
            ->where("convocatoria_id", "=", $this->tracking->convocatoria_id)->count() > 0
        ) {
            return Redirect::to('asignaturas/contenido/' . $asignatura->url_amigable . '/' . $asignatura->id);
        }

        // Si quiere entrar por url en detalle pero la asignatura ya esta activa y es de entrada directa
        // lo reenviamos al listado de modulos de la misma
        if ($asignatura->getActiva()["activa"] && config("elearning.cursos.ENTRAR_ASIGNATURA")) {
            return Redirect::to('asignaturas/contenido/' . $asignatura->url_amigable . '/' . $asignatura->id);
        }

        $page_title = $asignatura->getTranslatedTitulo();

        return view(
            'elearning::asignaturas.front_detalle',
            compact(
                'asignatura',
                'page_title'
            )
        )->with([
            'tracking' => $this->tracking
        ]);
    }

    public function contenido($slug, $id)
    {
        $asignatura = Asignatura::active()->findorFail($id);


        // Antes de nada comprobamos si podemos acceder a la asignatura
        if (!AsignaturaService::accesoAsignaturaCodigo($asignatura, auth()->user())) {
            // No tiene acceso volvemos a la home
            return Redirect::to('/asignaturas/codigo/'.$asignatura->id);
        }

        $this->tracking = new TrackContent(
            "",
            "",
            $id,
            auth()->user()->can('frontend-asignaturas-convocatoria-premium')
        );

        if (empty($asignatura)) {
            abort(404);
        }
        $page_title = $asignatura->getTranslatedTitulo();
        $track_asignatura = $asignatura->track()->where("user_id", "=", Auth::user()->id)
            ->where("convocatoria_id", "=", $this->tracking->convocatoria_id)
            ->first();
        $convocatoria = $this->tracking->getInformacionConvocatoria($this->tracking->convocatoria_id);
        $avance = $asignatura->getPercentContents($this->tracking->convocatoria_id);


        $dif = new ElearningHelper();
        // Obtenemos los minutos que nos quedan para acabar el curso/examen
        $timeToFinishAssignatura = $dif->timeToFinishAssignatura($id, $this->tracking->convocatoria_id, Auth::user());

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
            'elearning::asignaturas.front_contenido',
            compact(
                'asignatura',
                'page_title',
                'track_asignatura',
                'convocatoria',
                'avance',
                'isClosed',
                'alertLevel',
                'timeToFinishAssignatura',
                'tiempo_restante',
                'alertColor'
            )
        )
            ->with([
                'tracking' => $this->tracking
            ]);
    }

    public function openImage($id)
    {
        $asignatura = Asignatura::findOrFail($id);
        return $this->myServiceSPW->showFile($asignatura->image, '/' . $id);
    }


    public function certificado($slug, $id)
    {
        $this->tracking = new TrackContent(
            "",
            "",
            $id,
            auth()->user()->can('frontend-asignaturas-convocatoria-premium')
        );

        $asignatura = Asignatura::active()->findorFail($id);
        if (empty($asignatura)) {
            abort(404);
        }

        $convocatoria = $this->tracking->getInformacionConvocatoria($this->tracking->convocatoria_id);
        if (empty($convocatoria)) {
            abort(404);
        }

        $tracking = $asignatura->track()->where('user_id', '=', Auth::user()->id)
            ->where("convocatoria_id", "=", $convocatoria->id)
            ->first();

        if ($convocatoria->certificado_id != "" && $tracking->completado && $tracking->aprobado) {
            $certificado = Certificado::find($convocatoria->certificado_id);

            // Idiomas
            $serviceTranslation = new GetLanguage(config('app.default_locale'));
            $a_trans = $serviceTranslation->getLangs();

            $view = view(
                'elearning::certificados.front_pdf',
                compact(
                    'certificado',
                    'a_trans',
                    'asignatura',
                    'tracking'
                )
            )->render();

            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('a4', 'landscape');

            $fileName = str_slug(auth()->user()->userProfile->fullName . "_" .
                date('YmdHis') . "_" . 'Certificado') . '.pdf';

            return $pdf->download($fileName);
        } else {
            abort(403, "No se dispone de certificado");
        }
    }

    public function certificadoCurso(Curso $curso)
    {
        if ($curso->checkCertificado()) {
            $certificado = Certificado::find($curso->certificado_id);
            $asignatura = $curso;
            $asignatura->titulo = $curso->nombre;
            $tracking = TrackAsignatura::join(
                "asignatura_cursos AS ac",
                "track_asignatura.asignatura_id",
                "ac.asignatura_id"
            )
                ->where("ac.curso_id", $curso->id)
                ->whereNotNull("track_asignatura.fecha_fin")
                ->orderBy("track_asignatura.fecha_fin")
                ->first();

            // Idiomas
            $serviceTranslation = new GetLanguage(config('app.default_locale'));
            $a_trans = $serviceTranslation->getLangs();

            $view = view(
                'elearning::certificados.front_pdf',
                compact('certificado', 'a_trans', 'asignatura', 'tracking')
            )->render();

            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->setPaper('a4', 'landscape');

            return $pdf->download('Certificado.pdf');
        } else {
            abort(403, "No se dispone de certificado");
        }
    }

    public function codigo(Request $request, $id)
    {
        $page_title = trans("elearning::asignaturas/front_lang.codigo_title");

        $asignatura = Asignatura::active()->findorFail($id);

        $form_data = array(
            'url' => array('asignaturas/codigo'),
            'method' => 'POST',
            'id' => 'formData',
            'class' => 'form-horizontal'
        );

        return view(
            "elearning::asignaturas.front_codigo",
            compact(
                'page_title',
                'form_data',
                'asignatura'
            )
        );
    }

    public function setCodigo(CodigoAsignaturaRequest $request)
    {
        $asignatura_id = $request->input('asignatura_id', '');
        $asignatura = Asignatura::active()->find($asignatura_id);
        if (empty($asignatura)) {
            abort(404);
        }

        $user = User::find(auth()->user()->id);
        if (empty($user)) {
            abort(404);
        }

        // Vemos si viene con codigo y verificamos si existe y es valido
        $codigo_id = $request->input('codigo', '');

        $errorCodigo = true;
        // Es requerido el código
        if (!empty($codigo_id)) {
            // Buscamos el código en crudo
            $codigo = Codigo::select('codigos.id', 'codigos.codigo')
                ->where('codigo', $codigo_id)
                ->join('codigo_asignaturas', function ($join) use ($asignatura) {
                    $join->on('codigo_asignaturas.codigo_id', '=', 'codigos.id');
                    $join->on('codigo_asignaturas.asignatura_id', '=', DB::raw($asignatura->id));
                })
                ->first();
            // Verificamos que lo hemos encontrado
            if (!empty($codigo)) {
                // Verificamos si el alumno tiene este codigo y se lo ponemos
                $codigAsignaturaAlumno = CodigoAsignaturaUser::where('asignatura_id', $asignatura->id)
                    ->where('user_id', $user->id)
                    ->where('codigo_id', $codigo->id)
                    ->first();
                if (empty($codigAsignaturaAlumno)) {
                    $codigAsignaturaAlumno = new CodigoAsignaturaUser();
                    $codigAsignaturaAlumno->asignatura_id = $asignatura->id;
                    $codigAsignaturaAlumno->user_id = $user->id;
                    $codigAsignaturaAlumno->codigo_id = $codigo->id;
                    $codigAsignaturaAlumno->codigo = $codigo->codigo;
                    $codigAsignaturaAlumno->save();

                    // Tenemos código y todo es correcto
                    $errorCodigo = false;
                }
            }
        }

        // Si hay error => Rollback y volvemos
        if ($errorCodigo) {
            return redirect('asignaturas/codigo/'.$asignatura_id)
                ->with('error', trans('elearning::asignaturas/front_lang.error_codigo'));
        }


        return Redirect::to('asignaturas/contenido/' . $asignatura->url_amigable . '/' . $asignatura->id);
        //return redirect('asignaturas/'.$asignatura_id);
    }
}
