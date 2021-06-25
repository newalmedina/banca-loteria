<?php
/**
 * Created by PhpStorm.
 * User: Jose Juan
 * Date: 08/10/2017
 * Time: 18:32
 */

namespace App\Modules\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterMailingRecipients extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'newsletter_campaign_recipients';
    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'updated_at', 'created_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function campaign()
    {
        return $this->belongsTo(NewsletterCampaign::class, "id", "campaign_id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscriber()
    {
        return $this->belongsTo(NewsletterSubscriber::class, "user_id", "id");
    }
}
