<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 1/17/16
 * Time: 8:22 PM
 */

namespace App\Repositories\Criteria;

use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Bosnadev\Repositories\Criteria\Criteria;

class WorkerInvitations extends Criteria
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param            $model
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        // dd('x');

        return $model
            ->with('order', 'order.firstImage', 'group')
            ->where('user_id', $this->userId)
            ->whereNull('deleted_at')
            ->whereNull('accepted_at')
            ->whereNull('rejected_at');
    }
}