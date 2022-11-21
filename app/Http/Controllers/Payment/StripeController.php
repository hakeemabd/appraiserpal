<?php

namespace App\Http\Controllers\Payment;

use App\Events\Order\Paid;
use App\Events\Order\PaymentFailed;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\HistoryLog;
use App\Repositories\OrderRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\HistoryLogRepository;
use App\Repositories\UserRepository;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class StripeController extends Controller
{
    private $userRepository;
    private $orderRepository;
    private $transactionRepository;

    public function __construct(UserRepository $userRepository, OrderRepository $orderRepository,
                                TransactionRepository $transactionRepository, HistoryLogRepository $historyLogRepository)
    {
        $this->userRepository = $userRepository;
        $this->orderRepository = $orderRepository;
        $this->transactionRepository = $transactionRepository;
        $this->middleware('user.order', ['only' => ['charge']]);
        $this->historyLogRepository = $historyLogRepository;
    }

    /**
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function charge(Request $request, Order $order)
    {
        DB::beginTransaction();
        try {
            User::setStripeKey(env('STRIPE_API_SECRET'));
            $saveData = [];

            $userId = $request->get('user_id');
            $this->userRepository->update($request->only([
                'first_name',
                'last_name',
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'zip',
            ]), $userId);
            $transaction = $order->transaction;
            $user = $this->userRepository->find($request->get('user_id'));
            $customerToken = $this->userRepository->linkToStripe($user, $request->get('token'));

            if ($request->get('pay_now') == '1') {
                if ($this->userRepository->charge($user, $transaction->amount, $customerToken)) {
                    $saveData['status'] = Transaction::STATUS_PAID;
                    //history log
                    $user = Sentinel::check();
                    $extra = ['user' => $user->id];
                    $this->historyLogRepository->saveLog($order->id, HistoryLog::PAYMENT, $extra);
                } else {
                    $saveData['status'] = Transaction::STATUS_OVERDUE;
                }
            } else {
                if ($user->hasDelayedPayment()) {
                    $saveData['status'] = Transaction::STATUS_DELAYED;
                    $saveData['delayed_until'] = (string)Carbon::now()->addDays($user['delayed_payment'])->format('Y-m-d');
                } else {
                    $saveData['status'] = Transaction::STATUS_OVERDUE;
                }
            }

            $updatedTransaction = $this->transactionRepository->update($saveData, $transaction->id);
            if ($updatedTransaction->status == Transaction::STATUS_OVERDUE) {
                event(new PaymentFailed($order->id));
            } else {
                $this->transactionRepository->update(['paid_by' => '1'], $transaction->id);
                event(new Paid($order->id));
            }

            DB::commit();

            return response()->json($updatedTransaction);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array(
                'errors' => array(
//                    'message' => array((env('APP_DEBUG')) ? $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine() : 'Something went wrong'),
                    'message' => array($e->getMessage()),
                ),
            ), SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
//            return response()->json([
//                'errors' => (env('APP_DEBUG')) ? $e->getMessage() . ' ' . $e->getFile(). ' ' . $e->getLine() : 'Something went wrong',
//            ], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
