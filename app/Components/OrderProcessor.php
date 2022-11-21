<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 1/19/16
 * Time: 2:37 PM
 */

namespace App\Components;


use App\Events\Order\InvitationError;
use App\Events\Order\WorkerDeadline;
use App\Exceptions\NoWorkersInvitationException;
use App\Helpers\GuessOrder;
use App\Models\Order;
use App\Models\Worklog;
use App\Models\Setting;
use App\Repositories\OrderRepository;
use App\Repositories\SettingRepository;
use Carbon\Carbon;

class OrderProcessor
{
    use GuessOrder;
    /**
     * @var AssignmentService
     */
    protected $assignmentService;
    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var InvitationService
     */
    protected $invitationService;

    /**
     * @var SettingRepository
     */
    protected $settingRepository;

    /**
     * Create a new command instance.
     *
     * @param AssignmentService $assignmentService
     * @param OrderRepository   $orderRepository
     * @param InvitationService $invitationService
     */
    public function __construct(AssignmentService $assignmentService, OrderRepository $orderRepository, InvitationService $invitationService, SettingRepository $settingRepository)
    {
        $this->assignmentService = $assignmentService;
        $this->orderRepository = $orderRepository;
        $this->invitationService = $invitationService;
        $this->settingRepository = $settingRepository;
    }

    public function processAll()
    {
        foreach ($this->orderRepository->getOrdersForProcessing() as $order) {
            if ($this->canProcess($order)) {
                $this->process($order);
            }
        }
    }

    public function processGroupsChange($group)
    {
        foreach ($this->orderRepository->getOrdersForProcessing() as $order) {
            $this->assignmentService->makeAssignments($order->id, $group->id);
            $this->process($order);
        }
    }

    public function process($order)
    {
        $order = $this->guessOrder($order);
        $this->initialize($order);
        try {
            $this->assignmentService->nextStep($order);
        } catch (NoWorkersInvitationException $e) {
            event(new InvitationError($order, $e));
        }
    }

    public function initialize($order)
    {   
        $order = $this->guessOrder($order);
        if (!$order->deadline->gte($order->created_at)) {
            $this->setDeadline($order);
        }
        if (!$this->assignmentService->hasAssignments($order->id)) {
            $this->assignmentService->makeAssignments($order->id);
        }
        if (!$this->invitationService->canInviteAutomatically($order)) { 
            //@todo: notify admin about new order with manual invitations
        }
    }

    public function canProcess($order)
    {
        $order = $this->guessOrder($order);
        return ($order->isPaid() || $order->user->delayed_payment > 0);
    }

    public function notifyDeadline()
    {
        foreach ($this->orderRepository->getDeadlineWork() as $item) {
            $log = $this->assignmentService->logWork($item->order_id, $item->group_id, $item->user_id, Order::WORK_STATUS_OVERDUE);
            $worklog = Worklog::find($log);
            event(new WorkerDeadline($worklog));
        }
    }

    protected function setDeadline(Order $order)
    {

        $setting = $this->settingRepository->getByKey(Setting::GOT);
        $got = $setting->value;
        $order->deadline = Carbon::now()->addMinutes($got);
        $order->save();
    }

    public function extendDeadline(Order $order, $hours)
    {
        $minutes = intval($hours*60);
        $order->deadline = $order->deadline->addMinutes($minutes);
        $order->save();
    }

    public function getGroupByAssignmentsStatus($userId, $orderId, $status)
    {
        $assignmentGroupId = null;
        $assignments = $this->assignmentService->getAssignments($orderId, null, $userId);
        if($assignments) {
            $assignments = collect($assignments)->where('status', $status);
            $assignmentGroupId = $assignments->max('group_id');
        }
        return $assignmentGroupId;
    }

    public function getGroupReworking($userId, $orderId, $status)
    {
        $assignmentGroupId = null;
        $assignments = $this->assignmentService->getAssignments($orderId, null, $userId);
        if($assignments) {
            $assignments = collect($assignments)->where('status', $status);
            if(sizeof($assignments)) {
                $assignmentGroupId = $assignments->first()->group_id;
            }
        }
        return $assignmentGroupId;
    }

    public function checkTheLastGroup($groupId)
    {
        $result = false;
        if($groupId !== null) {
             $result = $this->assignmentService->checkTheLastGroup($groupId);
        }
        return $result;
    }

    public function getListOfReworkingGroups()
    {
        return $this->assignmentService->getListOfReworkingGroups();
    }
}