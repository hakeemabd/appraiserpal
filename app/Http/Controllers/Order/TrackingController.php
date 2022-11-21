<?php

namespace App\Http\Controllers\Order;

use App\Components\InvitationService;
use App\Components\OrderProcessor;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Group;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Worklog;
use App\Models\Comment;
use App\Models\HistoryLog;
use App\Repositories\AttachmentRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\WorklogRepository;
use App\Repositories\Criteria\AdminOrders;
use App\Repositories\Criteria\WorkerInvitations;
use App\Repositories\Criteria\WorkerOrders;
use App\Repositories\Criteria\WorkerLog;
use App\Repositories\InvitationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\WorkerGroupRepository;
use App\Repositories\CommentRepository;
use App\Repositories\HistoryLogRepository;
use App\Repositories\PaymentRepository;
use App\User;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use yajra\Datatables\Datatables;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use App\Events\Order\CompleteOrder;
use App\Events\Order\DeliverOrder;
use Illuminate\Support\Facades\DB;

class TrackingController extends Controller
{
    /**
     * @var AttachmentRepository
     */
    protected $attachmentRepository;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var InvitationRepository
     */
    protected $invitationRepository;

    protected $invitationService;

    protected $orderProcessor;

    protected $transactionRepository;

    public function __construct(OrderRepository $repository,
                                AttachmentRepository $attachmentRepository,
                                InvitationRepository $invitationRepository,
                                InvitationService $invitationService,
                                WorkerGroupRepository $workerGroupRepository,
                                WorklogRepository $worklogRepository,
                                OrderProcessor $orderProcessor,
                                CommentRepository $commentRepository,
                                HistoryLogRepository $historyLogRepository,
                                PaymentRepository $paymentRepository,
                                TransactionRepository $transactionRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
        $this->orderRepository = $repository;
        $this->invitationRepository = $invitationRepository;
        $this->invitationService = $invitationService;
        $this->workergroupRepository = $workerGroupRepository;
        $this->orderProcessor = $orderProcessor;
        $this->worklogRepository = $worklogRepository;
        $this->commentRepository = $commentRepository;
        $this->historyLogRepository = $historyLogRepository;
        $this->paymentRepository = $paymentRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Sentinel::check()->inRole('administrator') || Sentinel::check()->inRole('sub-admin')) {
            $newComments = $this->commentRepository->getCountPendingComments();
            $response = new \Illuminate\Http\Response(view('order.index'));
            return $response->withCookie(cookie('comments', $newComments));
        } else {
            return view('order.index');
        }
    }

    public function getOrders()
    {
        $orders = $this->orderRepository->getByCriteria(new AdminOrders([
            'order_id' => null,
            'title' => null,
            'effective_date' => null,
            'customer_id' => null,
            'status' => null,
        ]))->all();
        return Datatables::of($orders)
            ->addColumn('transaction_status', [$this, 'renderTransaction'])
            ->addColumn('rendered_status', [$this, 'renderStatus'])
            ->addColumn('worker_name', function ($model) {
                if ($model->worker_email) {
                    return User::formatFullName($model->worker_email, $model->worker_first_name, $model->worker_last_name);
                }
                return '';
            })
            ->addColumn('due_at', function ($row) {
                if ($row->group_status == Order::WORK_STATUS_WORKING) {
                    $dt = Carbon::parse($row->due_at);
                    if ($dt->isFuture()) {
                        return Carbon::parse($row->due_at)->diffInMinutes(null, false);
                    } else {
                        return 0;
                    }
                }
                return '';
            })
            ->setRowClass(function ($row) {
                $classNames = [$row->status, $row->transaction->status, $row->group_status];
                if ($row->group_status == Order::WORK_STATUS_WORKING) {
                    if (Carbon::parse($row->due_at)->isPast()) {
                        $classNames[] = 'late';
                    }
                }
                return implode(' ', $classNames);
            })
            ->addRowData('actions', function ($model) {
                $actions = [
                    'view' => [
                        'link' => route('admin:order.show', ['order' => $model]),
                        'ajax' => false,
                    ],
                    'assignments' => [
                        'link' => route('admin:order.assignments.index', ['order' => $model]),
                        'ajax' => false,
                    ],
                    'cancel' => [
                        'link' => route('admin:order.cancel', ['order' => $model]),
                        'confirm' => true,
                        'confirm-header' => 'Are you sure you want to cancel the order?',
                        'confirm-message' => 'This will unassign all workers and prevent user from ever getting this done.
                                          This cannot be undone!!!'
                    ]
                ];
                if (!$model->isPaid()) {
                    unset($actions['assignments']);
                }
                if ($model->transaction->getOriginal('status') == Transaction::STATUS_CANCELED) {
                    unset($actions['cancel']);
                    unset($actions['assignments']);
                }
                return $actions;
            })
            ->make(true);
    }

    public function getWorkerOrders()
    {
        $orders = $this->orderRepository->getByCriteria(new WorkerOrders(Sentinel::check()->id))->all();
        return Datatables::of($orders)
            ->addColumn('status', '')
            ->addColumn('rendered_status', [$this, 'renderStatus'])
            ->addColumn('time_left', function ($row) {
                $orderId = $row->id;
                $userId = Sentinel::check()->id;
                try {
                    $groupId = $this->orderProcessor->getGroupByAssignmentsStatus($userId, $orderId, Order::WORK_STATUS_WORKING);

                    $model = $this->worklogRepository->model();
                    $worklog = $model::where('user_id', '=', $userId)->where('group_id', '=', $groupId)->where('order_id', '=', $orderId)->where('status', '=', Order::WORK_STATUS_WORKING)->orderBy('id', 'desc')->first();

                    $started_at = null;

                    if (!is_null($worklog)) {
                        $started_at = $worklog->started_at;
                    }

                    $timeLeft = $this->workergroupRepository->getTimeLeft($orderId, $userId, $groupId, $started_at);
                } catch (Exception $e) {
                    return response()->json([
                        'status' => 'error'
                    ], 422);
                }

                return $timeLeft;
            })
            ->editColumn('due_at', function ($row) {
                return Carbon::parse($row->due_at)->diffForHumans();
            })
            ->addRowData('actions', function ($row) {
                $actions = [
                    'view' => [
                        'link' => route('worker:order.show', ['order' => $row]),
                        'ajax' => false,
                    ],
                ];
                return $actions;
            })
            ->make(true);
    }

    public function renderStatus($row)
    {
        return Order::formatStatus([
            'status' => $row->status,
            'group_status' => $row->group_status,
            'group_name' => $row->group_name,
        ]);
    }

    public function renderTransaction($model)
    {
        if (!$model->transaction) {
            return '';
        }
        if (!empty(Sentinel::check()) && Sentinel::check()->inRole('administrator')) {
            $tpl = "<span>$[amount]</span><br /><span class='[status]'>[Status]</span>";
            $search = ['[amount]', '[status]', '[Status]'];
            $replace = [
                money_format('%i', $model->transaction->amount),
                $model->transaction->status,
                ucfirst($model->transaction->status),
            ];
        } else {
            $tpl = "<span class='[status]'>[Status]</span>";
            $search = ['[status]', '[Status]'];
            $replace = [
                $model->transaction->status,
                ucfirst($model->transaction->status),
            ];
        }

        return str_replace($search, $replace, $tpl);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param Order $order
     *
     * @return \Illuminate\Http\Response
     * @internal param int $id
     *
     */
    public function show(Order $order)
    {
        $attachments = $this->attachmentRepository->getFiles($order->id);

        $uploadConfig = $this->attachmentRepository->getUploadConfig();

        $user = Sentinel::check();

        $groupId = $this->orderProcessor->getGroupByAssignmentsStatus($user->getId(), $order->id, Order::WORK_STATUS_WORKING);

        $orderStatus = $order->status;
        $theLast = $this->orderProcessor->checkTheLastGroup($groupId);
        $reviewerData = $this->orderProcessor->getListOfReworkingGroups();

        $btnType = false;
        if ($groupId != null) {
            $btnType = 'finish';
        }
        if ($user->inRole('administrator') || $user->inRole('sub-admin') || $theLast || $orderStatus === Order::STATUS_REWORKING) {
            $btnType = 'complete';
        }

        $markComplete = false;
        if ($user->inRole('administrator') || $user->inRole('sub-admin')) {
            $workingAssings = DB::table('order_assignments')->where('status', '!=', Order::WORK_STATUS_FINISHED)->where('order_id', $order->id)->get();

            if (sizeof($workingAssings) === 0) {
                $markComplete = true;
            }
        }

        if ($user->inRole('administrator') || $user->inRole('sub-admin')) {
            $reviewerData = $reviewerData->pluck('name', 'id');
        } else {
            $reviewerData = $reviewerData->pluck('name', 'id');
            foreach ($reviewerData as $key => $name) {
                if ($key === $groupId) {
                    unset($reviewerData[$key]);
                };
            };
        };
        $model = $this->worklogRepository->model();
        $worklog = $model::where('user_id', '=', $user->id)->where('group_id', '=', $groupId)->where('order_id', '=', $order->id)->where('status', '=', Order::WORK_STATUS_WORKING)->orderBy('id', 'desc')->first();
        $started_at = null;
        if (!is_null($worklog)) {
            $started_at = $worklog->started_at;
        }
        $timeLeft = $this->workergroupRepository->getTimeLeft($order->id, $user->id, $groupId, $started_at);

        Session::put('order_for_attachment_upload', $order->id);

        if (Sentinel::check()->inRole('administrator') || Sentinel::check()->inRole('sub-admin')) {
            $newComments = $this->commentRepository->getCountPendingComments();
            $response = new \Illuminate\Http\Response(view('order.show', [
                'attachments' => $attachments,
                'order' => $order,
                'configType' => 'any',
                'uploadConfig' => $uploadConfig,
                'user' => $order->user,
                'reviewerData' => $reviewerData,
                'btnType' => $btnType,
                'groupId' => $groupId,
                'markComplete' => $markComplete
            ]));
            return $response->withCookie(cookie('comments', $newComments));
        } else {
            return view('order.show', [
                'attachments' => $attachments,
                'order' => $order,
                'configType' => 'any',
                'uploadConfig' => $uploadConfig,
                'user' => $order->user,
                'reviewerData' => $reviewerData,
                'btnType' => $btnType,
                'groupId' => $groupId,
                'timeLeft' => $timeLeft,
                'markComplete' => $markComplete
            ]);
        }
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
        //
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

    public function cancel($order)
    {
        $transaction = $order->transaction;
        $saveData['status'] = Transaction::STATUS_CANCELED;
        $updatedTransaction = $this->transactionRepository->update($saveData, $transaction->id);
        return response()->json($updatedTransaction);
    }

    /**
     * Mark as completed specifified order
     *
     * @param  int $orderId
     *
     * @return \Illuminate\Http\Response
     */
    public function completeOrder($orderId)
    {
        $order = Order::find($orderId);

        $this->orderRepository->complete($order, true);
        //history log
        $user = Sentinel::check();
        $extra = ['user' => $user->id];
        $this->historyLogRepository->saveLog($orderId, HistoryLog::ADMIN_MARK_COMPLETED, $extra);
        event(new CompleteOrder($orderId, $user->id));
        $this->paymentRepository->savePayments($orderId);
        return redirect('/order/' . $orderId);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function orderHistory(Order $order)
    {
        return view('order.history', compact('order'));
    }

    public function history(Order $order)
    {
        $logs = $this->historyLogRepository->getByOrder($order->id);

        return Datatables::of($logs)
            ->addRowData('actions', function ($result) {
                return [];
            })
            ->addColumn('col_log_created_at', function ($result) {
                $current = Carbon::now();
                return date_format($result->created_at, 'jS F Y\, g:ia');
                //return  $result->created_at->diffForHumans();   
            }, false)
            ->addColumn('col_log_description', function ($result) {
                return $result->description;
            }, false)
            ->make(true);
    }

    public function deliver($orderId)
    {
        $order = Order::find($orderId);

        $this->orderRepository->deliver($order);
        //history log
        $user = Sentinel::check();
        $extra = ['user' => $user->id];
        $this->historyLogRepository->saveLog($orderId, HistoryLog::CUSTOMER_ACEPT_ORDER, $extra);
        event(new DeliverOrder($orderId));
        return redirect('/dashboard');
    }
}
