<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class TipoModulo extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;
    protected $table = 'tipo_modulos';

    public $translatedAttributes = ['nombre'];
}
