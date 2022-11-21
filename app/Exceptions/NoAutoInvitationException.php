<?php

namespace App\Exceptions;


class NoAutoInvitationException extends NoWorkersInvitationException
{
    protected $orderId;
    protected $groupId;

    public function __construct($orderId, $groupId, $code = 0, \Exception $previous = null)
    {
        $this->orderId = $orderId;
        $this->groupId = $groupId;
        $message = "Auto invitation is not possible for order=$orderId. Tried to invite to group=$groupId";
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