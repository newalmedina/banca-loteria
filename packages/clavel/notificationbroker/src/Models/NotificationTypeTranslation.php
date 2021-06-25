<?php

namespace Clavel\NotificationBroker\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTypeTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'notifications_broker_type_translations';

    public function notificationType()
    {
        return $this->belongsTo('Clavel\NotificationBroker\Models\NotificationType');
    }
}
