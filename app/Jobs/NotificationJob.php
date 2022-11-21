<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificationJob extends MailJob
{
    use InteractsWithQueue, SerializesModels;

    public function __construct($view, $data, $callback, $viewPath = null)
    {
        parent::__construct($view, $data, $callback, $viewPath = null);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();
        if($this->wasNotified) {
            $notification = Notification::find($this->data['notification_id']);
            $notification->sent_at = Carbon::now();
            $notification->save();
        }
    }
}
