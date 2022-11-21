<?php

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Queue\SerializesModels;

class InvitationError extends OrderEvent
{
    use SerializesModels;

    public $exception;

    /**
     * Create a new event instance.
     *
     * @param Order      $order
     * @param \Exception $e
     */
    public function __construct(Order $order, \Exception $e)
    {
        $this->exception = $e;
        parent::__construct($order);
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
