<?php

namespace Clavel\TimeTracker\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectStateTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'project_state_translations';

    public function projectState()
    {
        return $this->belongsTo('App\Models\ProjectState');
    }
}
