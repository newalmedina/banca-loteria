<?php

namespace Clavel\Basic\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemTypes extends Model
{
    //
    protected $table = 'menu_item_types';

    public function menuItem()
    {
        return $this->hasMany('App\Models\MenuItem', 'item_type_id', 'id');
    }
}
