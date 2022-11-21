<?php

namespace App\Listeners\Order;

use App\Components\OrderProcessor;
use App\Events\Order\StatusChanged;

class Process
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
     * @param  StatusChanged $event
     *
     * @return void
     */
    public function handle(StatusChanged $event)
    {
        $order = $event->order;
        if ($this->orderProcessor->canProcess($order)) {
            $this->orderProcessor->process($order);
        }
    }
}