<?php


namespace Clavel\Posts\Models;

use Illuminate\Database\Eloquent\Model;

class PostStat extends Model
{
    protected $fillable = ['post_id', 'visits', 'fecha'];

    public function post()
    {
        return $this->hasOne('Clavel\Posts\Models\Post');
    }
}
