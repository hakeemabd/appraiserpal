<?php

namespace App\Console\Commands;

use App\Components\OrderProcessor;
use Illuminate\Console\Command;

class ProcessOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes orders';

    /**
     * @var OrderProcessor
     */
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
        $this->orderProcessor->processAll();
    }
}
