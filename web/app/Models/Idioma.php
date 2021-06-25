<?php

namespace App\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;

class Idioma extends Model
{
    // Traits
    use \Astrotomic\Translatable\Translatable;

    protected $table = "idiomas";

    public $translatedAttributes = ['name', 'locale'];
    public $useTranslationFallback = true;

    protected $fillable = ['code', 'name', 'active', 'default'];

    protected $dates = ['created_at',
        'updated_at',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeDefault($query)
    {
        return $query->where('default', 1);
    }

    public function getLocaleNameAttribute()
    {
        $translator = $this->translate(App::getLocale());
        if (empty($translator)) {
            return ("");
        }
        return $translator->name;
    }
}
