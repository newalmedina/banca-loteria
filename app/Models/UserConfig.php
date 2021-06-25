<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserConfig extends Model
{
    protected $table = "user_configs";

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
