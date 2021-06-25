<?php

namespace Clavel\Basic\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;
    protected $table = 'pages';

    public $translatedAttributes = ['title', 'url_seo', 'body', 'meta_title', 'meta_content'];

    public function pageproviders()
    {
        return $this->hasMany('Clavel\Basic\Models\PageProvider');
    }

    public function getArrayPageProviders()
    {
        $a_metas_providers = [];

        foreach ($this->pageproviders as $key => $value) {
            foreach ($value->translations()->get() as $value2) {
                $a_metas_providers[$value->provider][$value2->locale][$value2->name] = $value2->value;
            }
        }

        return $a_metas_providers;
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'page_role')
            ->withPivot('page_id')
            ->withTimestamps();
    }
}
