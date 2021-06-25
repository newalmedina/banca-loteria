<?php

namespace Clavel\NotificationBroker\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTmp extends Model
{
    protected $guarded = [];
    protected $table = "notifications_tmp";

    public function type()
    {
        return $this->belongsTo(NotificationType::class);
    }
}
