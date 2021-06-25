<?php

namespace Clavel\Basic\Models;

use Illuminate\Database\Eloquent\Model;

class PageProvider extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;
    protected $table = 'page_providers';
    public $timestamps = false;
    protected $fillable = ['page_id', 'provider'];

    public $translatedAttributes = ['name', 'value'];

    public function page()
    {
        return $this->belongsTo('App\Models\Page');
    }
}
