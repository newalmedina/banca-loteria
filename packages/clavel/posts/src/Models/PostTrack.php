<?php


namespace Clavel\Posts\Models;

use Illuminate\Database\Eloquent\Model;

class PostTrack extends Model
{
    protected $fillable = ['user_id', 'post_id', 'visits'];

    public function post()
    {
        return $this->hasOne('Clavel\Posts\Models\Post');
    }

    public function user()
    {
        return $this->belongsTo("Clavel\Posts\Models\User");
    }
}
