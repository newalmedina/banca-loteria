<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class Codigo extends Model
{
    protected $table = "codigos";
    protected $fillable = [];
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function users()
    {
        return $this->hasMany('App\Models\User', 'codigo_id', 'id');
    }

    public function usuariosAsignatura()
    {
        return $this->hasMany('Clavel\Elearning\Models\CodigoAsignaturaUser', 'codigo_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'codigo_roles')
            ->withPivot('codigo_id');
    }

    public function rolSelected($role_id)
    {
        if (isset($this->attributes["id"])) {
            return (self::join("codigo_roles", "codigos.id", "=", "codigo_roles.codigo_id")
                    ->where("codigo_roles.role_id", "=", $role_id)
                    ->where("codigo_roles.codigo_id", "=", $this->attributes["id"])
                    ->count()>0) ? true : false;
        } else {
            return false;
        }
    }

    public function asignaturas()
    {
        return $this->belongsToMany('Clavel\Elearning\Models\Asignatura', 'codigo_asignaturas')
            ->withPivot('codigo_id');
    }

    public function asignaturaSelected($asignatura_id)
    {
        if (isset($this->attributes["id"])) {
            return (self::join("codigo_asignaturas", "codigos.id", "=", "codigo_asignaturas.codigo_id")
                    ->where("codigo_asignaturas.asignatura_id", "=", $asignatura_id)
                    ->where("codigo_asignaturas.codigo_id", "=", $this->attributes["id"])
                    ->count()>0) ? true : false;
        } else {
            return false;
        }
    }
}
