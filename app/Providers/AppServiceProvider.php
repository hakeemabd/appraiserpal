<?php

namespace App\Providers;

use App\Components\AssignmentService;
use App\Components\InvitationService;
use App\Components\OrderProcessor;
use App\Repositories\OrderRepository;
use App\Repositories\WorkerGroupRepository;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('AssignmentService', function ($app) {
            return new AssignmentService($app->make(WorkerGroupRepository::class), $app->make(InvitationService::class));
        });
        $this->app->singleton('OrderProcessor', function ($app) {
            return new OrderProcessor(
                $app->make(AssignmentService::class),
                $app->make(OrderRepository::class),
                $app->make(InvitationService::class));
        });
//        $repositories = [
//            AttachmentRepository::class,
//            InvitationRepository::class,
//            LabelRepository::class,
//            OrderRepository::class,
//            TransactionRepository::class,
//            UserRepository::class,
//            WorkerGroupRepository::class
//        ];
//        foreach ($repositories as $repository) {
//            $this->app->singleton($repository, function($app) use ($repository) {
//                return $app->make($repository);
//            });
//        }
    }
}
