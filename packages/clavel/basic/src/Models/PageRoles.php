<?php

namespace Clavel\Basic\Models;

use App\Models\Role;

class PageRoles extends Role
{


    /**
     * Return all the Roles asigned to all Pages
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function pages()
    {
        return $this->belongsToMany('Clavel\Basic\Models\Page', 'page_role', 'role_id')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    /**
     * Return all the Roles asigned to a Page
     * @param $page_id
     * @return bool
     */
    public function pagesSelected($page_id)
    {
        return (self::join("page_role", "roles.id", "=", "page_role.role_id")
                ->where("page_role.page_id", "=", $page_id)
                ->where("page_role.role_id", "=", $this->attributes["id"])
                ->count()>0) ? true : false;
    }
}
