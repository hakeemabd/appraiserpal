<?php

namespace App\Listeners\Order;

use App\Events\Order\WorkerDeadline;
use App\Jobs\MailJob;
use App\Jobs\NotificationJob;
use App\Jobs\SmsJob;
use App\Models\Notification;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

class NotifyAboutWorkerDeadline
{
    use DispatchesJobs;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Create the event listener.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the event.
     *
     * @param  InvitationError $event
     *
     * @return void
     */
    public function handle(WorkerDeadline $event)
    {
        $mailTemplate = 'emails.invitation.workerDeadline';

        foreach ($this->userRepository->admins()->get() as $admin) {

            $notification = Notification::create([
                'worklog_id' => $event->worklog->id
            ]);
            
            $this->dispatch(
                new NotificationJob($mailTemplate, $event->worklog->toArray() + ['notification_id' => $notification->id], function ($m) use ($admin, $event) {
                    $m->to($admin->email, $admin->fullName)->subject("Order #{$event->worklog->order_id} deadline of #{$event->worklog->user_id}");
                })
            );
        }
    }
}
