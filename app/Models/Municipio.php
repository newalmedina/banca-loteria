<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = "municipios";

    public function scopeActive($query)
    {
        return $query->where("active", true);
    }

    public function provincia()
    {
        return $this->belongsTo('App\Models\Provincia');
    }
}
