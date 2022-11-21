<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\Worklog;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class TransactionRepository
 * @package App\Repositories
 */
class TransactionRepository extends BaseRepository
{

    public function model()
    {
        return Transaction::class;
    }

    /**
     * @param $model
     * @return mixed
     */

    public function getSubscriptionInfo($subscriptionDetails, $user, $user_repo)
    {
        if (!empty($subscriptionDetails)) {
            $total_transactions = $this->getTotalTransactions($user, $subscriptionDetails);
            $isExceeded = $this->checkExceeded($user->stripe_plan, $total_transactions);
            if ($isExceeded) {
                $inTrial = false;
                $isSubscribed = false;
            } else {
                $inTrial = $user_repo->inTrial($user);
                $isSubscribed = $user->subscribed();
            }
        } else {
            $inTrial = false;
            $isSubscribed = false;
        }
        return [$inTrial, $isSubscribed];
    }

    public function checkExceeded($plan, $totalTransactions)
    {
        if (($plan != \App\User::SUBSCRIPTION_PLAN_ID || $totalTransactions >= \App\User::TOTAL_ORDERS)) {
            return true;
        }
        return false;
    }

    public function getRemainingQuota($plan, $totalTransactions)
    {
        if ($plan == \App\User::SUBSCRIPTION_PLAN_ID) {
            return (is_numeric((\App\User::TOTAL_ORDERS - $totalTransactions))) ? (\App\User::TOTAL_ORDERS - $totalTransactions) : 0;
        }
        return 0;
    }

    public function getTotalTransactions($user, $subscriptionObject)
    {
        if (!empty($subscriptionObject) && is_array(json_decode($subscriptionObject, true))) {
            $subscriptionArray = json_decode($subscriptionObject, true);
//            echo date('Y-m-d H:i:s', $subscriptionArray['current_period_start']) . "--" . date('Y-m-d H:i:s', $subscriptionArray['current_period_end']);
//            die;
            if (isset($subscriptionArray['current_period_start']) && !empty($subscriptionArray['current_period_start']) && isset($subscriptionArray['current_period_end']) && !empty($subscriptionArray['current_period_end'])) {
                $transaction = $this->getAllPaidTransactionWithinDates(date('Y-m-d H:i:s', $subscriptionArray['current_period_start']), date('Y-m-d H:i:s', $subscriptionArray['current_period_end']), $user->id);
                $totalTransactions = $transaction->count();
            }
        }
        if (!isset($totalTransactions)) {
//            if ($user->stripe_plan == \App\User::SUBSCRIPTION_PLAN_ID) {
//
//            }
            $totalTransactions = \App\User::TOTAL_ORDERS;
        }
        return $totalTransactions;
    }

    public function setTimestampByStatus($model)
    {
        if ($model['status'] != Transaction::STATUS_NEW) {
            $statusesWithTimestamps = [
                Transaction::STATUS_PAID => 'paid_at',
                Transaction::STATUS_DELAYED => 'delayed_at',
                Transaction::STATUS_CANCELED => 'canceled_at',
                Transaction::STATUS_OVERDUE => 'overdue_at',
            ];
            if ($model['status'] == 'paid') {
                if (empty($statusesWithTimestamps[$model['status']]))
                    $model[$statusesWithTimestamps[$model['status']]] = (string)Carbon::now();
            } else {
                $model[$statusesWithTimestamps[$model['status']]] = (string)Carbon::now();
            }
        }

        return $model;

    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id)
    {
        $model = $this->find($id);
        $model = $model->fill($data);
        $model = $this->setTimestampByStatus($model);
        if ($model->save()) {
            return $model;
        }

        return false;
    }

    /**
     * @param Order $order
     * @param User $user
     * @param bool $status
     * @return mixed
     */
    public function fill(Order $order, User $user, $status = false)
    {
        $transactionData['status'] = ($status) ?: Transaction::STATUS_NEW;

        if ($user->hasFreeOrders()) {
            $transactionData['amount'] = 0;
            $transactionData['is_free'] = 1;
            $user->free_order_count = --$user->free_order_count;
        } else {
            $transactionData['amount'] = $order->price;
            //is_free has default value of 0
        }

        return $this->model->fill($transactionData);
    }

    public function getDelayedAndOverdueTransaction($format = 'Y-m-d')
    {
        return $this->model
            ->where('status', '=', Transaction::STATUS_OVERDUE)
            ->orWhere(function ($query) use ($format) {
                $query->where('status', '=', Transaction::STATUS_DELAYED)
                    ->where('delayed_until', '=', Carbon::now()->format($format));
            })->get();
    }

    public function getAllCompletePayments()
    {
        return $this->model->where('status', '=', Transaction::STATUS_PAID)->get();
    }

    public function getAllPaidTransactionWithinDates($from_date, $to_date, $userId)
    {
        return $this->model
            ->join('orders', 'orders.id', '=', 'transactions.order_id')
            ->where('transactions.status', '=', Transaction::STATUS_PAID)
            ->where('orders.user_id', '=', $userId)
            ->where('transactions.amount', '=', 0)
            ->whereBetween('transactions.paid_at', [$from_date, $to_date])->get();
    }

    public function getAllDuePayments()
    {
        return $this->model->where('status', '!=', Transaction::STATUS_PAID)->get();
    }
}