<?php

namespace App\Events\Order;

use Illuminate\Queue\SerializesModels;

class CompleteOrder extends OrderEvent
{
    use SerializesModels;
    public $order;
    public $worker;

    /**
     * Create a new event instance.
     *
     * @param string               $order
     */
    public function __construct($order, $worker)
    {
        $this->order = $order;
        $this->worker = $worker;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
