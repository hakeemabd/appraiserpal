<?php

namespace App\Listeners\Order;

use App\Events\Order\CompleteOrder;
use App\Jobs\MailJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\Order;
use App\User;
use Illuminate\Support\Facades\DB;

class CompleteOrderMail
{
    use DispatchesJobs;

    /**
     * Create the event listener.
     *
     * @param OrderProcessor $orderProcessor
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  StatusChanged $event
     *
     * @return void
     */
    public function handle(CompleteOrder $event)
    {
        $order = Order::find($event->order);
        $worker = User::find($event->worker);
        if ($order->is_completed) {
            $customer = User::find($order->user_id);
            $mail = $customer->email;
            $this->dispatch(new MailJob('customer.emails.completeOrder', ['host' => env('CUSTOMER_HOST')], function ($m) use ($mail, $order) {
                $m->to($mail)->subject('Your appraisal report for '.$order->title.' is complete!');
            }, ''));
        } else {
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

            $this->dispatch(new MailJob('admin.emails.completeOrder', ['host' => env('ADMIN_HOST')], function ($m) use ($emails, $order, $worker) {
                $m->to($emails)->subject('Order #'.$order->id.' '.$order->title.' has been marked as complete by '.$worker->first_name.' '.$worker->last_name.'.');
            }, ''));
        }
    }
}