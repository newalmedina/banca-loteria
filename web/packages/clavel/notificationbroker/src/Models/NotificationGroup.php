<?php

namespace Clavel\NotificationBroker\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificationGroup extends Model
{
    protected $guarded = [];
    protected $table = "notifications_broker_group";

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
