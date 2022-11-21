<?php

namespace App\Repositories\Criteria;

use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Bosnadev\Repositories\Criteria\Criteria;

class WorkerLog extends Criteria {

    protected $userId;
    
    protected $groupId;
    
    protected $orderId;
    
    protected $status;
    
    public function __construct($userId, $groupId, $orderId, $status)
    {
        $this->userId = $userId;
        $this->groupId = $groupId;
        $this->orderId = $orderId;
        $this->status = $status;
    }

    public function apply($model, Repository $repository)
    {
        return $model->where('group_id', $this->groupId)
            ->where('order_id', $this->orderId)
            ->where('user_id', $this->userId)
            ->where('status', $this->status)
            ->orderBy('id', 'desc');
    }
    
}