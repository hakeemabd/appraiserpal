<?php

use App\Components\AssignmentService;
use App\Components\InvitationService;
use App\Models\Invitation;
use App\Models\Order;
use App\User;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Artisan;
use Laracasts\Behat\Context\Migrator;

/**
 * Defines application features from the specific context.
 */
class DatabaseOrderContext extends \Behat\MinkExtension\Context\MinkContext implements Context, SnippetAcceptingContext
{
    use Migrator;

    protected $orders;
    protected $customer;
    protected $workers;
    /**
     * @var \App\Repositories\WorkerGroupRepository
     */
    protected $workerGroupsRepository;
    protected $groups;
    protected $invitationRepository;
    protected $invitationService;
    protected $assignmentService;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->workerGroupsRepository = app()->make(App\Repositories\WorkerGroupRepository::class);
        $this->invitationRepository = \Mockery::mock(app()->make(App\Repositories\InvitationRepository::class));
        $this->invitationRepository->shouldReceive('create');
        $this->invitationService = new InvitationService($this->workerGroupsRepository, $this->invitationRepository);
        $this->assignmentService = new AssignmentService($this->workerGroupsRepository, $this->invitationService);
        Artisan::call('migrate:refresh');
    }

    /**
     * @Transform /^(should|should not)$/
     */
    public function interpretShould($string)
    {
        return $string === 'should' ? true : false;
    }

    /**
     * @Transform /^is (unavailable|available)$/
     */
    public function interpretAvailability($string)
    {
        return $string === 'available' ? 1 : 0;
    }

    /**
     * @Transform /^group (.*)$/
     */
    public function groupIdByName($string)
    {
        return $this->workerGroupsRepository->findBy('name', $string)->id;
    }

    /**
     * @Transform /^Worker (.*)$/
     */
    public function workerIdByName($string)
    {
        $users = app()->make(\App\Repositories\UserRepository::class);
        return $users->findBy('first_name', $string)->id;
    }

    /**
     * @Transform /^order (.*)$/
     */
    public function orderIdByTitle($string)
    {
        $orders = app()->make(\App\Repositories\OrderRepository::class);
        return $orders->findBy('title', $string)->id;
    }

    /**
     * @Transform /^now|(\d+) mins ago$/
     */
    public function strToCarbonNow($string)
    {
        if ($string == 'now') {
            return Carbon::create();
        }
//        if (preg_match('!(\d+) mins ago!is', $string, $matches)) {
        return Carbon::create()->subMinutes($string);
//        }
    }
//    /**
//     * @Transform /^$/
//     */
//    public function strToCarbonAgo($string)
//    {
//
//            return Carbon::create()->subMinutes($string);
//    }

    /**
     * @Given The following orders exist:
     */
    public function theFollowingOrdersExist(TableNode $table)
    {
        $hash = $table->getHash();
        $users = app()->make(\App\Repositories\UserRepository::class);
        foreach ($hash as $row) {
            $row = $this->trueTo1($row);
            $order = factory(App\Models\Order::class)->make([
                'title' => $row['title'],
                'auto_invite' => $row['auto_invite'],
                'status' => $row['status'],
            ]);
            $customer = $users->findBy('first_name', $row['customer']);
            $customer->orders()->save($order);
            if ($row['worked']) {
                preg_match_all('!([a-z]+) +?as +?([a-z]+)(,\s*)?!is', $row['worked'], $matches);
                foreach ($matches[1] as $i => $worker) {
                    $user = $users->findBy('first_name', $worker);
                    $group = $this->workerGroupsRepository->findBy('name', $matches[2][$i]);
                    $this->assignmentService->assignWorker($order->id, $group->id, $user->id, \App\Models\Order::WORK_STATUS_FINISHED);
                }
            }
        }
    }

    protected function trueTo1($arr)
    {
        foreach ($arr as $key => $value) {
            if ($value === 'true') {
                $arr[$key] = 1;
            } elseif ($value === 'false') {
                $arr[$key] = 0;
            }
        }
        return $arr;
    }

    /**
     * @Given The following groups exist:
     */
    public function theFollowingGroupsExist(TableNode $table)
    {
        $hash = $table->getHash();
        foreach ($hash as $row) {
            factory(App\Models\WorkerGroup::class)->create($row);
        }
    }

    /**
     * @Given /^The following (worker|customer)s exist:$/
     */
    public function theFollowingUsersExist($role, TableNode $table)
    {
        $hash = $table->getHash();
        $workerRole = Sentinel::findRoleBySlug('worker');
        $customerRole = Sentinel::findRoleBySlug('customer');
        foreach ($hash as $row) {
            $row = $this->trueTo1($row);
            $user = factory(App\User::class)->make(array_except($row, ['group', 'first_turnaround', 'next_turnaround']));
            $u = Sentinel::registerAndActivate(array_merge($user->toArray(), ['password' => 1234]));
            if ($role == 'worker') {
                $group = $this->workerGroupsRepository->findBy('name', $row['group']);
                $this->workerGroupsRepository->addWorkerToGroup($u->id, $group->id, [
                    'first_turnaround' => $row['first_turnaround'],
                    'next_turnaround' => $row['next_turnaround'],
                ]);
                $workerRole->users()->attach($u);
            } else {
                $customerRole->users()->attach($u);
            }
        }
    }

    /**
     * @Given /^"(order [^"]+)" is ready for processing$/
     */
    public function isReadyForProcessing($orderId)
    {
        $order = Order::find($orderId);
        return ($order->status == \App\Models\Order::STATUS_NEW && ($order->isPaid() || $order->customer->mayDelayPayment()));
    }

    /**
     * @Given /^"(Worker [^"]+)" (is (?:unavailable|available))$/
     */
    public function becameUnAvailable($userId, $availability)
    {
        User::where('id', $userId)->update(['available' => $availability]);
    }

    /**
     * @Given /^System invited "(Worker [^"]+)" to "(group [^"]+)" to "(order [^"]+)" (now|\d+ mins ago)$/
     */
    public function systemInvitedTo($workerId, $groupId, $orderId, $when)
    {
        $this->assignmentService->assignWorker($orderId, $groupId, null, Order::WORK_STATUS_ASSIGNING);
        $this->invitationToFor($workerId, 'got', $orderId, $groupId, $when);
    }

    /**
     * @Given /^"(Worker [^"]+)" (rejected|got) invitation to "(order [^"]+)" for "(group [^"]+)" (now|\d+ mins ago)$/
     */
    public function invitationToFor($workerId, $action, $orderId, $groupId, $when)
    {
        $data = [
            'user_id' => $workerId,
            'order_id' => $orderId,
            'group_id' => $groupId,
        ];
        switch ($action) {
            case 'rejected':
                $invitation = Invitation::where('user_id', $workerId)
                    ->where('order_id', $orderId)
                    ->where('group_id', $groupId)
                    ->first();
                if ($invitation) {
                    $invitation->rejected_at = $when;
                    $invitation->save();
                } else {
                    $data['rejected_at'] = $when;
                    $data['sent_at'] = $when->subMinutes(10);
                }
                break;
            case 'got':
                $data['sent_at'] = $when;
                break;

        }
        Invitation::create($data);
    }


###formatter:off
    /**
     * @When /^Admin invites (experienced|experienced and available|all|available) workers from "(group [^"]+)" to "(order [^"]+)"$/
     * ###formatter:on
     */
###formatter:on

    public function adminInvitesGroup($invitationType, $groupId, $orderId)
    {
        $this->invitationService->inviteTo($orderId, $groupId, $invitationType);
    }

    /**
     * @When /^System processe(?:s|d) "(order [^"]+)" ?(now|\d+ mins ago)?(?: once again)?$/
     */
    public function systemProcesses($orderId, $when = null)
    {
        if ($when) {
            Carbon::setTestNow($when);
        }
        try {
            $this->assignmentService->nextStep(Order::find($orderId));
        } catch (\App\Exceptions\NoAutoInvitationException $e) {
            //swallow this exception. It is correct system behavior
        }
        if ($when) {
            Carbon::setTestNow();
        }
    }



###formatter:off
    /**
     * @Then /^"(Worker [^"]+)" (should|should not) receive notification for the "(order [^"]+)" to the "(group [^"]+)"$/
     */
###formatter:on
    public function shouldReceiveNotification($workerId, $should, $orderId, $groupId)
    {
        $argument = both(hasEntry('order_id', $orderId))->
        andAlso(hasEntry('user_id', $workerId))->
        andAlso(hasEntry('group_id', $groupId));
        if ($should) {
            $this->invitationRepository->shouldHaveReceived('create', [$argument]);
        } else {
            $this->invitationRepository->shouldNotHaveReceived('create', [$argument]);
        }
    }
}
