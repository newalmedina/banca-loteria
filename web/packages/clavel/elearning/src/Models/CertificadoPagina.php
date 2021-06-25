<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class CertificadoPagina extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;
    protected $table = 'certificado_paginas';
    public $timestamps = false;

    public $translatedAttributes = ['plantilla', 'body'];
}
