<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 1/13/16
 * Time: 5:48 PM
 */

namespace App\Exceptions;


use Illuminate\Queue\SerializesModels;

class NoWorkersInvitationException extends \Exception
{

    use SerializesModels;

    protected $orderId;
    protected $groupId;

    public function __construct($orderId, $groupId, $code = 0, \Exception $previous = null)
    {
        $this->orderId = $orderId;
        $this->groupId = $groupId;
        $message = "No users for order=$orderId and group=$groupId found to be invited";
        parent::__construct($message, $code, $previous);
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }
}