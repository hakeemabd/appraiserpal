<?php

namespace App\Events;

use App\User;
use Cartalyst\Sentinel\Activations\EloquentActivation;
use Illuminate\Queue\SerializesModels;

class UserCreated extends Event
{
    use SerializesModels;

    public $user;
    public $activation;
    public $password;

    /**
     * Create a new event instance.
     *
     * @param User               $user
     * @param EloquentActivation $activation
     * @param                    $password
     */
    public function __construct(User $user, EloquentActivation $activation, $password)
    {
        $this->user = $user;
        $this->activation = $activation;
        $this->password = $password;
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
