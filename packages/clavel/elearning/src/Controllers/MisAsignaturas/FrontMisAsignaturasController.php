<?php

namespace Clavel\Elearning\Controllers\MisAsignaturas;

use App\Http\Controllers\FrontController;
use Clavel\Elearning\Models\Asignatura;
use Clavel\Elearning\Models\Curso;
use Clavel\Elearning\Models\TrackAsignatura;
use Clavel\Elearning\Services\TrackContent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class FrontMisAsignaturasController extends FrontController
{
    public function index()
    {
        $page_title = trans("elearning::misasignaturas/front_lang.mis_asignaturas");
        $user_id = auth()->user()->id;
        $track_asignaturas = TrackAsignatura::where("user_id", $user_id)->get();
        $aprobadas = TrackAsignatura::where("user_id", $user_id)->aprobadas()->count();
        $pendientes = TrackAsignatura::where("user_id", $user_id)->pendientes()->count();
        $suspendidas = TrackAsignatura::where("user_id", $user_id)->suspendidas()->count();

        return view(
            "elearning::misasignaturas.front_index",
            compact("page_title", 'track_asignaturas', 'aprobadas', 'pendientes', 'suspendidas')
        );
    }

    public function getData()
    {
        $locale = config('app.default_locale');

        $asignaturas = TrackAsignatura::select(
            array(
                'asignaturas.id',
                'asignatura_translations.titulo',
                'fecha_inicio',
                'asignatura_translations.creditos',
                'asignatura_translations.url_amigable',
                'nota',
                'completado',
                'aprobado'
            )
        )
            ->where("user_id", Auth::user()->id)
            ->join("asignaturas", function ($join) {
                $join->on("asignaturas.id", "=", "track_asignatura.asignatura_id");
                $join->on("asignaturas.activo", "=", DB::raw("'1'"));
            })
            ->join('asignatura_translations', function ($join) use ($locale) {
                $join->on('asignatura_translations.asignatura_id', '=', 'asignaturas.id');
                $join->on('asignatura_translations.locale', '=', DB::raw("'" . $locale . "'"));
            });


        return Datatables::of($asignaturas)
            ->editColumn('titulo', function ($row) {
                $strInfo = "";
                if ($row->completado == '1' && $row->aprobado == '1') {
                    $strInfo = '<span class="label label-success label-xs">' .
                        trans("elearning::misasignaturas/front_lang.ap") . '</span>';
                }
                if ($row->completado == '1' && $row->aprobado == '0') {
                    $strInfo = '<span class="label label-danger label-xs">' .
                        trans("elearning::misasignaturas/front_lang.sus") . '</span>';
                }
                return $row->titulo . " " . $strInfo;
            })
            ->editColumn('fecha_inicio', function ($row) {
                return $row->fecha_inicio_normal_formatted;
            })
            ->addColumn('grafica_1', function ($row) {
                $tracking = new TrackContent("", "", $row->id);
                $asignatura = Asignatura::find($row->id);
                $percent_content = $asignatura->getPercentContents($tracking->convocatoria_id);
                $str_Return = '<span class="badge badge-danger badge-xs">'
                    . trans("elearning::misasignaturas/front_lang.no_disponible") . '</span>';
                if ($asignatura->getActiva()["activa"]) {
                    $str_Return = '<div class="progress">';
                    $str_Return .= '<div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="' .
                        $percent_content . '" aria-valuemin="0" aria-valuemax="100" style="width: ' .
                        $percent_content . '%;"></div><br>' . $percent_content . '%';
                    $str_Return .= '</div>';
                }

                return $str_Return;
            })
            ->addColumn('grafica_2', function ($row) {
                $tracking = new TrackContent("", "", $row->id);
                $asignatura = Asignatura::find($row->id);
                $progress_percent = round(($tracking->calcularStatsAsignatura(
                    $row->id,
                    $tracking->convocatoria_id
                )["nota"] * 10), 2);
                $str_Return = '<span class="badge badge-danger badge-xs">' .
                    trans("elearning::misasignaturas/front_lang.no_disponible") . '</span>';
                if ($asignatura->getActiva()["activa"]) {
                    $str_Return = '<div class="progress">';
                    $str_Return .= '<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="' .
                        $progress_percent . '" aria-valuemin="0" aria-valuemax="100" style="width: ' .
                        $progress_percent . '%;"></div><br>' . $progress_percent . '%';
                    $str_Return .= '</div>';
                }
                return $str_Return;
            })
            ->addColumn('actions', function ($row) {
                $tracking = new TrackContent("", "", $row->id);
                $str_Return = '<button class="btn btn-success btn-sm"
                onclick="javascript:window.location=\'' .
                    url('asignaturas/detalle/' . $row->url_amigable . '/' . $row->id) . '\';"
                data-content="' . trans('elearning::misasignaturas/front_lang.ver_asignatura') . '"
                data-placement="right" data-toggle="popover">
                <i class="fa fa-play-circle"></i></button>&nbsp;&nbsp;';
                if ($row->aprobado && $tracking->getInformacionConvocatoria(
                    $tracking->convocatoria_id
                )->certificado_id != '') {
                    $str_Return .= '<button class="btn btn-primary btn-sm"
                    onclick="javascript:window.location=\'' . url('asignatura/'
                        . $row->url_amigable . '/' . $row->id . '/generarCertificado') . '\';"
                    data-content="' . trans('elearning::misasignaturas/front_lang.certificado') . '"
                    data-placement="left" data-toggle="popover">
                    <i class="fa  fa-graduation-cap" aria-hidden="true"></i></button>';
                }
                return $str_Return;
            })
            ->rawColumns(['titulo', 'grafica_1', 'grafica_2', 'actions'])
            ->make();
    }

    public function getDataCursos()
    {
        $locale = config('app.default_locale');

        $cursos = Curso::join("curso_translations", "curso_translations.curso_id", "cursos.id")
            ->select("cursos.id", "curso_translations.nombre", "cursos.certificado_id")
            ->where("curso_translations.locale", $locale)->where("activo", 1);

        return Datatables::of($cursos)
            ->addColumn('actions', function ($row) {
                if ($row->checkCertificado()) {
                    $str_Return = '<button class="btn btn-primary btn-sm" onclick="javascript:window.location=\'' .
                    url('/curso/' . $row->id . '/certificado-curso') . '\';">' .
                    trans('elearning::misasignaturas/front_lang.certificado') . '</button>';
                } else {
                    $str_Return = trans("elearning::misasignaturas/front_lang.no_certificado");
                }
                return $str_Return;
            })
            ->rawColumns(['actions'])
            ->make();
    }
}
