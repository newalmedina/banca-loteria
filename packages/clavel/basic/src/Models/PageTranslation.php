<?php

namespace Clavel\Basic\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    protected $table = 'page_translations';
    public $timestamps = false;

    public function setTitleAttribute($title)
    {
        $this->attributes['title'] = $title;
        $this->attributes['url_seo'] = Str::slug($title);
    }
}
