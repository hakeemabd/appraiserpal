<?php

namespace App\Events\Order;

use App\Events\Event;
use App\Models\Order;
use App\Models\Worklog;
use App\User;
use Illuminate\Queue\SerializesModels;

class WorkerDeadline extends Event
{
    use SerializesModels;

    public $worklog;
    
    /**
     * Create a new event instance.
     *
     * @param Worklog $worklog
     */
    public function __construct(Worklog $worklog)
    {
        $this->worklog = $worklog;
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
