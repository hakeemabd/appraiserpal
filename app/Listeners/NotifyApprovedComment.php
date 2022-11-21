<?php

namespace App\Listeners;

use App\Events\ApprovedComment;
use App\Jobs\MailJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\Comment;
use App\Models\Order;
use App\User;
use Illuminate\Support\Facades\DB;

class NotifyApprovedComment
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
    public function handle(ApprovedComment $event)
    {

        $comment = Comment::find($event->comment);
        $order = Order::find($comment->order_id);
        $user = User::find($comment->user_id);

        $data = [
            'content' => $comment->content,
            'adminHost' => env('ADMIN_HOST'),
            'workerHost' => env('WORKER_HOST'),
            'custmerHost' => env('CUSTOMER_HOST'),
            'orderTitle' => $order->title
        ];

        $workers = DB::table('order_assignments')
            ->join('users', 'order_assignments.user_id', '=', 'users.id')
            ->where('order_assignments.order_id', $order->id)
            ->where('order_assignments.status', '!=', Order::WORK_STATUS_ON_HOLD)->get();

        $admins = DB::table('users')
            ->join('role_users', 'role_users.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->where('roles.id', 1)
            ->where('users.email_notification', 1)->get();

        $customerEmail = null;
        if (!$user->inRole('customer') && $comment->namespace !== Comment::PRIVATE_CHANNEL) {
            $customerEmail = $order->user->email;

            $this->dispatch(new MailJob('customer.emails.commentApproved', ['data' => $data], function ($m) use ($customerEmail, $order) {
                $m->to($customerEmail)->subject('A new message regarding ' . $order->title);
            }, ''));
        }

        $emails = [];
        $n = 0;
        foreach ($workers as $worker) {
            if ($worker->id !== $user->id) {
                $emails[$n] = $worker->email;
                $n++;
            }
        }

        if (sizeof($emails) > 0) {
            $this->dispatch(new MailJob('worker.emails.commentApproved', ['data' => $data], function ($m) use ($emails, $order) {
                $m->from(env('WORKER_MAIL_FROM_ADDRESS'), env('WORKER_MAIL_FROM_NAME'));
                $m->to($emails)->subject('A new message regarding ' . $order->title);
            }, ''));
        }

        $emails = [];
        $n = 0;
        foreach ($admins as $admin) {
            $emails[$n] = $admin->email;
            $n++;
        }
        $description = "Customer or Worker Commented";
        $data_array['emails'] = $emails;
        $data_array['subject'] = (isset($order->title) && !empty($order->title)) ? $order->title . " - " . $description : $description;
        $body = 'Customer name : ' . $order->user->first_name . " " . $order->user->last_name . "<br>" . 'Comment : ' . $comment->content . "<br>" . $description;
        $this->dispatch(new MailJob('admin.emails.tasks', ['task' => $body], function ($m) use ($data_array) {
            $m->to($data_array['emails'])->subject($data_array['subject']);
        }, ''));
    }
}
