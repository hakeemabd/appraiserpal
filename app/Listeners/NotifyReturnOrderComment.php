<?php

namespace App\Listeners;

use App\Events\ReturnOrderComment;
use App\Jobs\MailJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\Comment;
use App\Models\Order;
use App\User;
use Illuminate\Support\Facades\DB;

class NotifyReturnOrderComment
{
    use DispatchesJobs;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserCreated $event
     *
     * @return void
     */
    public function handle(ReturnOrderComment $event)
    {

        $comment = Comment::find($event->comment);
        $order = Order::find($comment->order_id);
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
     
        $this->dispatch(new MailJob('admin.emails.returnOrderComment', ['host' => env('ADMIN_HOST')], function ($m) use ($emails, $order, $customer) {
            $m->to($emails)->subject('Customer '.$customer->first_name.' '.$customer->last_name.' has asked for revisions to #'.$order->id.' '.$order->title);
        }, ''));
    }
}
