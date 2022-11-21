<?php

namespace App\Jobs\Notification;

use App\Facades\Content;
use App\Jobs\Job;
use App\Models\Invitation;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\View\FileViewFinder;

class OrderInvitationJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $invitation;

    /**
     * Create a new job instance.
     *
     * @param Invitation $invitation
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $worker = $this->invitation->worker;
        //send email
        $finder = new FileViewFinder(app()['files'], array(realpath(base_path('resources/views'))));
        View::setFinder($finder);
        Mail::send('worker.emails.order.invitation', [
            'invitation' => $this->invitation,
            'host' => env('ADMIN_HOST')
        ], function ($message) use ($worker) { 
            $message
                ->from(Content::emailFromAddress('order.invitation'), Content::emailFromName('order.invitation'))
                ->to($worker->email, $worker->fullName)
                ->subject(Content::emailSubject('order.invitation'));
        });
        //send SMS
    }
}
