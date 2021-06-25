<?php

namespace Clavel\TimeTracker\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    public function scopeActives($query)
    {
        return $query->where("active", true);
    }
}
