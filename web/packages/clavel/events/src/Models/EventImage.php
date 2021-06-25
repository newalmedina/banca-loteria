<?php

namespace App\Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;

class EventImage extends Model
{
    public function event()
    {
        return $this->hasOne('App\Modules\Events\Models\Events');
    }
}
