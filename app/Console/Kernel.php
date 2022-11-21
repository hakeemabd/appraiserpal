<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Psy\Command\Command;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\DelayedTransaction::class,
        Commands\ProcessOrders::class,
        Commands\WorkerDeadline::class,
        Commands\CheckIdleOrderTime::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('orders:process')->everyMinute();
        $schedule->command('worker:deadline')->everyMinute();
        $schedule->command('orders:idletime')->daily();
    }
}
