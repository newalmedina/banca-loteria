<?php

namespace Clavel\Posts\Models;

use Illuminate\Database\Eloquent\Model;

class PostTranslation extends Model
{
    public $timestamps = false;

    public function post()
    {
        return $this->belongsTo('Clavel\Posts\Models\Post');
    }

    public function setTitleAttribute($title)
    {
        $this->attributes['title'] = $title;
        $this->attributes['url_seo'] = str_slug($title);
    }
}
