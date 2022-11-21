<?php

namespace App\Http\Controllers\Payment;

use App\Components\Paypal;
use App\Events\Order\Paid;
use App\Events\Order\PaymentFailed;
use App\Exceptions\PaypalException;
use App\Exceptions\SaveException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\HistoryLog;
use App\Repositories\TransactionRepository;
use App\Repositories\HistoryLogRepository;
use Illuminate\Http\Request;
use PayPal\Exception\PayPalConnectionException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use yajra\Datatables\Datatables;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class PaypalController extends Controller
{
    private $transactionRepository;
    private $paypal;

    public function __construct(TransactionRepository $transactionRepository, Paypal $paypal, HistoryLogRepository $historyLogRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->paypal = $paypal;
        $this->historyLogRepository = $historyLogRepository;

    }

    /**
     * @param Order $order
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws PaypalException
     * @throws SaveException
     */
    public function index(Order $order)
    {
        $orderTransaction = $order->transaction;
        $total = $orderTransaction->amount;
        $returnUrl = "http://{$_SERVER['HTTP_HOST']}/api/paypal/status/{$orderTransaction->id}";
        try {
            $payment = $this->paypal->createPayment($total, $returnUrl);
        } catch (PayPalConnectionException $ex) {
            return response()->json([
                'message' => 'Paypal error',
            ], SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        if (!$this->transactionRepository->update([
            'payment_id' => $payment->getId(),
            'amount' => $total,
        ], $orderTransaction->id)
        ) {
            throw new SaveException("Transaction not saved");
        }

        return response()->json(['url' => $this->paypal->getApprovalUrl($payment)]);
    }

    public function getPaymentStatus(Request $request, Transaction $transaction)
    {
        $payment_id = $transaction->payment_id;

        $paymentStatus = $this->paypal->executePayment($payment_id, $request->get('PayerID'));

        $transaction = $this->transactionRepository->update(['status' => $paymentStatus], $transaction->id);

        if ($transaction->status == Transaction::STATUS_OVERDUE) {
            event(new PaymentFailed($transaction->order_id));
        } else {
            $this->transactionRepository->update(['paid_by' => '1'], $transaction->id);
            //history log
            $user = Sentinel::check();
            $extra = ['user' => $user->id];
            $this->historyLogRepository->saveLog($transaction->order_id, HistoryLog::PAYMENT, $extra);
            event(new Paid($transaction->order_id));
        }

        return redirect()->route('customer:dashboard');
    }

    public function getPayments($status)
    {
        if (!empty(Sentinel::check()) && Sentinel::check()->inRole('administrator')) {

            if ($status === 'complete') {
                $payments = $this->transactionRepository->getAllCompletePayments();
            } else {
                $payments = $this->transactionRepository->getAllDuePayments();
            }

            return Datatables::of($payments)
                ->addRowData('actions', function ($result) {
                    return [];
                })
                ->addColumn('col_title', function ($result) {
                    return $result->order->title;
                }, false)
                ->addColumn('col_customer', function ($result) {
                    return $result->order->user->first_name . ' ' . $result->order->user->last_name;
                }, false)
                ->addColumn('col_date', function ($result) {
                    return date_format($result->created_at, 'jS F Y\, g:ia');
                    //return $result->created_at->diffForHumans();
                }, false)
                ->addColumn('col_cost', function ($result) {
                    return $result->amount;
                }, false)
                ->make(true);

        }
        return redirect(url('/'));
    }
}
