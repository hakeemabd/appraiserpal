<?php

namespace App\Providers;

use App\Models\Attachment;
use App\Models\Label;
use App\Models\Order;
use App\Models\PromoCode;
use App\Models\ReportType;
use App\Models\Softwares;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\WorkerGroup;
use App\Models\Setting;
use App\Models\Comment;
use App\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $router->pattern('role', 'customer|worker|administrator|sub-admin');

        $router->model('group', WorkerGroup::class);
        $router->model('workerGroup', WorkerGroup::class);
        $router->model('user', User::class);
        $router->model('attachment', Attachment::class);
        $router->model('order', Order::class);
        $router->model('label', Label::class);
        $router->model('transaction', Transaction::class);
        $router->model('payment', Payment::class);
        $router->model('software', Softwares::class);
        $router->model('promoCodeById', PromoCode::class);
        $router->model('reportType', ReportType::class);
        $router->model('setting', Setting::class);
        $router->model('comment', Comment::class);
        $router->model('historyLog', HistoryLog::class);
        $router->bind('promoCode', function ($value) {
            return PromoCode::where('code', '=', $value)->firstOrFail();
        });
        parent::boot($router);

    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router $router
     *
     * @return void
     */
    public function map(Router $router)
    {
        $router->group(['namespace' => $this->namespace], function ($router) {
            require app_path('Http/routes.php');
        });
    }
}
