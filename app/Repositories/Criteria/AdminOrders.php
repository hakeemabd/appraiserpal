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

class AdminOrders extends Criteria
{

    /**
     * @param            $model
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model
            ->select(DB::raw('orders.*,
            oa_cur_status.status group_status,
            worklogs.deadline as due_at,

            workers.first_name worker_first_name,
            workers.last_name worker_last_name,
            workers.email as worker_email,

            groups.name as group_name'))
            ->leftJoin(DB::raw('order_assignments oa_cur_status'), function ($join) {
                $join->on('orders.id', '=', 'oa_cur_status.order_id')
                    ->where('oa_cur_status.active', '=', 1);
            })
            ->leftJoin('groups', 'oa_cur_status.group_id', '=', 'groups.id')
            ->leftJoin('worklogs', 'oa_cur_status.worklog_id', '=', 'worklogs.id')
            ->leftJoin(DB::raw('users as workers'), 'oa_cur_status.user_id', '=', 'workers.id')
            ->with(['user', 'transaction', 'reportType'])
            ->where('orders.completed', 1)
            ->whereNotIn('orders.status', [Order::STATUS_ARCHIVED, Order::STATUS_CANCELED, Order::STATUS_ACCEPTED])
            ->orderBy('id', 'desc');
    }
}