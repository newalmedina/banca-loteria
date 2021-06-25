<?php

namespace Clavel\Elearning\Services;

use App\Models\User;
use Clavel\Elearning\Models\Codigo;
use Clavel\Elearning\Models\Asignatura;
use Clavel\Elearning\Models\CodigoAsignaturaUser;

class AsignaturaService
{

    /**
     * accesoAsignaturaCodigo
     * Verifica si un usuario tiene acceso a una asignatura mediante codigo
     * 1.- Si la asignatura tiene código de acceso verifica si el usuario lo ha introducido
     * 2.- Si el usuario tiene el permiso de frontend-asignaturas-convocatoria-premium
     * puede acceder fuera de convocatoria
     *
     * @param  mixed $asignatura
     * @param  mixed $user
     * @return void
     */
    public static function accesoAsignaturaCodigo(Asignatura $asignatura, User $user)
    {
        // Si la asignatura no requiere código permitimos
        if (!$asignatura->requiere_codigo) {
            return true;
        }

        // Verificamos que el codigo que tenemos es bueno
        $codigAsignaturaAlumno = CodigoAsignaturaUser::where('asignatura_id', $asignatura->id)
            ->where('user_id', $user->id)
            ->first();
        if (!empty($codigAsignaturaAlumno)) {
            return true;
        }



        return false;
    }
}
