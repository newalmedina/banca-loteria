<?php

namespace App\Modules\Newsletter\Services\Mailchimp;

use App\Modules\Newsletter\Contracts\NewsletterList as NewsletterListInterface;
use Mailchimp;

/**
 * Class NewsletterList
 * @package App\Modules\Newsletter\Services\Mailchimp
 */
class NewsletterList implements NewsletterListInterface
{
    /**
     * @var Mailchimp
     */
    protected $mailchimp;

    protected $lists = [
      'newsletterSubscribers' => 'fffced8e58'
    ];

    /**
     * NewsletterList constructor.
     * @param Mailchimp $mailchimp
     */
    public function __construct(Mailchimp $mailchimp)
    {
        $this->mailchimp = $mailchimp;
    }


    /**
     * Subscribe a user to a Mailchimp list
     *
     * @param $listName
     * @param $email
     * @return mixed
     */
    public function subscribeTo($listName, $email)
    {
        return $this->mailchimp->lists->subscribe(
            $this->lists[$listName],
            ['email' => $email],
            null, // merge vars
            'html', // email type
            false, // require double opt in?
            true // update existing customers?
        );
    }

    /**
     * @param $listName
     * @param $email
     * @return mixed
     */
    public function unsubscribeFrom($listName, $email)
    {
        return $this->mailchimp->lists->unsubscribe(
            $this->lists[$listName],
            ['email' => $email],
            false, // delete the member permanently
            false, // send goodbye email?
            false // send unsubscribe notification email?
        );
    }
}
