<?php

namespace Clavel\Basic\Presenters;

use Nwidart\Menus\Presenters\Bootstrap\NavbarPresenter;

class FrontMenuNavbarPresenter extends NavbarPresenter
{
    /**
     * {@inheritdoc }.
     */
    public function getOpenTagWrapper()
    {
        return PHP_EOL . '<ul class="nav nav-pills">' . PHP_EOL;
    }
}
