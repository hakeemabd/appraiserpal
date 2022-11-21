<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Jobs\MailJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class WelcomeUser
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
    public function handle(UserCreated $event)
    {
        $host = null;
        if ($event->user->inRole('administrator') || $event->user->inRole('sub-admin')) {
            $host = env('ADMIN_HOST');
        };

        if ($event->user->inRole('worker')) {
            $host = env('WORKER_HOST');
        };

        $this->dispatch(new MailJob('emails.welcome', [
            'user' => $event->user,
            'activation' => $event->activation,
            'password' => $event->password,
            'host' => $host
        ], function ($m) use ($event) {
            if ($event->user->inRole('worker')) {
                $m->from(env('WORKER_MAIL_FROM_ADDRESS'), env('WORKER_MAIL_FROM_NAME'));
            };
            if ($event->user->inRole('worker')) {
                $m->to($event->user->email)->subject('Welcome to Appraisers Solutions!');
            } else {
                $m->to($event->user->email)->subject('Welcome to Appraiser Pal!');
            };


        }));
    }
}
