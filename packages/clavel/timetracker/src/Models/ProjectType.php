<?php


namespace Clavel\TimeTracker\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    use Translatable;

    public $useTranslationFallback = true;

    public $translatedAttributes = ['name', 'description'];

    protected $table = 'project_types';

    public function scopeActives($query)
    {
        return $query->where("active", true);
    }
}
