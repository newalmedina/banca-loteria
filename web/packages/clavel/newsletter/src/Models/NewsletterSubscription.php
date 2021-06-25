<?php

namespace App\Modules\Newsletter\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscription extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'newsletter_subscriptions';
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'updated_at', 'created_at'];
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['opted_in_at'];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'list_id' => 'integer',
        'opted_in' => 'boolean',
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function newsletter()
    {
        return $this->belongsTo(NewsletterMailingList::class, 'list_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriber()
    {
        return $this->belongsTo(NewsletterSubscriber::class, "user_id");
    }

    public function listSelected($tag_id)
    {
        if (isset($this->attributes["list_id"])) {
            return (self::join("event_event_tag", "events.id", "=", "event_event_tag.event_id")
                    ->where("event_event_tag.event_tag_id", "=", $tag_id)
                    ->where("event_event_tag.event_id", "=", $this->attributes["id"])
                    ->count()>0) ? true : false;
        } else {
            return false;
        }
    }
}
