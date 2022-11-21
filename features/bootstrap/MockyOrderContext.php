<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use App\Components\InvitationService;

/**
 * Defines application features from the specific context.
 */
class MockyOrderContext extends \Behat\MinkExtension\Context\MinkContext implements Context, SnippetAcceptingContext
{
    protected $orders;
    protected $customer;
    protected $workers;
    protected $workerGroupsRepository;
    protected $groups;
    protected $invitationRepository;
    protected $invitationService;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->workerGroupsRepository = \Mockery::mock('alias:App\Repositories\WorkerGroupRepository');
        $this->invitationRepository = \Mockery::mock('alias:App\Repositories\InvitationRepository');
        $this->invitationRepository->shouldReceive('create');
        $this->invitationService = new InvitationService($this->workerGroupsRepository, $this->invitationRepository);
    }

    /**
     * @Transform /^(should|should not)$/
     */
    public function interpretShould($string)
    {
        return $string === 'should' ? true : false;
    }

    /**
     * @Given /^There is a (new|creating|reworking|done|completed) order id=(\d+)$/
     */
    public function thereIsANewOrder($status, $id)
    {
        $this->orders[$id] = \Mockery::mock('order');
        $this->orders[$id]->id = $id;
        $this->orders[$id]->status = $status;
    }

    /**
     * @Given /^Order (\d+) customer is "([^"]+)" \((\d+)\)$/
     */
    public function orderCustomerIs($orderId, $customerName, $customerId)
    {
        $this->orders[$orderId]->customer_id = $customerId;
        $this->customer = $customer = \Mockery::mock();
        $customer->name = $customerName;
        $customer->id = $customerId;
        $this->orders[$orderId]->shouldReceive('customer')->withNoArgs()->andReturn($customer);
    }

    /**
     * @Given There the following groups exist:
     */
    public function thereTheFollowingGroupsExist(TableNode $table)
    {
        $hash = $table->getHash();
        foreach ($hash as $row) {
            $group = \Mockery::mock('group', $row);
            $group->id = $row['id'];
            $group->name = $row['name'];
            $group->sort = $row['sort'];
            $this->groups[$row['name']] = $group;

            $this->workerGroupsRepository->shouldReceive('find')->withArgs([$row['id']])->andReturn($group);
        }
    }

    /**
     * @Given The following workers exist:
     */
    public function theFollowingWorkersExist(TableNode $table)
    {
        $hash = $table->getHash();
        $customer = &$this->customer;
        foreach ($hash as $row) {
            $worker = \Mockery::mock('worker', $row);
            $worker->id = $row['id'];
            $worker->name = $row['name'];
            $worker->shouldReceive('workedWith')->withArgs([\Mockery::on(function ($value) use (&$customer) {
                return $value == $customer->id;
            })])->andReturnUsing(function () use (&$customer, $row) {
                return $customer->id == $row['worked_with_customer'];
            });
            $this->workers[$row['name']] = $worker;
            $groups[$row['group']][] = $worker;
        }
        foreach ($groups as $group => $workers) {
            $this->groups[$group]->shouldReceive('workers')->withNoArgs()->andReturn($workers);
        }
    }

    /**
     * @Given /^(Order|Customer) (does not have|has) autoinvite$/
     */
    public function autoinviteSet($entity, $has)
    {
        if ($entity == 'Order') {
            $this->order->shouldReceive('getAutoInvite')->once()->andReturn(false);
        }

        throw new PendingException();
    }

    /**
     * @When /^Admin invites (experienced|all|available) workers from group "([^"]+)" to order (\d+)$/
     */
    public function adminInvitesGroup($invitationType, $groupName, $id)
    {
        $groupId = $this->groups[$groupName]->id;
        $this->invitationService->invite($this->orders[$id], ['groupId' => $groupId, 'scope' => $invitationType]);
    }

    /**
     * @Then /^Worker "([^"]+)" (should|should not) receive notification for the order (\d+) to the group "([^"]+)"$/
     */
    public function shouldReceiveNotification($worker, $should, $orderId, $group)
    {
        $argument = both(hasEntry('order_id', $orderId))->
        andAlso(hasEntry('user_id', $this->workers[$worker]->id));
        if ($should) {
            $this->invitationRepository->shouldHaveReceived('create', [$argument]);
        } else {
            $this->invitationRepository->shouldNotHaveReceived('create', [$argument]);
        }
    }
}
