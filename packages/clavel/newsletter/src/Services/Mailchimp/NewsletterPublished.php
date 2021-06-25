<?php
/**
 * Created by PhpStorm.
 * User: Jose Juan
 * Date: 23/09/2017
 * Time: 11:36
 */

namespace app\Modules\Newsletter\Services\Mailchimp;

use app\Modules\Newsletter\Contracts\NewsletterPublished as NewsletterPublishedInterface;
use Mailchimp;

/**
 * Class NewsletterPublished
 * @package app\Modules\Newsletter\Services\Mailchimp
 */
class NewsletterPublished implements NewsletterPublishedInterface
{
    const NEWSLETTER_SUBSCRIBERS_ID = 'fffced8e58';

    /**
     * @var Mailchimp
     */
    protected $maichimp;


    /**
     * NewsletterPublished constructor.
     * @param Mailchimp $maichimp
     */
    public function __construct(Mailchimp $maichimp)
    {
        $this->maichimp = $maichimp;
    }


    /**
     * @param $title
     * @param $body
     * @return mixed
     */
    public function notify($title, $body)
    {
        $options = [
            'list_id' => self::NEWSLETTER_SUBSCRIBERS_ID,
            'subject' => 'Newsletter form Clavel: '. $title,
            'from_name' => 'Clavel CMS',
            'from_email' => 'info@aduxia.com',
            'to_name' => 'Clavel CMS Newsletter Subscribers'
        ];

        $content = [
            'html' => $body,
            'text' => strip_tags($body)
        ];

        $campaign = $this->maichimp->campaigns->create('regular', $options, $content);

        $this->maichimp->campaigns->send($campaign['id']);
    }
}
