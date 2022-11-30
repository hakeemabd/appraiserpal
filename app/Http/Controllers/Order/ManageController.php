<?php

namespace App\Http\Controllers\Order;

use App\Helpers\OrderViewHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Order;
use App\Repositories\AttachmentRepository;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use App\Repositories\CommentRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use yajra\Datatables\Datatables;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\HistoryLog;
use App\Events\Order\Paid;
use App\Events\Order\PaymentFailed;
use App\Repositories\TransactionRepository;
use App\Repositories\HistoryLogRepository;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ManageController extends Controller
{
    /**
     * @var AttachmentRepository
     */
    protected $attachmentRepository;
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    public function __construct(AttachmentRepository $attachmentRepository, OrderRepository $orep, UserRepository $userRepository, CommentRepository $commentRepository, TransactionRepository $transactionRepository, HistoryLogRepository $historyLogRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
        $this->orderRepository = $orep;
        $this->userRepository = $userRepository;
        $this->commentRepository = $commentRepository;
        $this->transactionRepository = $transactionRepository;
        $this->historyLogRepository = $historyLogRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        print_r('fd');
        $uploadConfig = $this->attachmentRepository->getUploadConfig();
        $configType = 'any';
        $user = $this->userRepository->find(Sentinel::getUser()->id);
        $trial = $this->userRepository->inTrial($user);
        $subscriptionDetails = $this->userRepository->getStripeSubscriptionDetails($user->stripe_subscription, env('STRIPE_API_SECRET'));
        $remainingOrders = 0;
        if (!empty($subscriptionDetails)) {
            $total_transactions = $this->transactionRepository->getTotalTransactions($user, $subscriptionDetails);
            $remainingQuota = $this->transactionRepository->getRemainingQuota($user->stripe_plan, $total_transactions);
            if (empty($remainingQuota)) {
                if ($trial)
                    $remainingOrders = 'User not subscribed';
            } else {
                $remainingOrders = $remainingQuota;
            }
        }


        //        $subscriptionObject = $this->userRepository->getStripeSubscriptionDetails($user->stripe_subscription, env('STRIPE_API_SECRET'));
        //
        //        if($user->onTrial()){
        //
        //        }
        //
        //        $totalTransactions = $this->transactionRepository->getTotalTransactions($user, $subscriptionObject);
        //        $quotaExceeded = $this->transactionRepository->checkExceeded($user->stripe_plan, $totalTransactions);
        //        $remainingQuota = $this->transactionRepository->getRemainingQuota($user->stripe_plan, $totalTransactions);

        return view(
            'order.dashboard',
            compact(
                'uploadConfig',
                'configType',
                'user',
                'trial',
                'remainingOrders'
            )
        );
        //        ,
        //        'totalTransactions',
        //                'quotaExceeded',
        //                'remainingQuota',
        //                'user'
    }


    public function confirmOrder(Request $request)
    {
        DB::beginTransaction();
        try {
            $orderId = $request->orderId;
            $orderData = $this->orderRepository->getModel($orderId)->toArray();
            if (isset($orderData['order_transaction']['id']) && !empty($orderData['order_transaction']['id'])) {
                $user = $this->userRepository->find(Sentinel::getUser()->id);
                if (!$user->subscribed() || (empty($user->stripe_plan) && empty($user->stripe_subscription))) {
                    DB::rollback();
                    return response()->json(array(
                        'errors' => array(
                            'message' => array('Your are not subscribed or expired'),
                        ),
                    ), SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
                }
                $subscriptionObject = $this->userRepository->getStripeSubscriptionDetails($user->stripe_subscription, env('STRIPE_API_SECRET'));
                if (!empty($subscriptionObject)) {
                    $total_transactions = $this->transactionRepository->getTotalTransactions($user, $subscriptionObject);
                    $isExceeded = $this->transactionRepository->checkExceeded($user->stripe_plan, $total_transactions);
                    if ($isExceeded) {
                        DB::rollback();
                        return response()->json(array(
                            'errors' => array(
                                'message' => array('Your Quota Exceeded'),
                            ),
                        ), SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
                    }
                } else {
                    DB::rollback();
                    return response()->json(array(
                        'errors' => array(
                            'message' => array('something went wrong on subscription'),
                        ),
                    ), SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
                }
                $saveData['status'] = Transaction::STATUS_PAID;
                $saveData['paid_at'] = (string)Carbon::now();
                $user = Sentinel::check();
                $extra = ['user' => $user->id];
                $this->historyLogRepository->saveLog($orderId, HistoryLog::PAYMENT, $extra);
                $updatedTransaction = $this->transactionRepository->update($saveData, $orderData['order_transaction']['id']);
                if ($updatedTransaction->status == Transaction::STATUS_OVERDUE) {
                    event(new PaymentFailed($orderId));
                } else {
                    event(new Paid($orderId));
                }
                DB::commit();
                return response()->json($updatedTransaction);
            } else {
                DB::rollback();
                return response()->json(array(
                    'errors' => array(
                        'message' => array('Order transaction not found'),
                    ),
                ), SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(array(
                'errors' => array(
                    'message' => array($e->getMessage()),
                ),
            ), SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
<<<<<<< HEAD
public function bidmodal($id){
dd($id);

    $values='test';
    return;
}
=======

>>>>>>> d4f8875e56bf3704e2a7e9308344340c4175bdd9
    public function getOrdersData()
    {
        $orders = $this->orderRepository->getCustomerOrders();

        return Datatables::of($orders)
            ->editColumn('customerStatus', function ($model) {
                return '<span class="' . OrderViewHelper::getStatusWrapCls($model->status) . '">' . $model->customerStatus . '</span>';
            })
            ->addColumn('paid', function ($model) {
                $ret = '';
                if ($model->transaction) {
                    // dd($model->transaction);
                    $ret="<span>$00</span>
                        <span> $00 </span>";
                //     $ret = "<span>$" . money_format('%i', $model->transaction->amount) . "</span>
                //    <span>" . ucfirst($model->transaction->status) . "</span>";
                   /* newCommentabd */
                }
                return $ret;
            })

            ->addColumn('action', function ($model) {
                $actions = [];
                $icons = [
                    'view' => 'eye',
                    'edit' => 'pencil',
                    'comment' => 'comments',
                    'download' => 'download',
                    'cancel' => 'times',
                    'leaveFeedback' => 'heart'

                ];
                foreach (['view', 'edit', 'comment', 'cancel', 'download', 'leaveFeedback'] as $action) {
                    $method = 'can' . ucfirst($action);
                    if (!method_exists($model, $method) || $model->{$method}()) {
                        $actions[] = '<i data-role="action" data-action="' . $action . '" class="fa fa-' . $icons[$action] . '"></i>';
                    }
                }

                if ($model->is_completed && $model->status !== Order::STATUS_DELIVERED) {
                    $actions[] = '<i data-toggle="modal" data-id="' . $model->id . '" data-title="' . $model->title . '" data-target="#modal-comment-customer"  class="fa fa-reply"></i>';
                    $actions[] = '<i><a class="fa fa-check-circle-o" onclick="deliver(' . $model->id . ')"></a></i>';
                }

                return join(' ', $actions);
            })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $uploadConfig = $this->attachmentRepository->getUploadConfig();
        return view('order.create', compact('uploadConfig'));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $uploadConfig = $this->attachmentRepository->getUploadConfig();
        $attachments = $this->attachmentRepository->getFiles($order->id);

        $admins = $this->userRepository->admins()->get();
        $workers = $this->userRepository->workers()->get();

        $adminsAndWorkers = $admins->merge($workers);

        $finalFiles = $this->attachmentRepository->getFinalOrderFiles($order->id, true);

        $finalFiles = $finalFiles->filter(function ($item) use ($adminsAndWorkers) {
            return in_array($item->getUserid(), $adminsAndWorkers->pluck('id')->toArray());
        });

        $comments = $this->commentRepository->getCommentsOrder($order->id, 'public', Sentinel::check()->id);

        return view('order.view', [
            'attachments' => $attachments,
            'order' => $order,
            'finalFiles' => $finalFiles,
            'comments' => $comments,
            'currentUser' => Sentinel::check()->id,
            'uploadConfig' => $uploadConfig,
            'configType' => 'any'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $uploadConfig = $this->attachmentRepository->getUploadConfig();

        return view('order.edit', compact('uploadConfig'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
