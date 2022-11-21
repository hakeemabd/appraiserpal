<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderEvent;
use App\Events\Order\Paid;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAboutPayment implements ShouldQueue
{

    /**
     * Handle the event.
     *
     * @param  OrderEvent $event
     *
     * @return void
     */
    public function handle(OrderEvent $event)
    {
        if ($event instanceof Paid) {
            //notify about success
        }
        else {
            //notify about error
        }
    }
}
