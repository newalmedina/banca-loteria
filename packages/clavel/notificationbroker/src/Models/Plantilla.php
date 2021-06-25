<?php

namespace Clavel\NotificationBroker\Models;

use Illuminate\Database\Eloquent\Model;

class Plantilla extends Model
{
    protected $table = "notifications_broker_templates";
    protected $fillable = [];
    protected $guarded =[];

    public function getArchivoHumanAttribute()
    {
        return str_replace(".blade.php", "", $this->attributes['archivo']);
    }
}
