<?php

namespace Clavel\Elearning\Models;

use Illuminate\Database\Eloquent\Model;

class TipoContenido extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;
    protected $table = 'tipo_contenidos';

    public $translatedAttributes = ['nombre','icono'];
}
