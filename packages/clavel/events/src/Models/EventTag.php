<?php

namespace App\Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;

class EventTag extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;

    public $translatedAttributes = ['tag'];

    public function scopeActives($query)
    {
        return $query->where("active", 1);
    }

    public function events()
    {
        return $this->belongsToMany('App\Modules\Events\Models\Events');
    }
}
