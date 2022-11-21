<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\UserCreated' => [
            'App\Listeners\WelcomeUser',
        ],
        //order events
        'App\Events\Order\Created' => [
            'App\Listeners\Order\Initialize',
        ],
        'App\Events\Order\Paid' => [
            'App\Listeners\Order\Initialize',
            'App\Listeners\Order\NotifyAboutPayment',
        ],
        'App\Events\Order\StatusChanged' => [
            'App\Listeners\Order\Process',
        ],
        'App\Events\Order\PaymentFailed' => [
            'App\Listeners\Order\NotifyAboutPayment',
        ],
        'App\Events\Order\InvitationError' => [
            'App\Listeners\Order\NotifyAboutInvitationError',
        ],
        'App\Events\Order\WorkerDeadline' => [
            'App\Listeners\Order\NotifyAboutWorkerDeadline'  
        ],
        'App\Events\Order\CompleteOrder' => [
            'App\Listeners\Order\CompleteOrderMail'  
        ],
        'App\Events\Order\CreateOrder' => [
            'App\Listeners\Order\CreateOrderMail'  
        ],
        'App\Events\ApprovalComment' => [
            'App\Listeners\NotifyApprovalComment'  
        ],
        'App\Events\ApprovedComment' => [
            'App\Listeners\NotifyApprovedComment'  
        ],
        'App\Events\Order\DeliverOrder' => [
            'App\Listeners\Order\DeliverOrderMail'  
        ],
        'App\Events\ReturnOrderComment' => [
            'App\Listeners\NotifyReturnOrderComment'  
        ]
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher $events
     *
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

    }
}
