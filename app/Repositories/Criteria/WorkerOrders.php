<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 1/17/16
 * Time: 8:22 PM
 */

namespace App\Repositories\Criteria;

use App\Models\Order;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Bosnadev\Repositories\Criteria\Criteria;
use Illuminate\Support\Facades\DB;

class WorkerOrders extends Criteria
{
    protected $workerId;
    protected $all;

    public function __construct($workerId, $all = false)
    {
        $this->workerId = $workerId;
        $this->all = $all;
    }

    /**
     * @param            $model
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $workerId = $this->workerId;
        $condition = $model
            ->select(DB::raw('DISTINCT orders.*,
            oa_cur_status.status group_status,
            groups.name as group_name,
            worklogs.deadline as due_at'))
            ->join(DB::raw('order_assignments oa_cur_user'), function ($join) use ($workerId) {
                $join->on('orders.id', '=', 'oa_cur_user.order_id')
                    ->where('oa_cur_user.user_id', '=', $workerId);
            })
            ->join(DB::raw('order_assignments oa_cur_status'), function ($join) {
                $join->on('orders.id', '=', 'oa_cur_status.order_id')
                    ->where('oa_cur_status.active', '=', 1);
            })
            ->join('worklogs', 'oa_cur_status.worklog_id', '=', 'worklogs.id')
            ->join('groups', 'oa_cur_status.group_id', '=', 'groups.id');
        if (!$this->all) {
            $condition->whereNotIn('orders.status', [Order::STATUS_ARCHIVED, Order::STATUS_CANCELED, Order::STATUS_DELIVERED]);
        }
        return $condition;
    }
}