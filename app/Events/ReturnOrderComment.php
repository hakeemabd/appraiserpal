<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class ReturnOrderComment extends Event
{
    use SerializesModels;
    public $comment;

    /**
     * Create a new event instance.
     *
     * @param string $comment
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
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
