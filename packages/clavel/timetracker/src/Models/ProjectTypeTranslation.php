<?php


namespace Clavel\TimeTracker\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTypeTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'project_type_translations';

    public function projectType()
    {
        return $this->belongsTo('App\Models\ProjectType');
    }
}
