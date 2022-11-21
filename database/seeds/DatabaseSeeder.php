<?php

use App\Events\Order\Created;
use App\Models\Transaction;
use App\Repositories\OrderRepository;
use App\Repositories\TransactionRepository;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $users = factory(App\User::class, 2)->make();
        $role = Sentinel::findRoleBySlug('customer');
        $workerRole = Sentinel::findRoleBySlug('worker');

        $faker = app(Faker\Generator::class);

        $orderRepository = app(OrderRepository::class);

        $groupNames = ['Intake', 'Adjuster', 'Review'];
        $sort = 1;
        echo "Groups:\n";
        foreach ($groupNames as $groupName) {
            $group = new \App\Models\WorkerGroup();
            $group->name = $groupName;
            $group->sort = $sort;
            $sort++;
            $group->save();
            $workers = factory(App\User::class, 2, 4)->make();
            echo "$group->name:\n";
            foreach ($workers as $worker) {
                $w = Sentinel::registerAndActivate(array_merge(array_except($worker->toArray(), ['fullName']), ['password' => 1234]));
                $workerRole->users()->attach($w);
                echo "$w->email\n";
                $group->workers()->attach($w, [
                    'fee' => rand(10, 20),
                    'first_turnaround' => rand(4 * 60, 8 * 60),
                    'next_turnaround' => rand(30, 60),
                ]);
            }
        }

        echo "Customers: \n";
        foreach ($users as $user) {
            /**
             * @var App\User
             */
            $u = Sentinel::registerAndActivate(array_merge(array_except($user->toArray(), ['fullName']), ['password' => 1234]));
            $role->users()->attach($u);
            echo "$u->email\n";
            $orders = factory(App\Models\Order::class, 3)->make(['user_id' => $u->id]);
            foreach ($orders as $order) {
                $order->save();
                $transaction = app(TransactionRepository::class);
                $transaction->create([
                    'order_id' => $order->id,
                    'status' => Transaction::STATUS_PAID,
                    'amount' => $order->price,
                    'is_free' => 0,
                ]);

                event(new Created($order));
            }
        }

        Model::reguard();
    }
}
