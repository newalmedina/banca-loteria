<?php

namespace Clavel\NotificationBroker\Models;

use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    protected $table = "notifications_broker_blacklist";

    const SLUG_SELECT = [
        'sms' => 'SMS',
        'email' => 'eMail'
    ];
}
