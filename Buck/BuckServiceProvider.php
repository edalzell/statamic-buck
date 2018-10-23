<?php

namespace Statamic\Addons\Buck;

use Statamic\Extend\ServiceProvider;
use Statamic\Addons\Buck\Models\Order;

class BuckServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Order::deleting(function ($order) {
            $order->items()->delete();
        });
    }
}
