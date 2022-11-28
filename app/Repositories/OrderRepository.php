<?php

namespace App\Repositories;

use App\Events\Order\Created;
use App\Events\Order\CreateOrder;
use App\Helpers\GuessOrder;
use App\Models\Order;
use App\Models\Transaction;
use APP\Models\HistoryLog;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use App\Repositories\HistoryLogRepository;

class OrderRepository extends BaseRepository
{
    use GuessOrder;

    public function model()
    {
        return Order::class;
    }

    public function fillFieds($fillableDatas)
    {
        return $this->model->fill($fillableDatas);
    }

    public function getModel($order)
    {
        return $this->guessOrder($order);
    }

    public function getFillableFields()
    {
        return $this->model['fillable'];
    }

    public function incompleteOrderExists($id)
    {
        return $this->model->where('id', $id)->where('completed', 0)->exists();
    }

    public function getCustomerOrders($customerId = null)
    {
        if ($customerId === null) {
            $customerId = Sentinel::check()->id;
        }
        return $this->model
            ->with('reportType')
            ->where('user_id', $customerId)
            ->where('completed', 1);
    }

    public function update(array $data, $order)
    {
        $user = $order->user;
        $user->standard_instructions = isset($data['standard_instructions']) ? $data['standard_instructions'] : $user->standard_instructions;
        $user->software_id = isset($data['software_id']) ? $data['software_id'] : $user->software_id;

        $wasCompleted = $order->isCompleted();
        $order->fill($data);
        $order->update();
        if ($this->isCompleted($order)) {
            //history log
            $historyLogRepository = app(HistoryLogRepository::class);
            $user = Sentinel::check();
            $extra = ['user' => $user->id];

            if (!$order->hasTransaction()) {
                // $historyLogRepository->saveLog($order->id, HistoryLog::NEW_ORDER, $extra);
                // event(new CreateOrder($order->id));
                $transaction = app(TransactionRepository::class);
                $order->transaction()->save(
                    $transaction->fill($order, $user)
                );
                $order = $this->find($order->id); //@todo research
            }
            if (!$wasCompleted) { //fire this event only if order was not completed before and became completed now.
                event(new Created($order->id));
            }
        }

        $user->save();

        return $order;
    }

    public function isCompleted($order)
    {
        $order = $this->guessOrder($order);
        return $order->completed == '1';
    }

    public function getOrdersForProcessing()
    {
        return $this->model
            ->whereNotIn('status', [Order::STATUS_CANCELED, Order::STATUS_DELIVERED, Order::STATUS_ACCEPTED, Order::STATUS_ARCHIVED])
            ->where('completed', 1)
            ->get();
    }

    public function getDeadlineWork()
    {
        return DB::table('worklogs as w1')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('worklogs as w2')
                    ->whereRaw('w1.order_id = w2.order_id')
                    ->whereRaw('w1.user_id = w1.user_id')
                    ->where(function ($q) {
                        $q->where('w2.status', Order::WORK_STATUS_FINISHED)
                            ->orWhere('w2.status', Order::WORK_STATUS_OVERDUE);
                    });
            })
            ->where('w1.deadline', '<', date('Y-m-d H:m:s'))
            ->where('w1.status', Order::WORK_STATUS_WORKING)->get();
    }

    /**
     * Returns query builder that produces the query to get all info about users' assignments and invitations.
     *
     * @param $orderId
     * @param $groupId
     *
     * @return Builder
     */
    public function getWorkersWithAssignment($orderId, $groupId)
    {
        //type casting here because later we'll inject them into the SQL
        //Hack, but no better way found...
        $orderId = (int)$orderId;
        $groupId = (int)$groupId;
        $finishedStatuses = "'" . implode("','", [
                Order::STATUS_ARCHIVED,
                Order::STATUS_DELIVERED,
                Order::STATUS_ACCEPTED,
                Order::STATUS_CANCELED,
            ]) . "'";
        /*
         * This builds the following query:
SELECT users.id,
  users.email,
  IFNULL(order_assignments.status, 'not invited') status,
  IFNULL(worked_before.user_id, 0) worked_before,
  IFNULL(invitations.id, 0) invited
FROM users
INNER JOIN worker_groups on users.id = worker_groups.user_id AND worker_groups.group_id = 2
LEFT JOIN (SELECT oa.*, left_ord.feedbackRating
           from order_assignments oa
           INNER JOIN orders left_ord ON oa.order_id=left_ord.id AND left_ord.id <> 5 AND left_ord.status IN ('delivered', 'archived')
           INNER JOIN orders right_ord ON left_ord.user_id = right_ord.user_id AND right_ord.id = 5)
          worked_before ON users.id=worked_before.user_id
LEFT JOIN order_assignments ON users.id = order_assignments.user_id AND order_assignments.order_id=5 AND order_assignments.group_id =2
LEFT JOIN invitations ON users.id = invitations.user_id AND invitations.order_id=5 AND invitations.group_id =2
         * */
        return DB::table('users')
            ->select(DB::raw("DISTINCT
                users.*,
                worker_groups.*,
                order_assignments.status as assignment_status,
                worklogs.deadline as deadline,
                worked_before.user_id worked_before,
                worked_before.feedbackRating feedback_rating,
                invitations.code invited"))
            ->join('worker_groups', function ($join) use ($groupId) {
                $join->on('users.id', '=', 'worker_groups.user_id')
                    ->where('worker_groups.group_id', '=', $groupId);
            })
            ->leftJoin(DB::raw("(select oa.*, left_ord.feedbackRating
                        from order_assignments oa
                        INNER JOIN orders left_ord
                            ON oa.order_id=left_ord.id
                            AND left_ord.id <> $orderId
                            AND left_ord.status IN (" . $finishedStatuses . ")
                        inner join orders right_ord
                            ON left_ord.user_id=right_ord.user_id
                            AND right_ord.id = $orderId)
                        worked_before"),
                'users.id', '=', 'worked_before.user_id')
            ->leftJoin('order_assignments', function ($join) use ($orderId, $groupId) {
                $join->on('users.id', '=', 'order_assignments.user_id')
                    ->where('order_assignments.order_id', '=', $orderId)
                    ->where('order_assignments.group_id', '=', $groupId);
            })
            ->leftJoin('worklogs', 'order_assignments.worklog_id', '=', 'worklogs.id')
            ->leftJoin('invitations', function ($join) use ($orderId, $groupId) {
                $join->on('users.id', '=', 'invitations.user_id')
                    ->where('invitations.order_id', '=', $orderId)
                    ->where('invitations.group_id', '=', $groupId)
                    ->whereNull('invitations.rejected_at')
                    ->whereNull('invitations.deleted_at')
                    ->whereNull('invitations.accepted_at');
            });

    }

    public function complete(Order $order, $isCompleted)
    {
        $order->is_completed = $isCompleted;
        $order->save();
    }

    public function deliver(Order $order)
    {
        $order->status = Order::STATUS_DELIVERED;
        $order->save();
    }
}