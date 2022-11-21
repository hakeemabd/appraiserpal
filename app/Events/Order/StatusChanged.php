<?php

namespace App\Events\Order;

use App\Models\Order;
use Illuminate\Queue\SerializesModels;

class StatusChanged extends OrderEvent
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Order|integer $order
     * @param               $status
     * @param               $oldStatus
     * @param               $subStatus
     */
//    public function __construct($order, $status, $oldStatus, $subStatus = false)
//    {
//        $this->status = $status;
//        $this->oldStatus = $oldStatus;
//        $this->subStatus = $subStatus;
//        parent::__construct($order);
//    }

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
