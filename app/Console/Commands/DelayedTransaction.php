<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\User;

class DelayedTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:charge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delayed payments';

    private $transactionRepository;
    private $userRepository;

    /**
     * DelayedTransaction constructor.
     * @param TransactionRepository $transactionRepository
     * @param UserRepository $userRepository
     */
    public function __construct(TransactionRepository $transactionRepository, UserRepository $userRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        User::setStripeKey(env('STRIPE_API_SECRET'));
        $failedTransaction = [];
        $transactions = $this->transactionRepository->getDelayedAndOverdueTransaction();
        foreach ($transactions as $transaction) {
            try {
                if (Transaction::STATUS_OVERDUE == ($status = $transaction->charge())) {
                    $failedTransaction[] = $transaction->id;
                }

                $this->transactionRepository->update([
                    'status' => $status,
                ], $transaction->id);
            } catch (\Exception $e) {
                \Log::error("{$e->getMessage()} {$e->getFile()} on {$e->getLine()}");
            }
        }
        /**
         * todo send notification
         */
    }
}
