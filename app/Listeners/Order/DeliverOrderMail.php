<?php

namespace App\Listeners\Order;

use App\Events\Order\DeliverOrder;
use App\Jobs\MailJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\Order;
use App\User;
use Illuminate\Support\Facades\DB;

class DeliverOrderMail
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
    public function handle(DeliverOrder $event)
    {
        $order = Order::find($event->order);
        $customer = User::find($order->user_id);
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

        $this->dispatch(new MailJob('admin.emails.deliverOrder', ['host' => env('ADMIN_HOST')], function ($m) use ($emails, $order, $customer) {
            $m->to($emails)->subject($customer->first_name.' '.$customer->last_name.' has approved the completed appraisal report #'.$order->id.' '.$order->title);
        }, ''));
    }
}