<?php

namespace App\Listeners\Order;

use App\Events\Order\InvitationError;
use App\Exceptions\NoWorkersInvitationException;
use App\Jobs\MailJob;
use App\Jobs\SmsJob;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

class NotifyAboutInvitationError implements ShouldQueue
{
    use DispatchesJobs;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Create the event listener.
     *
     * @return void
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
    public function handle(InvitationError $event)
    {
        if ($event->exception instanceof NoWorkersInvitationException) {
            $mailTemplate = 'emails.invitation.noUsers';
            $smsTemplate = 'sms.invitation.noUsers';
        } else {
            $mailTemplate = 'emails.invitation.cantAutoinvite';
            $smsTemplate = 'sms.invitation.cantAutoinvite';
        }
        foreach ($this->userRepository->admins()->get() as $admin) {
            $this->dispatch(new MailJob($mailTemplate, [
                'order' => $event->order,
                'groupId' => $event->exception->getGroupId(),
            ], function ($m) use ($admin, $event) {
                $m->to($admin->email, $admin->fullName)->subject("Order #{$event->order->id} invitation error");
            }));
            $this->dispatch(new SmsJob($smsTemplate, [
                'order' => $event->order,
            ], $admin->mobile_phone));
        }
    }
}
