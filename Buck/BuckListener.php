<?php

namespace Statamic\Addons\Buck;

use Statamic\API\Nav;
use Statamic\Extend\Listener;

class BuckListener extends Listener
{
    public $events = [
        'cp.nav.created' => 'add',
    ];

    /**
     * Undocumented function
     *
     * @param \Statamic\CP\Navigation\Nav $nav
     *
     * @return void
     */
    public function add($nav)
    {
        /*
            I'd like to add the store as the second in the nav.

            To do that we have to break up the nav's items after `content`,
            then merge in the `store` and the remaining nav items

            @todo gotta be a better way to do this
        */
        $storeNav = Nav::item('store')->title('Store');
        $discountItem = Nav::item('discounts')
            ->title('Discounts')
            ->route('discounts.index')
            ->icon('calculator');
        $settingsItem = Nav::item('buck:settings')
            ->title('Settings')
            ->route('addon.settings', 'buck')
            ->icon('sound-mix');

        // if the product collection has been defined, add it to the store nav
        if ($slug = $this->getConfig('product_collection')) {
            $productsNav = $nav->get('content.collections.collections:' . $slug);
            $productsNav->title('Products')->icon('price-tag');
            $storeNav->add($productsNav);
        }

        // need a copy of the nav array to do the splicin'
        $contentNav = $nav->tree->all();

        // this removes all but the first item, and returns the remaining ones
        $remainingNav = array_splice($contentNav, 1, 2);

        // now merge those all back together
        $nav->tree = collect(
            array_merge(
                $contentNav,
                ['store' => $storeNav],
                $remainingNav
            )
        );

        $nav->addTo('store', $discountItem);
        $nav->addTo('store', $settingsItem);
    }
}
