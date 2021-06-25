<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdiomaTranslation extends Model
{
    protected $table = 'idioma_translations';

    public $timestamps = false;
    protected $fillable = ['name'];

    public function idioma()
    {
        return $this->belongsTo('App\Models\Idioma');
    }
}
