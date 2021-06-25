<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoAsignaturaUser extends Model
{
    protected $table = "codigo_asignatura_user";
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function codigo()
    {
        return $this->belongsTo('Clavel\Elearning\Models\Codigo', 'codigo_id', 'id');
    }

    public function asignatura()
    {
        return $this->belongsTo('Clavel\Elearning\Models\Asignatura', 'asignatura_id', 'id');
    }
}
