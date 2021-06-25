<?php

namespace App\Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;

class EventFavourite extends Model
{
    protected $table = 'event_favorites';

    public function event()
    {
        return $this->belongsTo('App\Modules\Events\Models\Event');
    }

    public function scopeHasFavorite($query)
    {
        return $query->where('user_id', auth()->user()->id);
    }

    public function scopeHasFavoriteInOrder($query)
    {
        return $query
            ->join("events", "event_favorites.event_id", "=", "events.id")
            ->where('event_favorites.user_id', auth()->user()->id)
            ->orderBy("events.date_start", "DESC");
    }
}
