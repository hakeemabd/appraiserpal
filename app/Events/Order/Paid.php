<?php

namespace App\Events\Order;

use Illuminate\Queue\SerializesModels;

class Paid extends OrderEvent
{
    use SerializesModels;

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
