<?php

namespace App\Events\Order;

use Illuminate\Queue\SerializesModels;

class CreateOrder extends OrderEvent
{
    use SerializesModels;
    public $order;

    /**
     * Create a new event instance.
     *
     * @param string               $order
     */
    public function __construct($order)
    {
        $this->order = $order;
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
