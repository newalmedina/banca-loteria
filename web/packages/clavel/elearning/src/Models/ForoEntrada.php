<?php

namespace Clavel\Elearning\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class ForoEntrada extends Model
{
    protected $table = "foro_entradas";
    protected $fillable = [];
    protected $guarded = [];

    public function scopeActivos($query)
    {
        return $query->where("visible", 1);
    }

    public function scopeHiloPadre($query)
    {
        return $query->whereNull("parent_id");
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function getCreacionAttribute()
    {
        if ($this->created_at != null && $this->created_at != '') {
            return $this->created_at->format('d/m/Y H:i:s');
        }

        return "";
    }

    public function getRespuestasAttribute()
    {
        return self::where("parent_id", "=", $this->id)->activos()->count();
    }

    public function getUltimo()
    {
        Carbon::setLocale(App::getLocale());
        $a_return = array(
            "Usuario" => $this->user->userProfile->full_name,
            "Fecha" => $this->updated_at->diffForHumans()
        );
        $last = self::where("parent_id", "=", $this->id)->activos()->orderby("updated_at", "DESC")->first();
        if (!is_null($last) || !empty($last)) {
            $a_return["Usuario"] = $last->user->userProfile->full_name;
            $a_return["Fecha"] = $last->updated_at->diffForHumans();
        }

        return $a_return;
    }


    public function getCreacionHumanosAttribute()
    {
        Carbon::setLocale(App::getLocale());
        if ($this->created_at != null && $this->created_at != '') {
            return $this->created_at->diffForHumans();
        }

        return "";
    }
}
