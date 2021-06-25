<?php namespace Clavel\Elearning\Models;

use Spatie\MediaLibrary\Models\Media as BaseMedia;

// https://github.com/spatie/laravel-medialibrary/issues/75
// https://github.com/spatie/laravel-medialibrary/issues/1061
// https://docs.spatie.be/laravel-medialibrary/v7/advanced-usage/using-your-own-model/

class SpatieMedia extends BaseMedia
{
    /**
     * Boot events
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($media) {
            if ($user = auth()->getUser()) {
                $media->user_id = $user->id;
            }
        });
    }

    /**
     * User relationship (one-to-one)
     * @return App\Models\User
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
