<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 1/19/16
 * Time: 3:14 PM
 */

namespace App\Events\Order;

use App\Events\Event;
use App\Helpers\GuessOrder;
use Illuminate\Queue\SerializesModels;

class OrderEvent extends Event
{
    use SerializesModels, GuessOrder;
    public $order;

    public function __construct($order)
    {
        $this->order = $this->guessOrder($order);
    }
}