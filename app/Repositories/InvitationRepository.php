<?php
namespace App\Repositories;

use App\Events\Order\Invited;
use App\Events\Order\StatusChanged;
use App\Exceptions\OrderInvitationException;
use App\Jobs\Notification\OrderInvitationJob;
use App\Models\Invitation;
use App\Models\Order;
use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;

class InvitationRepository extends Repository
{
    use DispatchesJobs;

    public function model()
    {
        return Invitation::class;
    }

    public function getLatestInvitation($orderId, $groupId)
    {
        return $this->model
            ->where('group_id', $groupId)
            ->where('order_id', $orderId)
            ->whereNull('rejected_at')
            ->whereNull('accepted_at')
            ->whereNull('deleted_at')
            ->whereNotNull('sent_at')
            ->orderBy('sent_at', 'desc')
            ->first();
    }

    public function invite($orderId, $groupId, $userId)
    {
        $invitation = $this->create([
            'order_id' => $orderId,
            'group_id' => $groupId,
            'user_id' => $userId,
            'sent_at' => Carbon::now(),
            'code' => str_random(32),
        ]);
        $this->dispatch(new OrderInvitationJob($invitation));
    }

    public function cancel($code)
    {
        $invitation = $this->findBy('code', $code);
        if (!$invitation) {
            throw new OrderInvitationException();
        }
        $invitation->delete();
        //notify order processor that there might be no invitations
        event(new StatusChanged($invitation->order_id, Order::WORK_STATUS_ASSIGNING, Order::WORK_STATUS_ASSIGNING, true));
    }

    public function reject($code)
    {
        $invitation = $this->findBy('code', $code);
        if (!$invitation) {
            throw new OrderInvitationException();
        }
        $invitation->rejected_at = Carbon::now();
        $invitation->save();
        //notify order processor that there might be no invitations
        event(new StatusChanged($invitation->order_id, Order::WORK_STATUS_ASSIGNING, Order::WORK_STATUS_ASSIGNING, true));
    }

    public function accept(Invitation $invitation)
    {
        $invitation->accepted_at = Carbon::now();
        $invitation->save();
        $this->cancelInvitations($invitation->order_id, $invitation->group_id, $invitation->id);
    }

    public function getInvitationByCode($code)
    {
        return $this->model->where('code', $code)
            ->whereNull('rejected_at')
            ->whereNull('accepted_at')
            ->whereNull('deleted_at')
            ->first();
    }

    public function getInvitation($userId, $orderId)
    {
//        return $this->model()->where()
    }

    public function cancelInvitations($orderId, $groupId, $except = null)
    {
        $condition = $this->model->where('order_id', $orderId)->where('group_id', $groupId);
        if ($except) {
            $condition->where('id', '<>', $except);
        }
        foreach ($condition->get() as $invitation) {
            $invitation->delete();
        }
    }
}