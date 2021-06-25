<?php

namespace Clavel\Posts\Models;

use App\Models\Role;

class PostRoles extends Role
{
    public function postsSelected($post_id)
    {
        return (self::join("post_role", "roles.id", "=", "post_role.role_id")
                ->where("post_role.post_id", "=", $post_id)
                ->where("post_role.role_id", "=", $this->attributes["id"])
                ->count()>0) ? true : false;
    }
}
