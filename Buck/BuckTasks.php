<?php

namespace Statamic\Addons\Buck;

use Statamic\Extend\Tasks;
use Statamic\Addons\Buck\Models\Order;
use Illuminate\Console\Scheduling\Schedule;

class BuckTasks extends Tasks
{
    /**
     * Define the task schedule
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    public function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            Order::abandoned()->get()->each(function ($order, $key) {
                $order->delete();
            });
        })->daily();
    }
}
