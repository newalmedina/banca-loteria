<?php

namespace Clavel\Basic\Presenters;

class FrontMenuNavbarRightPresenter extends FrontMenuNavbarPresenter
{
    /**
     * {@inheritdoc }.
     */
    public function getOpenTagWrapper()
    {
        return PHP_EOL . '<ul class="navbar-nav ml-auto navbar-right">' . PHP_EOL;
    }

    /**
     * {@inheritdoc }.
     */
    public function getMenuWithDropDownWrapper($item)
    {
        return '<li  class="nav-item dropdown pull-right">
			      <a class="nav-link" href="#" class="dropdown-toggle" data-toggle="dropdown">
					' . $item->getIcon() . ' ' . $item->title . '
			      	<b class="caret"></strong>
			      </a>
			      <ul class="dropdown-menu">
			      	' . $this->getChildMenuItems($item) . '
			      </ul>
		      	</li>'
            . PHP_EOL;
    }
}
