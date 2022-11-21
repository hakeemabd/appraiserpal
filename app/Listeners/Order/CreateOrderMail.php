<?php

namespace App\Listeners\Order;

use App\Events\Order\CreateOrder;
use App\Jobs\MailJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\Order;
use App\User;
use Illuminate\Support\Facades\DB;

class CreateOrderMail
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
    public function handle(CreateOrder $event)
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
        $emails[$n] = $customer->email;
        $this->dispatch(new MailJob('admin.emails.createOrder', ['customer' => $customer->first_name.' '.$customer->last_name, 'host' => env('CUSTOMER_HOST')], function ($m) use ($emails) {
            $m->to($emails)->subject('We have received your order!');
        }, ''));
    }
}