<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 1/12/16
 * Time: 12:38 AM
 */

namespace App\Components;


use App\Helpers\GuessOrder;
use App\Models\Invitation;
use App\Repositories\InvitationRepository;
use App\Repositories\WorkerGroupRepository;
use App\User;

class InvitationService
{
    use GuessOrder;

    const INVITE_EXPERIENCED = 'experienced';
    const INVITE_EXPERIENCED_AND_AVAILABLE = 'experienced and available';
    const INVITE_AVAILABLE = 'available';
    const INVITE_ALL = 'all';

//    const EXCLUDE_UNAVAILABLE = 'unavailable';
    const EXCLUDE_INVITED = 'invited';

    protected $wgRepository;
    protected $invitationRepository;

    public function __construct(WorkerGroupRepository $wg, InvitationRepository $ir)
    {
        $this->wgRepository = $wg;
        $this->invitationRepository = $ir;
    }

    public function invite($order, $groupId)
    {
        $order = $this->guessOrder($order);
        if (!$this->canInviteAutomatically($order)) {
            return false;
        }
        //try getting users who were working with the customer before and are available
        foreach ([self::INVITE_EXPERIENCED_AND_AVAILABLE, self::INVITE_AVAILABLE, self::INVITE_ALL] as $scope) {
            $workers = $this->expandInviteeToWorkers($order, $groupId, $scope, self::EXCLUDE_INVITED);
            if ($workers && $workers->count() > 0) {
                $this->doInvite($workers, $order->id, $groupId);
                return true;
            }
        }
        return false;
    }

    public function canInviteAutomatically($order)
    {
        $order = $this->guessOrder($order);
        return ($order->auto_invite || $order->user->auto_invite);
    }

    protected function expandInviteeToWorkers($order, $groupId, $invitee, $exclude = null)
    {   
        $order = $this->guessOrder($order);
        $workers = [];
        if (is_string($invitee)) {
            $workerScope = [
                'groupId' => $groupId,
            ];
            switch ($invitee) {
                case self::INVITE_EXPERIENCED:
                    $workerScope['workedFor'] = $order->user_id;
                    break;
                case self::INVITE_EXPERIENCED_AND_AVAILABLE:
                    $workerScope['workedFor'] = $order->user_id;
                    $workerScope['availability'] = 'now';
                    break;
                case self::INVITE_AVAILABLE:
                    $workerScope['availability'] = 'now';
                    break;
            }
            if ($exclude == self::EXCLUDE_INVITED) {
                $workerScope['exclude'] = [
                    'type' => self::EXCLUDE_INVITED,
                    'orderId' => $order->id,
                ];
            }
            $workers = $this->wgRepository->getWorkers($workerScope);
        }
        if (is_array($invitee) && sizeof($invitee) > 0) {
            if (!($invitee[0] instanceof User)) {
                $workers = $this->wgRepository->getWorkers(['ids' => $invitee]);
            }
        }
        if ($invitee instanceof User) {
            $workers = [$invitee];
        }

        return $workers;
    }

    protected function doInvite($workers, $orderId, $groupId)
    {
        foreach ($workers as $worker) { 
            $this->invitationRepository->invite($orderId, $groupId, $worker->id);
        }
    }

    public function inviteTo($orderId, $groupId, $invitee)
    {  
        $workers = $this->expandInviteeToWorkers($orderId, $groupId, $invitee);
        $this->doInvite($workers, $orderId, $groupId);
    }

    public function getLatestInvitation($orderId, $groupId)
    {
        return $this->invitationRepository->getLatestInvitation($orderId, $groupId);
    }

    public function cancelInvitation($code)
    {
        $this->invitationRepository->cancel($code);
    }

    public function acceptInvitation(Invitation $invitation)
    {
        $this->invitationRepository->accept($invitation);
    }

    public function rejectInvitation($code)
    {
        $this->invitationRepository->reject($code);
    }

    public function checkInvitation($userId, $orderId)
    {
//        $this->invitationRepository
    }

    public function getOthersInvitation($orderId, $groupId)
    {
        $invitations = Invitation::where('order_id', $orderId)
            ->where('group_id', $groupId)
            ->whereNull('accepted_at')
            ->whereNull('rejected_at')->get();

        return $invitations;
    }
//    public function getGroupOrdinalNumber($orderId, $groupId)
//    {
//        $groups = $this->invitationRepository->findAllBy('order_id', $orderId);
//        $group = $this->invitationRepository->findWhere(['order_id' => $orderId, 'group_id' => $groupId])->first();
//        return $groups->search($group);
//    }
}