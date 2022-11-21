<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 1/21/16
 * Time: 6:49 PM
 */

namespace App\Helpers;


use App\Exceptions\OrderNotFoundException;
use App\Models\Order;

trait GuessOrder
{
    protected function guessOrder($order)
    {
        if (is_numeric($order)) {
            $order = Order::find($order);
            if (!$order) {
                throw new OrderNotFoundException("Can't find order $order");
            }
        }
        return $order;
    }
}