<?php

namespace App\Repositories;

use App\Models\HistoryLog;
use App\Models\WorkerGroup;
use App\User;
use App\Jobs\MailJob;
use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Mail;

class HistoryLogRepository extends BaseRepository
{
    use DispatchesJobs;

    public function model()
    {
        return HistoryLog::class;
    }

    public function saveLog($orderId, $type, $extra = null)
    {
        $description = '';
        switch ($type) {
            case HistoryLog::NEW_ORDER:
                $description = 'New order is created by customer ' . $this->getUserName($extra['user']);
                break;
            case HistoryLog::PAYMENT:
                $description = 'Payment is made by customer ' . $this->getUserName($extra['user']);
                break;
            case HistoryLog::INVITED:
                $description = 'Worker ' . $this->getUserName($extra['user']) . ' gets invited as ' . $this->getGroupName($extra['group']);
                break;
            case HistoryLog::ACEPT_INVITED:
                $description = 'Worker ' . $this->getUserName($extra['user']) . ' accepts invitation as ' . $this->getGroupName($extra['group']);
                break;
            case HistoryLog::UPLOAD_FILE:
                $description = 'Worker ' . $this->getUserName($extra['user']) . ' upload document ' . $extra['file'];
                break;
            case HistoryLog::WORKER_FINISHED:
                $description = 'Worker ' . $this->getUserName($extra['user']) . ' marks the order as finish';
                break;
            case HistoryLog::WORKER_MARK_COMPLETED:
                $description = 'Worker ' . $this->getUserName($extra['user']) . ' marks the order as complete';
                break;
            case HistoryLog::ADMIN_MARK_COMPLETED:
                $description = 'Admin ' . $this->getUserName($extra['user']) . ' marks the order as complete';
                break;
            case HistoryLog::SEND_BACK:
                $description = $this->getUserRole($extra['user']) . ' ' . $this->getUserName($extra['user']) . ' sends back the Order';
                break;
            case HistoryLog::ADMIN_APPROVED_FILE:
                $description = 'Admin ' . $this->getUserName($extra['user']) . ' approves a deliverable ' . $extra['file'];
                break;
            case HistoryLog::ADMIN_DISAPPROVED_FILE:
                $description = 'Admin ' . $this->getUserName($extra['user']) . ' disapproves a deliverable ' . $extra['file'];
                break;
            case HistoryLog::CUSTOMER_ACEPT_ORDER:
                $description = 'Customer ' . $this->getUserName($extra['user']) . ' accepts the Order';
                break;
        }
        if (!empty($description)) {
            $admins = DB::table('users')
                ->join('role_users', 'role_users.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_users.role_id')
                ->where('roles.id', 1)
                ->where('users.email_notification', 1)->get();

            $orderDetails = DB::table('orders')
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->where('orders.id', $orderId)->get();
//            print_r($orderDetails);
//            die;
            if (isset($orderDetails[0]->id) && !empty($orderDetails[0]->id)) {
                $name = (!empty($orderDetails[0]->first_name) && !empty($orderDetails[0]->last_name)) ? $orderDetails[0]->first_name . " " . $orderDetails[0]->last_name : $orderDetails[0]->email;
                $orderTitle = $orderDetails[0]->title;
                $emails = [];
                $n = 0;
                foreach ($admins as $admin) {
                    $emails[$n] = $admin->email;
                    $n++;
                }
                $data_array['emails'] = $emails;
                $data_array['subject'] = (!empty($orderTitle)) ? $orderTitle . " - " . $description : $description;
                $body = 'Customer name : ' . $name . "<br>" . $description;
                $this->dispatch(new MailJob('admin.emails.tasks', ['task' => $body], function ($m) use ($data_array) {
                    $m->to($data_array['emails'])->subject($data_array['subject']);
                }, ''));
            }
        }
        $data = [
            'order_id' => $orderId,
            'description' => $description,
        ];
        return HistoryLog::create($data);
    }

    public function getUserName($userId)
    {
        $user = User::find($userId);
        return $user->first_name . ' ' . $user->last_name;
    }

    public function getUserRole($userId)
    {
        $user = User::find($userId);
        return $user->roles[0]->slug;
    }

    public function getGroupName($groupId)
    {
        return WorkerGroup::find($groupId)->name;
    }

    public function getByOrder($orderId)
    {
        return HistoryLog::where('order_id', $orderId)->get();
    }
}