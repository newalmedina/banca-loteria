<?php

namespace Clavel\Posts\Models;

use Illuminate\Database\Eloquent\Model;

class PostTag extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;

    public $translatedAttributes = ['tag'];

    public function scopeActives($query)
    {
        return $query->where("active", 1);
    }

    public function posts()
    {
        return $this->belongsToMany('Clavel\Posts\Models\Post');
    }
}
