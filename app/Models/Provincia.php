<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table = 'provincias';

    public function scopeActive($query)
    {
        return $query->where("active", true);
    }

    public function municipios()
    {
        return $this->hasMany('App\Models\Municipio');
    }
}
