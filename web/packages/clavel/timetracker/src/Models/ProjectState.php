<?php

namespace Clavel\TimeTracker\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class ProjectState extends Model
{
    use Translatable;

    public $useTranslationFallback = true;

    public $translatedAttributes = ['name', 'description'];

    protected $table = 'project_states';

    public function scopeActives($query)
    {
        return $query->where("active", true);
    }
}
