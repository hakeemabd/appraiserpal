<?php

namespace App\Http\Controllers\Order;

use App\Components\OrderProcessor;
use App\Models\Worklog;
use App\Repositories\Criteria\WorkerLog;
use App\Repositories\WorklogRepository;
use Illuminate\Http\Request;
use App\Components\AssignmentService;
use App\Components\InvitationService;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Order;
use App\Models\Comment;
use App\Models\HistoryLog;
use App\Repositories\InvitationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\WorkerGroupRepository;
use App\Repositories\CommentRepository;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Session;
use Mockery\CountValidator\Exception;
use yajra\Datatables\Datatables;
use App\Events\ApprovedComment;
use App\Events\ReturnOrderComment;
use App\Repositories\HistoryLogRepository;

class AssignmentController extends Controller
{
    /**
     * @var InvitationService
     */
    protected $invitationService;
    /**
     * @var AssignmentService
     */
    protected $assignmentService;
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var WorkerGroupRepository
     */
    protected $workerGroupRepository;

    /**
     * @var InvitationRepository
     */
    protected $invitationRepository;

    /**
     * @var OrderProcessor
     */
    protected $orderProcessor;

    /**
     * @var WorklogRepository
     */
    protected $worklogRepository;

    /**
     * @var HistoryLogRepository
     */
    protected $historyLogRepository;

    /**
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     * AssignmentController constructor.
     *
     * @param InvitationService $is
     * @param AssignmentService $as
     * @param WorkerGroupRepository $workerGroupRepository
     * @param OrderRepository $orderRepository
     * @param InvitationRepository $invitationRepository
     * @param OrderProcessor $orderProcessor
     * @param WorklogRepository $worklogRepository
     * @param CommentRepository $commentRepository
     */
    public function __construct(InvitationService $is, AssignmentService $as,
                                WorkerGroupRepository $workerGroupRepository,
                                OrderRepository $orderRepository,
                                InvitationRepository $invitationRepository,
                                OrderProcessor $orderProcessor,
                                WorklogRepository $worklogRepository,
                                CommentRepository $commentRepository, 
                                HistoryLogRepository $historyLogRepository
    )
    {
        $this->invitationService = $is;
        $this->assignmentService = $as;
        $this->orderRepository = $orderRepository;
        $this->workerGroupRepository = $workerGroupRepository;
        $this->invitationRepository = $invitationRepository;
        $this->orderProcessor = $orderProcessor;
        $this->worklogRepository = $worklogRepository;
        $this->commentRepository = $commentRepository;
        $this->historyLogRepository = $historyLogRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Order $order
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Order $order)
    {
        $workerGroups = $this->workerGroupRepository->allSorted();
        return view('order.assignments.index', compact('order', 'workerGroups'));
    }

    public function getAssignments($orderId, $groupId)
    {
        return Datatables::of($this->orderRepository->getWorkersWithAssignment($orderId, $groupId))
            ->addRowData('actions', function ($row) use ($orderId, $groupId) {
                $actions = [];
                $params = [
                    'orderId' => $orderId,
                    'groupId' => $groupId,
                    'userId' => $row->id,
                ];
                if (empty($row->assignment_status) && empty($row->invited)) {
                    $actions['invite'] = [
                        'link' => route('admin:order.invite', $params),
                    ];
                } elseif (empty($row->assignment_status) && !empty($row->invited)) {
                    $actions['cancel'] = [
                        'link' => route('admin:order.invite.cancel', ['code' => $row->invited]),
                        'confirm' => true,
                        'confirm-header' => 'Cancel invitation?',
                        'confirm-body' => 'Are you sure you want to cancel an invitation?'
                    ];
                } elseif ($row->assignment_status != Order::WORK_STATUS_FINISHED) {
                    //per discussion with Lidiya, unassigning finished worker leads to the mess in the system since
                    //workers in the subsequent groups may be already working
                    $actions['unassign'] = [
                        'link' => route('admin:order.unassign', $params),
                        'confirm' => true,
                        'confirm-header' => 'Unassign user?',
                        'confirm-body' => 'Are you sure you want to unassign user from an order?
                        Unassigned users do not get payment regardless of the amount of work done!'
                    ];

                    $actions['add time'] = [
                        'handler' => 'popup',
                        'link' => '#add-additional-worker-time-modal',
                        'class' => 'waves-effect waves-light btn modal-trigger',
                        'confirm' => false,
                        'user-id' => $row->user_id,
                        'group-id' => $row->group_id
                    ];
                }
                return $actions;
            })
            ->make(true);
    }

    public function invite($orderId, $groupId, $userId)
    {
        $this->assignmentService->inviteManually($orderId, $groupId, [$userId]);
        return response()->json();
    }

    public function cancelInvite($code)
    {
        $this->invitationService->cancelInvitation($code);
        return response()->json();
    }

    public function unassign($orderId, $groupId, $userId)
    {
        $this->assignmentService->unassign($orderId, $groupId, $userId);
        return response()->json();
    }

    public function addTime(Request $request, $orderId)
    {
        try {
            $this->orderProcessor->extendDeadline(Order::find($orderId), $request->input('time'));
        }
        catch (Exception $e) {
            return response()->json([
                'status' => 'error'
            ], 422);
        }

        return response()->json();
    }

    public function addAdditionalWorkerTime(Request $request, $orderId)
    {
        try {
            $worklog = $this->worklogRepository->getByCriteria(new WorkerLog(
                $request->input('user_id'),
                $request->input('group_id'),
                $orderId,
                Order::WORK_STATUS_WORKING
            ))->first();
            $this->worklogRepository->extendDeadline($worklog, $request->input('time'));
        }
        catch (Exception $e) {
            return response()->json([
                'status' => 'error'
            ], 422);
        }
        return response()->json();
    }

    /**
     * Worker route.
     *
     * @param $code
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function acceptInvitation($code)
    {
        $invitation = $this->invitationRepository->getInvitationByCode($code);
        if (!$invitation) {
            Session::flash('__msg', [
                'type' => 'error',
                'text' => 'Could not find the invitation. Have you already accepted it?',
            ]);
            return redirect(route('worker:dashboard'));
        }
        $this->assignmentService->acceptInvitation($invitation);
        Session::flash('__msg', [
            'type' => 'success',
            'text' => 'Successfully accepted invitation to the order <strong>' . $invitation->order->title . '</strong>',
        ]);

        //cancel others invitations
        $others = $this->invitationService->getOthersInvitation($invitation->order_id, $invitation->group_id);
        foreach ($others as $other) {
            $this->invitationService->cancelInvitation($other->code);
        }

        return redirect(route('worker:dashboard'));
    }

    /**
     * Worker route.
     *
     * @param $code
     */
    public function rejectInvitation($code)
    {
        $invitation = $this->invitationRepository->getInvitationByCode($code);
        if (!$invitation) {
            Session::flash('__msg', [
                'type' => 'error',
                'text' => 'Could not find the invitation. Have you already rejected it?',
            ]);
            return redirect(route('worker:dashboard'));
        }
        Session::flash('__msg', [
            'type' => 'success',
            'text' => 'Successfully rejected invitation to the order <strong>' . $invitation->order->title . '</strong>',
        ]);
        $this->invitationService->rejectInvitation($code);
        return redirect(route('worker:dashboard'));
    }

    /**
     * Worker route.
     *
     * @param $code
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function finishOrder($orderId, $groupId)
    {
        $user = Sentinel::check();
        $this->assignmentService->finishOrder($orderId, $groupId, $user->id);
        return redirect(route('worker:dashboard'));
    }

    /**
     * Worker route.
     *
     * @param $code
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function reworkingOrder(Request $request, $orderId)
    {
        $user = Sentinel::check();
        if ($user->inRole('worker')) {
            $groupId = $this->orderProcessor->getGroupByAssignmentsStatus($user->getId(), $orderId, Order::WORK_STATUS_WORKING);
            $this->assignmentService->finishOrder($orderId, $groupId, $user->id);
        }
        $reworkingGroupId = $request->input('group_id');
//        $this->assignmentService->inviteManually($orderId, $groupId);

        $this->assignmentService->reworkingOrder($orderId, $reworkingGroupId, $user->id);

        return response()->json();
    }

    /**
     * Worker route.
     *
     * @param $code
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function reworkingOrderByCustomer(Request $request)
    {   
        $orderId = $request->input('orderId');
        $user = Sentinel::check();
        //remove mark is completed
        $order = Order::find($orderId);
        $this->orderRepository->complete($order, false);
        $order->status = Order::STATUS_SENT_BACK;
        $order->save();

        //add comment 
        $data = [
            'user_id' => Sentinel::check()->id,
            'namespace' => Comment::PUBLIC_CHANNEL,
            'parent_id' => 0,
            'order_id' => $orderId,
            'content' => 'I have sent back this work because is wrong: '.$request->input('content'),
            'role_id' => $user->roles[0]->id
        ];
        $comment = $this->commentRepository->create($data);
        $comment->markApproved();
        event(new ReturnOrderComment($comment->id));

        $extra = ['user' => $user->id];
        $this->historyLogRepository->saveLog($orderId, HistoryLog::SEND_BACK, $extra);

        //response
        return response()->json();
    }

    /**
     * Worker route.
     *
     * @param $code
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function completeOrder($orderId, $groupId)
    {
        $user = Sentinel::check();
        $this->assignmentService->completeOrder($orderId, $groupId, $user->id);
        return redirect(route('worker:dashboard'));
    }
}
