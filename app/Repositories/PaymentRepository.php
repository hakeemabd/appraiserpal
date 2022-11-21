<?php
namespace App\Repositories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Worklog;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class PaymentRepository
 * @package App\Repositories
 */
class PaymentRepository extends BaseRepository
{
    public function model()
    {
        return Payment::class;
    }

    public function getAllCompletePayments() 
    {
        return $this->model->where('status', '=', Payment::STATUS_PAID)->get();
    }

    public function getAllDuePayments() 
    {
        return $this->model->where('status', '=', Payment::STATUS_NEW)->get();
    }

    public function savePayments($orderId)
    {   
        $workers = DB::table('worklogs')
            ->select('worklogs.*', 'worker_groups.fee', 'worker_groups.second_fee')
            ->join('worker_groups', function ($join) {
                $join->on('worklogs.user_id', '=', 'worker_groups.user_id')
                     ->on('worklogs.group_id', '=', 'worker_groups.group_id');
            })
            ->where('worklogs.order_id', $orderId)
            ->where('worklogs.status', Order::WORK_STATUS_FINISHED)->get();
        
        $transactions = [];
        foreach ($workers as $worker) {
            $fee = 0;
            if (array_key_exists($worker->group_id, $transactions)) {
                $fee = $worker->second_fee;
            } else {
                $fee = $worker->fee;
            }

            $transactions[$worker->group_id][] = [
                'order_id' => $orderId,
                'amount' => $fee,
                'status' => Payment::STATUS_NEW,
                'user_id' => $worker->user_id
            ];
        };

        foreach ($transactions as $transaction) {
            foreach ($transaction as $tran) { 
                Payment::create($tran);
            }
        }
    }
}