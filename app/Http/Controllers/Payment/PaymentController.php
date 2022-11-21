<?php

namespace App\Http\Controllers\Payment;

use App\Repositories\PaymentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\Payment;
use yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    private $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function getPayments($status)
    {
        if (!empty(\Sentinel::check()) && \Sentinel::check()->inRole('administrator')) {
            if ($status === 'complete') {
                $payments = $this->paymentRepository->getAllCompletePayments();
            } else {
                $payments = $this->paymentRepository->getAllDuePayments();
            }
            return Datatables::of($payments)
                ->addRowData('actions', function ($result) {
                    $actions = [
                        'Pay' => [
                            'link' => route('admin:payments.update', ['payment_id' => $result->id,
                                'status' => Payment::STATUS_PAID]),
                            'method' => 'PUT'
                        ],
                        'Cancel' => [
                            'link' => route('admin:payments.update', ['payment_id' => $result->id,
                                'status' => Payment::STATUS_CANCELED]),
                            'method' => 'PUT'
                        ]
                    ];
                    return $actions;
                })
                ->addColumn('col_title', function ($result) {
                    return $result->order->title;
                }, false)
                ->addColumn('col_worker', function ($result) {
                    return $result->user->first_name . ' ' . $result->user->last_name;
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

    public function updatePayment($payment_id, $status)
    {
        if (!empty(\Sentinel::check()) && \Sentinel::check()->inRole('administrator')) {
            $payment = Payment::find($payment_id);
            $payment->status = $status;
            $payment->save();

            return response()->json([
                'success' => true,
                'message' => 'Payment successfully paid',
            ], SymfonyResponse::HTTP_OK);
        }
        return redirect(url('/'));
    }
}