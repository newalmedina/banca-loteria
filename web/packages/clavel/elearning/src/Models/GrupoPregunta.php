<?php namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoPregunta extends Model
{
    protected $table = 'grupos_preguntas';

    public function contenido()
    {
        return $this->belongsTo('App\Models\Contenido', 'contenido_id', 'id');
    }

    public function scopeDelContenido($query, $contenido_id)
    {
        $query->where("contenido_id", $contenido_id);

        return $query;
    }
}
