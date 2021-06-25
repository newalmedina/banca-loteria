<?php

namespace Clavel\Posts\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    public function post()
    {
        return $this->hasOne('Clavel\Posts\Models\Post');
    }

    public function scopeOnlyParents($query)
    {
        return $query->where('parent_id', 0);
    }

    public function scopeChildren($query, $parent_id)
    {
        return $query->where('parent_id', $parent_id);
    }

    private function dateLocale()
    {
        /* Esto es una chapuza que tengo que mirar como solucionar */
        Carbon::setLocale(app()->getLocale());
        $strLocale = app()->getLocale()."_".strtoupper(app()->getLocale());
        setlocale(LC_CTYPE, $strLocale.'.utf8');
        setlocale(LC_ALL, $strLocale.'.utf8');
        /* Fin de la chapuza */
    }

    public function getDateCommentFormattedAttribute()
    {
        $this->dateLocale();
        $dateInfo = new Carbon($this->created_at);
        return ucfirst($dateInfo->formatLocalized('%B %d, %Y '.trans("posts::front_lang.a_las").' %H:%M'));
    }

    public function user()
    {
        return $this->belongsTo("Clavel\Posts\Models\User");
    }
}
