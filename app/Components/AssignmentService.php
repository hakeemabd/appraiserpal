<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 1/13/16
 * Time: 4:47 PM
 */

namespace App\Components;


use App\Events\Order\StatusChanged;
use App\Events\Order\CompleteOrder;
use App\Exceptions\NoWorkersInvitationException;
use App\Exceptions\OrderInvitationException;
use App\Models\Invitation;
use App\Models\Order;
use App\Models\HistoryLog;
use App\User;
use App\Repositories\WorkerGroupRepository;
use App\Repositories\HistoryLogRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

//use Illuminate\Support\Facades\Cache;

class AssignmentService
{
    protected $assignmentTable = 'order_assignments';
    protected $worklogTable = 'worklogs';

    /**
     * @var WorkerGroupRepository
     */
    protected $workerGroupRepository;
    /**
     * @var InvitationService
     */
    protected $invitationService;

    public function __construct(WorkerGroupRepository $wgr, InvitationService $invitationService, HistoryLogRepository $historyLogRepository)
    {
        $this->workerGroupRepository = $wgr;
        $this->invitationService = $invitationService;
        $this->historyLogRepository = $historyLogRepository;
    }

    public function hasAssignments($orderId, $groupId = null)
    {
        return $this->getAssignments($orderId, $groupId) != null;
    }

    public function getAssignments($orderId, $groupId = null, $userId = null)
    {
        $tags = ['order_' . $orderId];
        if ($groupId) {
            $tags[] = 'group_' . $groupId;
        }
//        return Cache::tags($tags)->rememberForever('assignments', function () use ($orderId, $groupId) {
        $condition = DB::table($this->assignmentTable)->where('order_id', $orderId);
        if ($groupId !== null) {
            $condition->where('group_id', $groupId);
        }
        if ($userId !== null) {

            $condition->where('user_id', $userId);
        }
        return $condition->get();
//        });
    }

    public function inviteManually($orderId, $groupId, $users)
    {
        $assignments = $this->getAssignments($orderId, $groupId);

        if (!$assignments || !$assignments[0]) {
            throw new OrderInvitationException();
        }

        $assignment = $assignments[0];
        //prohibit any invitations if users are assigned
        if ($assignment->user_id) {
            throw new OrderInvitationException();
        } else {
            $this->invitationService->inviteTo($orderId, $groupId, $users);
            $this->assignWorker($assignment->order_id, $assignment->group_id, null, Order::WORK_STATUS_ASSIGNING);

            //history log
            $extra = ['group' => $groupId, 'user' => $users[0]];
            $this->historyLogRepository->saveLog($orderId, HistoryLog::INVITED, $extra);
            event(new StatusChanged($assignment->order_id));
        }
    }

    /**
     * Assigns worker or updates assignment for a given order, group, worker and status.
     *
     * @param      $orderId
     * @param      $groupId
     * @param null $workerId
     * @param null $status
     * @param int  $processed Denotes if further processing is required
     *
     * @return \StdClass
     */
    public function assignWorker($orderId, $groupId, $workerId = null, $status = null)
    {
        if ($status === null) {
            $status = Order::WORK_STATUS_ON_HOLD;
        }

        $worklogId = $this->logWork($orderId, $groupId, $workerId, $status);

        $data = [
            'order_id' => $orderId,
            'group_id' => $groupId,
            'status' => $status,
            'worklog_id' => $worklogId,
        ];
        $data['user_id'] = $workerId;

        $condition = DB::table($this->assignmentTable)->where('order_id', $orderId)->where('group_id', $groupId);
        $row = $condition->first();
        if ($row) {
            $condition->update(array_except($data, ['order_id', 'group_id']));
        } else {
            DB::table($this->assignmentTable)->insert($data);
        }
//        Cache::tags(['order_' . $orderId])->forget('assignments');
        return (object)$data;
    }

    /**
     * Adds a worklog entry for a given order, group, status and worker
     *
     * @param      $orderId
     * @param      $groupId
     * @param null $workerId
     * @param null $status
     *
     * @return integer
     * @throws \App\Exceptions\OrderAssignmentException
     */
    public function logWork($orderId, $groupId, $workerId = null, $status = null)
    {
        $data = [
            'order_id' => $orderId,
            'group_id' => $groupId,
            'status' => $status,
        ];
        if ($workerId) {
            $data['user_id'] = $workerId;
        }
        if ($status === Order::WORK_STATUS_WORKING) {
            $now = Carbon::now();
            $data['started_at'] = $now->format(Carbon::DEFAULT_TO_STRING_FORMAT);
            //define deadline for the worker
            $data['deadline'] = $now->addMinutes($this->workerGroupRepository->getWorkerTurnaround($workerId, $groupId, $orderId))
                ->format(Carbon::DEFAULT_TO_STRING_FORMAT);
            //history log
            $extra = ['group' => $groupId, 'user' => $workerId];
            $this->historyLogRepository->saveLog($orderId, HistoryLog::ACEPT_INVITED, $extra);
        }
        if ($status === Order::WORK_STATUS_FINISHED) {
            $now = Carbon::now();
            $data['finished_at'] = $now->format(Carbon::DEFAULT_TO_STRING_FORMAT);
        }

        return DB::table($this->worklogTable)->insertGetId($data);
    }

    /**
     * @param Invitation $invitation
     */
    public function acceptInvitation(Invitation $invitation)
    {
        $this->invitationService->acceptInvitation($invitation);
        $this->assignWorker($invitation->order_id, $invitation->group_id, $invitation->user_id, Order::WORK_STATUS_ON_HOLD);
        if ($invitation->order->status == Order::STATUS_NEW) {
            $invitation->order->status = Order::STATUS_CREATING;
            $invitation->order->save();
        }
        event(new StatusChanged($invitation->order_id));
    }

    public function finishOrder($orderId, $groupId, $userId)
    {
        $this->assignWorker($orderId, $groupId, $userId, Order::WORK_STATUS_FINISHED);
        //history log
        $extra = ['user' => $userId];
        $this->historyLogRepository->saveLog($orderId, HistoryLog::WORKER_FINISHED, $extra);
        event(new StatusChanged($orderId));
    }

    public function completeOrder($orderId, $groupId, $userId)
    {
        $this->assignWorker($orderId, $groupId, $userId, Order::WORK_STATUS_FINISHED);
        $order = Order::find($orderId);
        $order->status = Order::STATUS_DONE;
        $order->save();
        //history log
        $extra = ['user' => $userId];
        $this->historyLogRepository->saveLog($orderId, HistoryLog::WORKER_MARK_COMPLETED, $extra);
        event(new CompleteOrder($orderId, $userId));
        event(new StatusChanged($orderId));
    }

    public function bidSave($invitation,$request)
    {
        $bidData = new Bid();
        $bidData->worker_id = $invitation->id;
        $bidData->order_id = $invitation->order_id;
        $bidData->group_id = $invitation->group_id;
        $bidData->bid_amount = $request->fname;
        $bidData->save();
    }

    public function reworkingOrder($orderId, $groupId, $userId)
    {
//        $this->assignWorker($orderId, $groupId, $userId, Order::WORK_STATUS_FINISHED);
//        event(new StatusChanged($orderId));
        $order = Order::find($orderId);
        if ($order->status !== Order::STATUS_REWORKING) {
            $order->status = Order::STATUS_REWORKING;
            $order->save();
        }

        //history log
        $extra = ['user' => $userId];
        $this->historyLogRepository->saveLog($orderId, HistoryLog::SEND_BACK, $extra);

        $assigns = DB::table($this->assignmentTable)->where('order_id', $orderId)->where('group_id', $groupId)->where('status', Order::WORK_STATUS_FINISHED)->get();

        $sendUser = false;
        if (!is_null($assigns)) {
            foreach ($assigns as $assign) {
                $user = User::find($assign->user_id);
                if ($user->available && !$sendUser) {
                    $sendUser = true;

                    $this->invitationService->inviteTo($orderId, $groupId, [$user->id]);
                    $this->assignWorker($orderId, $groupId, $user->id, Order::WORK_STATUS_ASSIGNING);

                    //history log
                    $extra = ['group' => $groupId, 'user' => $user->id];
                    $this->historyLogRepository->saveLog($orderId, HistoryLog::INVITED, $extra);
                }
            }
        }

        if (!$sendUser) {
            $this->assignWorker($orderId, $groupId, null, Order::WORK_STATUS_INVITING);
        }
        event(new StatusChanged($orderId));
    }

    public function unassign($orderId, $groupId, $userId)
    {
        $this->assignWorker($orderId, $groupId, null, Order::WORK_STATUS_INVITING);
        //@todo: notify worker he has been unassigned
        event(new StatusChanged($orderId));
    }

    /**
     * Initializes the working process and processed every step of the process.
     *
     * @param $order
     *
     * @throws NoWorkersInvitationException
     * @throws \App\Exceptions\OrderInvitationException
     */
    public function nextStep($order)
    {
        foreach ($this->workerGroupRepository->allSorted() as $group) {
            $assignments = $this->getAssignments($order->id, $group->id);
            $assignment = $assignments ? $assignments[0] : null;
            if ($assignment === null) {
                $assignment = $this->makeAssignments($order->id, $group->id);
            }
            $method = 'process' . ucfirst($assignment->status) . 'Assignment';
            if (method_exists($this, $method)) {
                if (!$this->$method($assignment)) {
                    $this->setCurrent($assignment);
                    break;
                }
            }
        }
        //@todo: reached the end, switch status of the order.
    }

    /**
     * Creates assignment records for a certain group of users for a given order.
     *
     * @param      $orderId
     * @param null $groupId
     *
     * @return array
     */
    public function makeAssignments($orderId, $groupId = null)
    {
        if ($assignments = $this->getAssignments($orderId, $groupId)) {
            if (sizeof($assignments) > 0) {
                return $assignments;
            }
        }
        if ($groupId !== null) {
//            Cache::tags(['order_' . $orderId])->forget('assignments');
            return $this->assignWorker($orderId, $groupId, null, Order::WORK_STATUS_ASSIGNING);

        }
        foreach ($this->workerGroupRepository->allSorted() as $group) {
            $assignments[] = $this->assignWorker($orderId, $group->id, null, Order::WORK_STATUS_INVITING);
        }
//        Cache::tags(['order_' . $orderId])->forget('assignments');
        return $assignments;
    }

    protected function setCurrent($assignment)
    {
        DB::table($this->assignmentTable)
            ->where('order_id', $assignment->order_id)
            ->update([
                'active' => 0,
            ]);
        DB::table($this->assignmentTable)
            ->where('order_id', $assignment->order_id)
            ->where('group_id', $assignment->group_id)
            ->update([
                'active' => 1,
            ]);
    }

    /**
     * @param \StdClass $assignment Assignment info
     *
     * @return bool
     * @throws NoWorkersInvitationException
     */
    protected function processInvitingAssignment($assignment)
    {
        if (!$this->invitationService->canInviteAutomatically($assignment->order_id)) {
            return false;
        }
        if ($this->invitationService->invite($assignment->order_id, $assignment->group_id)) {
            $this->assignWorker($assignment->order_id, $assignment->group_id, null, Order::WORK_STATUS_ASSIGNING);
            event(new StatusChanged($assignment->order_id));
            return false;
        } else {
            throw new NoWorkersInvitationException($assignment->order_id, $assignment->group_id);
        }
    }

    /**
     * @param \StdClass $assignment Assignment info
     *
     * @return bool
     */
    protected function processAssigningAssignment($assignment)
    {
        if ($this->invitationsTimedOut($assignment->order_id, $assignment->group_id)) {
            $this->assignWorker($assignment->order_id, $assignment->group_id, null, Order::WORK_STATUS_INVITING);
            event(new StatusChanged($assignment->order_id));
        }
        return false;
    }

    /**
     * Checks if invitations have timed out for the certain order and group
     *
     * @param $orderId
     * @param $groupId
     *
     * @return bool
     */
    public function invitationsTimedOut($orderId, $groupId)
    {
        $invitationTimeout = 15;//todo: take from config when system settings are ready.
        $invitation = $this->invitationService->getLatestInvitation($orderId, $groupId);
        if (!$invitation) { //if there are no invitations that are valid for this order and group
            return true;
        }
        //sent_at plus X minutes should be in the future. It it's not, then it's timed out
        $invitation->sent_at->addMinutes($invitationTimeout)->lt(Carbon::now());
        return false;
    }

    /**
     * @param \StdClass $assignment Assignment info
     *
     * @return bool
     */
    protected function processOnholdAssignment($assignment)
    {
//        $assignments = $this->getAssignments($assignment->order_id);
//        $groupOrderId = array_search($assignment, $assignments);
//        if($groupOrderId == 1) {
//            $order = Order::find($assignment->order_id);
//            $order->status = Order::STATUS_REWORKING;
//            $order->save();
//        }
        $this->assignWorker(
            $assignment->order_id,
            $assignment->group_id,
            $assignment->user_id,
            Order::WORK_STATUS_WORKING
        );
        return false;
    }

    /**
     * We need to break the loop if the person is working. No need to invite/process anything for the following groups
     *
     * @param \StdClass $assignment
     *
     * @return bool
     */
    protected function processWorkingAssignment($assignment)
    {
        return false;
    }

    /**
     *
     *
     * @param $assignment
     */
    protected function processFinishedAssignment($assignment)
    {
        //@todo: rework logic! Consider this case
        // group 1 finished
        // group 2 on hold
        // we can't change order status in this case...
        // we cat change it when the main loop finishes without break;s meaning everythig is processed
        //@todo: probably need to check if he is the last one...
//        $order = Order::find($assignment->order_id);
//        $order->status = Order::STATUS_DONE;
//        $order->save();
        return true;
    }

    public function checkTheLastGroup($groupId)
    {
        $groups = $this->workerGroupRepository->allSortedNotCached();
        $sortId = $groups->where('id', $groupId)->first()->sort;
        $maxSortId = $groups->max('sort');
        return $sortId == $maxSortId;
    }

    public function getListOfReworkingGroups()
    {
        $groups = $this->workerGroupRepository->allSorted();
        return $groups;
    }
}