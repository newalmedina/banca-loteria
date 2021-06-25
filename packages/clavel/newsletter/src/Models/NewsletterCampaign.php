<?php

namespace App\Modules\Newsletter\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class NewsletterCampaign extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'newsletter_campaigns';
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
    protected $dates = ['scheduled_for', 'sent_at'];
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'list_id' => 'integer',
        'is_scheduled' => 'boolean',
        'sent_count' => 'integer',
        'attachments' => 'json',
    ];

    public function scopeOnlyPending($query)
    {
        $code = NewsletterCampaignState::where("code", 1)->first();
        return $query->where('newsletter_campaign_state_id', '=', $code->id);
    }

    public function scopeOnlySending($query)
    {
        $code = NewsletterCampaignState::where("code", 2)->first();
        return $query->where('newsletter_campaign_state_id', '=', $code->id);
    }

    public function scopeOnlySent($query)
    {
        $code = NewsletterCampaignState::where("code", 3)->first();
        return $query->where('newsletter_campaign_state_id', '=', $code->id);
    }

    public function scopePogramadas($query)
    {
        $code = NewsletterCampaignState::where("code", 1)->first();
        return $query->where('newsletter_campaign_state_id', '=', $code->id)->where("is_scheduled", 1);
    }

    public function mailingList()
    {
        return $this->belongsToMany(NewsletterMailingList::class, 'newsletter_campaign_list', 'campaign_id', 'list_id')
            ->withPivot('campaign_id')
            ->withTimestamps();
    }

    public function newsletter()
    {
        return $this->belongsTo(Newsletter::class, 'newsletter_id');
    }

    public function recipients()
    {
        return $this->Hasmany(NewsletterMailingRecipients::class, "campaign_id");
    }

    public function setNameAttribute($value)
    {
        if (!isset($this->attributes['name']) || $this->attributes['name'] != $value) {
            $this->attributes['name'] = $this->getNonDuplicateName($value);
        }
    }

    public function getNonDuplicateName($name)
    {
        $exists = self::nameExists($name);
        while ($exists) {
            preg_match('/^(?P<name>.+?)(?P<copy>\s+copy\s+(?P<num>\d+))?$/i', $name, $matches);
            $num = isset($matches['num']) ? $matches['num']+1 : 1;
            $name = sprintf('%s copy %d', $matches['name'], $num);
            $exists = self::nameExists($name);
        }
        return $name;
    }

    public static function nameExists($name)
    {
        return static::where('name', $name)->exists();
    }

    public function addAttachments($attachments)
    {
        $this->attachments = (array) $attachments;
        return $this;
    }

    public function attachmentPath($attachment = null)
    {
        return sprintf('%s/%s', config('epistolary.attachments.storage'), $attachment);
    }

    public function markAsSent()
    {
        $code = NewsletterCampaignState::where("code", 3)->first();
        $this->newsletter_campaign_state_id = $code->id;
        $this->sent_at = Carbon::now();
        $this->sent_count = $this->mailingList->getCurrentSubscriptions()->count();
        $this->save();
    }

    public function getIsSentAttribute()
    {
        $code = NewsletterCampaignState::where("code", 3)->first();
        return $this->newsletter_campaign_state_id==$code->id;
    }

    public function getIsSendingAttribute()
    {
        $code = NewsletterCampaignState::where("code", 2)->first();
        return $this->newsletter_campaign_state_id==$code->id;
    }

    public function getIsPreparedAttribute()
    {
        $code = NewsletterCampaignState::where("code", 1)->first();
        return $this->newsletter_campaign_state_id==$code->id;
    }

    public function getIsPendingAttribute()
    {
        $code = NewsletterCampaignState::where("code", 0)->first();
        return $this->newsletter_campaign_state_id==$code->id;
    }

    public function getScheduledForDateFormattedAttribute()
    {
        if (!empty($this->scheduled_for)) {
            $date_start = new Carbon($this->scheduled_for);
            return $date_start->format('d/m/Y');
        }

        return "";
    }

    public function getSentAtDateFormattedAttribute()
    {
        if (!empty($this->sent_at)) {
            $date_start = new Carbon($this->sent_at);
            return $date_start->format('d/m/Y');
        }

        return "";
    }

    public function getSentAtYearAttribute()
    {
        if (!empty($this->sent_at)) {
            $date_start = new Carbon($this->sent_at);
            return $date_start->format('Y');
        }

        return "";
    }

    public function getSentAtMonthAttribute()
    {
        if (!empty($this->sent_at)) {
            $date_start = new Carbon($this->sent_at);
            return $date_start->format('m');
        }

        return "";
    }

    public function getScheduledForTimeFormattedAttribute()
    {
        if (!empty($this->scheduled_for)) {
            $date_start = new Carbon($this->scheduled_for);
            return $date_start->format('H:i');
        }

        return "";
    }
}
