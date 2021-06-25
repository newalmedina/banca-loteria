<?php

namespace App\Modules\Newsletter\Contracts;

/**
 * Interface NewsletterPublished
 * @package app\Modules\Newsletter\Contracts
 */
interface NewsletterPublished
{
    /**
     * @param $title
     * @param $body
     * @return mixed
     */
    public function notify($title, $body);
}
