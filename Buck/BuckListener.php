<?php

namespace Statamic\Addons\Buck;

use Statamic\API\Nav;
use Statamic\Extend\Listener;

class BuckListener extends Listener
{
    public $events = [
        'cp.nav.created' => 'add',
    ];

    public function add($nav)
    {
        $nav->addTo(
            'content',
            Nav::item('Discounts')
                ->route('discounts.index')
                ->icon('shopping-cart')
        );
    }
}
