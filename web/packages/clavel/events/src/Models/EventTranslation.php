<?php

namespace App\Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;

class EventTranslation extends Model
{
    public $timestamps = false;

    public function setTitleAttribute($title)
    {
        $this->attributes['title'] = $title;
        $this->attributes['url_seo'] = str_slug($title);
    }
}
