<?php

namespace App\Console\Commands;

use App\Components\OrderProcessor;
use Illuminate\Console\Command;

class WorkerDeadline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worker:deadline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify the admin when Worker exceeds given time limit';

    protected $orderProcessor;

    /**
     * Create a new command instance.
     *
     * @param OrderProcessor $orderProcessor
     */
    public function __construct(OrderProcessor $orderProcessor)
    {
        $this->orderProcessor = $orderProcessor;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->orderProcessor->notifyDeadline();
    }
}
