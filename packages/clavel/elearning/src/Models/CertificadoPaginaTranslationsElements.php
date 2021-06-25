<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class CertificadoPaginaTranslationsElements extends Model
{
    protected $table = 'certificado_pagina_translations_elementos';
    public $timestamps = false;

    protected $fillable = [];
    protected $guarded = [];

    public function paginaTranslation()
    {
        return $this->belongsTo(
            'Clavel\Elearning\Models\CertificadoPaginaTranslation',
            'certificado_pagina_translation_id',
            'id'
        );
    }
}
