<?php

namespace Clavel\Basic\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    //
    public function items()
    {
        return $this->hasMany('Clavel\Basic\Models\MenuItem');
    }

    public function itemsActiveRoot()
    {
        return $this->hasMany('Clavel\Basic\Models\MenuItem')->where("status", "=", "1")->whereNull("parent_id");
        //return $this->hasMany('Clavel\Basic\Models\MenuItem')->where("status", "=", "1")->where("depth", "=", "0");
    }
}
