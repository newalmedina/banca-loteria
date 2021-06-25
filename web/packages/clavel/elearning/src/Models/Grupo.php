<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = "grupos";
    protected $fillable = [];
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }

    public function userPivot()
    {
        return $this->belongsToMany('App\Models\User', 'grupo_users')
            ->withPivot('grupo_id');
    }

    public function profesorPivot()
    {
        return $this->belongsToMany('App\Models\User', 'grupo_profesor')
            ->withPivot('grupo_id');
    }

    public function userSelected($user_id)
    {
        if (isset($this->attributes["id"])) {
            return (self::join("grupo_users", "grupos.id", "=", "grupo_users.grupo_id")
                    ->where("grupo_users.user_id", "=", $user_id)
                    ->where("grupo_users.grupo_id", "=", $this->attributes["id"])
                    ->count()>0) ? true : false;
        } else {
            return false;
        }
    }

    public function userGrupos($user_id)
    {
        return self::join("grupo_users", "grupos.id", "=", "grupo_users.grupo_id")
            ->where("grupo_users.user_id", "=", $user_id)
            ->where("grupos.activo", "1");
    }

    public function profesorSelected($user_id)
    {
        if (isset($this->attributes["id"])) {
            return (self::join("grupo_profesor", "grupos.id", "=", "grupo_profesor.grupo_id")
                    ->where("grupo_profesor.user_id", "=", $user_id)
                    ->where("grupo_profesor.grupo_id", "=", $this->attributes["id"])
                    ->count()>0) ? true : false;
        } else {
            return false;
        }
    }

    public function profesorGrupos($user_id)
    {
        return self::join("grupo_profesor", "grupos.id", "=", "grupo_profesor.grupo_id")
            ->where("grupo_profesor.user_id", "=", $user_id)
            ->where("grupos.activo", "1");
    }

    public function alumnoSelected($user_id)
    {
        if (isset($this->attributes["id"])) {
            return (self::join("grupo_users", "grupos.id", "=", "grupo_users.grupo_id")
                    ->where("grupo_users.user_id", "=", $user_id)
                    ->where("grupo_users.grupo_id", "=", $this->attributes["id"])
                    ->count()>0) ? true : false;
        } else {
            return false;
        }
    }
}
