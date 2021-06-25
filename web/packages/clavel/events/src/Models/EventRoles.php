<?php

namespace App\Modules\Events\Models;

use App\Models\Role;

class EventRoles extends Role
{
    public function eventsSelected($event_id)
    {
        return (self::join("event_role", "roles.id", "=", "event_role.role_id")
                ->where("event_role.event_id", "=", $event_id)
                ->where("event_role.role_id", "=", $this->attributes["id"])
                ->count()>0) ? true : false;
    }
}
