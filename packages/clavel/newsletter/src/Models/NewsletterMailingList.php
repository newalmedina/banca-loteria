<?php

namespace App\Modules\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterMailingList extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'newsletter_lists';
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'updated_at', 'created_at'];

    /**
     * Get a newsletter by its slug.
     *
     * @param string $list_slug
     * @return MailingList
     */
    public static function getBySlug($list_slug)
    {
        return static::where('slug', $list_slug)->first();
    }

    /**
     * Get a list by its ID or slug.
     *
     * @param int|string $list
     * @return MailingList
     */
    public static function getList($list)
    {
        if (is_numeric($list)) {
            return static::find($list);
        }
        return static::getBySlug($list);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    /*public function campaigns()
    {
        return $this->hasMany(NewsletterCampaign::class, 'list_id');
    }
    */
    public function campaigns()
    {
        return $this->belongsToMany(NewsletterCampaign::class, 'newsletter_campaign_list', 'list_id', 'campaign_id')
            ->withPivot('list_id')
            ->withTimestamps();
    }

    public function campaignSelected($campaign_id)
    {
        return (self::join("newsletter_campaign_list", "newsletter_lists.id", "=", "newsletter_campaign_list.list_id")
                ->where("newsletter_campaign_list.campaign_id", "=", $campaign_id)
                ->where("newsletter_campaign_list.list_id", "=", $this->attributes["id"])
                ->count()>0) ? true : false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(NewsletterSubscription::class, 'list_id');
    }

    /**
     * Get all the currently subscribed members of this list. This
     * excludes unsubscribed members (obviously), and if required,
     * members who have not opted in.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCurrentSubscriptions()
    {
        $query = $this->subscriptions();
        if ($this->requires_opt_in) {
            $query->where('opted_in', true);
        }
        return $query->with('subscriber')->get();
    }
}
