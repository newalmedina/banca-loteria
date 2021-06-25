<?php

namespace Clavel\NotificationBroker\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $useTranslationFallback = true;

    public $translatedAttributes = ['title', 'subject', 'body'];

    protected $table = 'notifications_broker_type';
}
