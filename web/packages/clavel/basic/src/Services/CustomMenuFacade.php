<?php namespace Clavel\Basic\Services;

use Illuminate\Support\Facades\Facade;

class CustomMenuFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'customMenu';
    }
}
