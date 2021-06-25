<?php
namespace App\Modules\Newsletter\Models;

use App\Models\User;

class NewsletterSubscriber extends User
{
    protected $table = 'users';
    /**
     * Return all the Subscriptions asigned to the User/Subscriber
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscriptions()
    {
        return $this->hasMany(NewsletterSubscription::class, 'user_id', 'id');
    }

    public function hasSubscription($list)
    {
        if (empty($list)) {
            return false;
        }

        foreach ($this->subscriptions as $subscription) {
            if ($list == $subscription->list_id) {
                return true;
            }
        }
        return false;
    }
}
