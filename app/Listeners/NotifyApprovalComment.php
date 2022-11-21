<?php

namespace App\Listeners;

use App\Events\ApprovalComment;
use App\Jobs\MailJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\Comment;
use App\Models\Order;
use App\User;
use Illuminate\Support\Facades\DB;

class NotifyApprovalComment
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
    public function handle(ApprovalComment $event)
    {

        $comment = Comment::pending()->find($event->comment);
        $order = Order::find($comment->order_id);
        $user = User::find($comment->user_id);
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
     
        $this->dispatch(new MailJob('admin.emails.commentApproval', ['host' => env('ADMIN_HOST')], function ($m) use ($emails, $user) {
            $m->to($emails)->subject('A comment by worker or customer '.$user->first_name.' '.$user->last_name.' needs approval!');
        }, ''));
    }
}
