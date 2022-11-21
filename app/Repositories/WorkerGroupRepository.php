<?php

namespace App\Repositories;

use App\Components\InvitationService;
use App\Components\OrderProcessor;
use App\Exceptions\OrderAssignmentException;
use App\Helpers\GuessOrder;
use App\Models\Order;
use App\Models\WorkerGroup;
use App\User;
use Bosnadev\Repositories\Eloquent\Repository;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class WorkerGroupRepository extends Repository
{
    use GuessOrder;
    /**
     * @var OrderRepository
     */
    protected $orderRepository;


    public function __construct(App $app, Collection $collection)
    {
        parent::__construct($app, $collection);
        $this->orderRepository = app()->make(OrderRepository::class);
    }

    public function model()
    {
        return WorkerGroup::class;
    }

    public function create(array $data)
    {
        $group = parent::create($data);
        $this->onGroupsChange($group);
        return $group;
    }

    public function update(array $data, $group)
    {
        $group->update($data);
        $this->onGroupsChange($group);
        return $group;
    }

    protected function onGroupsChange($group)
    {
        $orderProcessor = app('OrderProcessor');
        $orderProcessor->processGroupsChange($group);
    }

    public function addWorkerToGroup($workerId, $groupId, $params = [])
    {
        /**
         * @var WorkerGroup
         */
        $group = $this->find($groupId);
        if (!$groupId) {
            throw new ModelNotFoundException("Group $groupId was not found");
        }

        $group->workers()->attach($workerId, $params);
    }

    public function getWorkerTurnaround($workerId, $groupId, $order)
    {
        $order = $this->guessOrder($order);
        $worker = $this->find($groupId)->workers()->where('user_id', $workerId)->first();
        if (!$worker) {
            throw new OrderAssignmentException("Worker $workerId is not in the group $groupId");
        }

        if ($order->status === Order::STATUS_CREATING || $order->status === Order::STATUS_NEW) {
            return $worker->pivot->first_turnaround;
        }
        return $worker->pivot->next_turnaround;
    }

    /**
     * @param array $params
     *   [workedFor] => <clientId>
     *   [availability] => now|any
     *   [groupId] => <id of the group>
     *   [ids] => [array of users]
     */
    public function getWorkers($params)
    {
        $condition = DB::table('users');
        if (isset($params['groupId'])) {
            $condition->join('worker_groups', 'users.id', '=', 'worker_groups.user_id')
                ->where('worker_groups.group_id', $params['groupId']);
        }
        if (isset($params['workedFor'])) {
            //@todo: check for status of the order
            $condition->join('order_assignments', 'users.id', '=', 'order_assignments.user_id')
                ->join('orders', 'order_assignments.order_id', '=', 'orders.id')
                ->whereIn('orders.status', [Order::STATUS_ARCHIVED, Order::STATUS_DELIVERED])
                ->where('orders.user_id', '=', $params['workedFor']);
        }
        if (isset($params['availability']) && $params['availability'] == 'now') {
            $condition->where('users.available', '=', '1');
        }
        if (isset($params['ids'])) {
            $condition->whereIn('users.id', $params['ids']);
        }
        if (isset($params['exclude']) && $params['exclude']['type'] == InvitationService::EXCLUDE_INVITED) {
            $condition->whereNotIn('users.id', function ($query) use ($params) {
                $query->select('invitations.user_id')->distinct()->from('invitations')
                    ->where('invitations.group_id', $params['groupId'])
                    ->where('invitations.order_id', $params['exclude']['orderId']);
            });
        }
        $ids = $condition->select('users.id')->lists('users.id');
        return User::whereIn('id', $ids)->get();
    }

    public function allSorted()
    {
        static $all;
        if (!$all) {
            $self = $this;
            $all = Cache::rememberForever('groups', function () use ($self) {
                return $self->all()->sortBy('sort');
            });
        }
        return $all;
    }

    public function allSortedNotCached()
    {
        Cache::forget('groups');
        return $this->allSorted();
    }

    public function getTimeLeft($orderId, $userId, $groupId, $startedTime)
    {
        if (!is_null($startedTime)) {
            $turnaround = $this->getWorkerTurnaround($userId, $groupId, $orderId);
            $interval = intval(round(abs(strtotime("now") - strtotime($startedTime)) / 60, 2));
            //$interval = $interval.'*'.strtotime($startedTime).'*'.strtotime("now");
            $time_left = $turnaround - $interval;
            $seconds = 60 - date_format($startedTime, 's');
            /*$time_left_human = '';
            if ($time_left > 0) {
                if ($time_left < 60) {
                    $time_left_human = $time_left.'min';
                } else {
                    $hrs = intval($time_left/60);
                    $min = $time_left-($hrs*60);
                    if (strlen($min) < 2) {
                        $min = '0'.$min;
                    }
                    $time_left_human = $hrs.':'.$min.'hrs';
                }
            } else {
                $time_left_human = '0min';
            }
        } else {
            $time_left_human = '0min';
        }*/

            return "
        <div id='countdown" . $orderId . "'></div>
        <script type='text/javascript'>

            function startTimer" . $orderId . "(time, seconds) {
                var element" . $orderId . " = document.getElementById( 'countdown" . $orderId . "' );
                var timer" . $orderId . " = (time*60)+seconds;
                setInterval(function () {

                    var hours" . $orderId . " = parseInt(timer" . $orderId . " / 3600);
                    var minutes" . $orderId . " = parseInt((timer" . $orderId . " % 3600)/60);
                    var seconds" . $orderId . " = parseInt(timer" . $orderId . " % 60);

                    minutes" . $orderId . " = minutes" . $orderId . " < 10 ? '0' + minutes" . $orderId . " : minutes" . $orderId . ";
                    seconds" . $orderId . " = seconds" . $orderId . " < 10 ? '0' + seconds" . $orderId . " : seconds" . $orderId . ";

                    if (timer" . $orderId . " > 0) {
                        element" . $orderId . ".innerHTML = hours" . $orderId . "+':'+minutes" . $orderId . "+':'+seconds" . $orderId . "+' hrs';
                    } else {
                        element" . $orderId . ".innerHTML = '00:00';
                    }

                    --timer" . $orderId . ";
                }, 1000);
            }
            startTimer" . $orderId . "(" . $time_left . ", " . $seconds . ");
        </script>";
        } else {
            return '00:00';
        }
    }
}