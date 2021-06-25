<?php
namespace App\Modules\Newsletter\Contracts;

interface NewsletterList
{
    /**
     * @param $listName
     * @param $mail
     * @return mixed
     */
    public function subscribeTo($listName, $mail);

    /**
     * @param $listName
     * @param $email
     * @return mixed
     */
    public function unsubscribeFrom($listName, $email);
}
