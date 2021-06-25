<?php

namespace Clavel\Posts\Models;

use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    public function post()
    {
        return $this->hasOne('Clavel\Posts\Models\Post');
    }
}
