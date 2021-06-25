<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class CertificadoPaginaTranslation extends Model
{
    protected $table = 'certificado_pagina_translations';
    public $timestamps = false;
    protected $fillable = [];
    protected $guarded = [];

    public function elementosPagina()
    {
        return $this->hasMany('Clavel\Elearning\Models\CertificadoPaginaTranslationsElements');
    }
}
