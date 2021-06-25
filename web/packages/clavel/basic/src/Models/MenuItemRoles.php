<?php

namespace Clavel\Basic\Models;

use App\Models\Role;

class MenuItemRoles extends Role
{
    public function menusSelected($menu_item_id)
    {
        return (self::join("menu_items_role", "roles.id", "=", "menu_items_role.role_id")
                ->where("menu_items_role.menu_item_id", "=", $menu_item_id)
                ->where("menu_items_role.role_id", "=", $this->attributes["id"])
                ->count()>0) ? true : false;
    }
}
