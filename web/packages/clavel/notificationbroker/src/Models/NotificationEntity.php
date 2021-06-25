<?php

namespace Clavel\NotificationBroker\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationEntity extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $table = "notifications_broker_entity";
}
