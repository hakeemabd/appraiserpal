<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\User;
use Illuminate\Support\Facades\DB;
use App\Repositories\SettingRepository;
use App\Jobs\MailJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CheckIdleOrderTime extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:idletime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'checks how long an order has been sitting on the "Uninvited" status.
A notification must be sent to the administrator';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orders = DB::table('order_assignments')
            ->join('orders', 'orders.id', '=', 'order_assignments.order_id')
            ->whereNotIn('orders.status', [Order::STATUS_ARCHIVED, Order::STATUS_CANCELED, Order::STATUS_ACCEPTED])
            ->where('order_assignments.status', Order::WORK_STATUS_ASSIGNING)
            ->where('sent_idle', 0)->get();
       
        $settingRepository = app(SettingRepository::class);
        $model = $settingRepository->model();
        $setting = $settingRepository->getByKey($model::IOT);

        $iot = $setting->value;
        $idleOrders = [];
        $idleOrderIds = [];
        $i = 0;

        foreach ($orders as $order) {
            $interval = intval(round(abs(strtotime("now") - strtotime($order->updated_at)) / 60,2));
            $time_left = $iot - $interval;

            if ($time_left <= 0 && !in_array($order->order_id, $idleOrderIds)) {
                echo 'the order with id: '.$order->order_id. ' exceeded "idle order time"' ."\n";
                $idleOrders[$i]['id'] = $order->order_id;
                $idleOrders[$i]['title'] = $order->title;
                $idleOrders[$i]['group'] = $order->group_id;
                $idleOrderIds[$i] = $order->order_id;
                $i++;
            }
        }

        if (sizeof($idleOrders) > 0) {

            echo 'Cases found' ."\n";

            $admins = DB::table('users')
            ->join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->where('roles.id', 1)
            ->where('users.email_notification', 1)->get();

            $emails = [];
            $n = 0;
            foreach ($admins as $admin) {
               $emails[$n] = $admin->email;
                $n++;
            }
   
            echo 'Mail sending...' ."\n";
            try {
                foreach ($idleOrders as $idleOrder) {
                    $worker = DB::table('invitations')
                        ->select('users.first_name', 'users.last_name')
                        ->join('users', 'invitations.user_id', '=', 'users.id')
                        ->where('invitations.order_id', $idleOrder['id'])
                        ->where('invitations.group_id', $idleOrder['group'])
                        ->whereNull('invitations.accepted_at')
                        ->first();

                    $this->dispatch(new MailJob('admin.emails.notifyIdle', ['worker' => $worker->first_name.' '.$worker->last_name, 'host' => env('ADMIN_HOST')], function ($m) use ($emails, $worker, $idleOrder) {
                        $m->to($emails)->subject($worker->first_name.' '.$worker->last_name.' has not accepted their invitation to #'.$idleOrder['id'].' '.$idleOrder['title'].', its needs to be reassigned');
                    }, ''));
                    echo 'Mail sent' ."\n";
                }

                //mark sent idle
                /*foreach ($idleOrderIds as $idleOrderId) {
                    $order = Order::find($idleOrderId);
                    $order->sent_idle = true;
                    $order->save();
                    echo 'Order with id: '.$idleOrderId.' has been update' ."\n";
                };*/
            } catch (\Exception $e) {
                echo 'Some error has occurred:'.$e->getMessage() ."\n";
            }
        } else {
            echo 'Nothing to report' ."\n";
        }
    }
}
