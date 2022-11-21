<?php

namespace App\Listeners\Order;

use App\Components\OrderProcessor;
use App\Events\Order\Created;
use App\Events\Order\OrderEvent;

class Initialize
{
    /**
     * @var OrderProcessor
     */
    protected $orderProcessor;


    /**
     * Create the event listener.
     *
     * @param OrderProcessor $orderProcessor
     */
    public function __construct(OrderProcessor $orderProcessor)
    {
        $this->orderProcessor = $orderProcessor;
    }

    /**
     * Handle the event.
     *
     * @param  Created $event
     *
     * @return void
     */
    public function handle(OrderEvent $event)
    {
        $order = $event->order;
        if ($this->orderProcessor->canProcess($order)) {
            $this->orderProcessor->initialize($order);
            $this->orderProcessor->process($order);
        }
    }
}