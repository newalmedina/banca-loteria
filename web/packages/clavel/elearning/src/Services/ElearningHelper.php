<?php namespace Clavel\Elearning\Services;

use Carbon\Carbon;
use Clavel\Elearning\Models\Asignatura;
use Clavel\Elearning\Models\Convocatoria;
use Illuminate\Support\Facades\Auth;

class ElearningHelper
{
    public $id = "";
    public $convocatoria_id = "";

    public static function timeToFinishAssignatura($asignatura_id, $convocatoria_id, $user)
    {
        // Obtenemos la asignatura
        $asignatura = Asignatura::active()
            ->findorFail($asignatura_id);

        // obtenemos la convocatoria sobre la que estamos trabajando
        $convocatoria = Convocatoria::findorFail($convocatoria_id);

        // Obtenemos a partir de la convocatoria el acceso que del usuario, es decir, obtendremos la fecha del primer
        // acceso a la plataforma por parte del usuario
        $track_asignatura = $asignatura->track()
            ->where("user_id", "=", $user->id)
            ->where("convocatoria_id", "=", $convocatoria->id)
            ->first();

        // Obtenemos los días que tiene el usuario para visualizar los contenidos
        $limite_finalizacion = $convocatoria->limite_finalizacion;
        // Si hemos puesto 0 o nulo en días para realizar el examen devolvemos -1 para indicar que no hay limitaciones
        if (empty($limite_finalizacion)) {
            return -1;
        }

        if (!empty($track_asignatura)) {
            $fecha_fin = Carbon::createFromFormat('Y-m-d H:i:s', $track_asignatura->fecha_inicio)
                ->addDay($limite_finalizacion);
            $diff_in_minutes = $fecha_fin->diffInMinutes(Carbon::now());

            if ($diff_in_minutes<0) {
                $diff_in_minutes = 0;
            }
            return ($diff_in_minutes);
        }
    }
}
