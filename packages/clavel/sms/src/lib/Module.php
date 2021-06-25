<?php

namespace Clavel\Sms\lib;

use Clavel\Sms\Sms;

class Module
{
    public $sms;
    public function __construct(Sms $sms)
    {
        $this->sms = $sms;
    }
}
