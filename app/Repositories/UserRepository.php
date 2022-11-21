<?php

namespace App\Repositories;

use App\Exceptions\SaveException;
use App\Exceptions\UserDeleteException;
use App\Models\Order;
use App\User;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\StripeGateway;

class UserRepository extends BaseRepository
{
    public function model()
    {
        return User::class;
    }

    public function curlReq($url, $api_secret)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Authorization: Bearer " . $api_secret,
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Host: api.stripe.com",
                "User-Agent: PostmanRuntime/7.15.2",
                "cache-control: no-cache"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
//            echo "cURL Error #:" . $err;
            return false;
        } else {
            return $response;
        }
    }

    public function getStripeSubscriptionDetails($subscriptionId, $api_secret)
    {
        return $this->curlReq("https://api.stripe.com/v1/subscriptions/" . $subscriptionId, $api_secret);
    }

    public function inTrial($user)
    {
        $date = Carbon::parse($user->created_at);
        $now = Carbon::now();
        $diff = $date->diffInDays($now);
        if ($diff <= User::TRIAL_PERIOD) {
            return true;
        }
        return false;
    }

    public function chargeSubscription(User $user, $request)
    {
        $res = $user->newSubscription($request->plan_name, $request->plan_id)->create($request->stripeToken);
        return $res;
    }

    public function charge(User $user, $amount, $customerToken)
    {
        $chargeData = $user->charge($amount * 100, [
            'currency' => 'usd',
            'customer' => $customerToken,
        ]);
        if ($chargeData['paid'] && $chargeData['status'] == 'succeeded') {
            return true;
        }
        return false;
    }

    public function linkToStripe(User $user, $userToken = false)
    {
        if ($user->hasStripeToken()) {
            if ($userToken) {
                $this->updateCustomer($user, $userToken);
            }
            return $user->stripe_id;
        }
        $userData = $this->createCustomer($userToken);
//        $user->stripe_active = 1;
        $user->stripe_id = $userData['id'];
        $user->last_four = $userData['sources']['data'][0]['last4'];
        $user->card_type = $userData['sources']['data'][0]['brand'];
        $user->save();
        return $user->stripe_id;

    }

    public function createCustomer($token, array $properties = [])
    {
        return (new StripeGateway($this->model))->createStripeCustomer($token, $properties);
    }

    public function admins()
    {
        return Sentinel::findRoleBySlug('administrator')->users();
    }

    public function workers()
    {
        return Sentinel::findRoleBySlug('worker')->users();
    }

    public function setAvailable($available)
    {
        $user = Sentinel::check();
        $user->available = $available;
        $user->save();
    }

    public function updateCustomer(User $user, $token)
    {
        $customer = $this->retrieve($user->stripe_id);
        $customer->source = $token;
        $customer->save();

        return $this->update([
            'last_four' => $customer->sources->data[0]->last4,
            'card_type' => $customer->sources->data[0]->brand,
        ], $user->id);
    }

    public function retrieve($id)
    {
        return (new StripeGateway($this->model))->getStripeCustomer($id);
    }

    public function update(array $data, $id)
    {
        $data = array_diff($data, [null]);
        $model = $this->model->find($id);
        $model = $model->fill($data);
        if (!$model->save()) {
            throw new SaveException('User is not updated');
        }

        return $model;
    }

    public function delete($user)
    {
        if (is_numeric($user)) {
            $user = User::find($user);
            if (!$user) {
                throw new ModelNotFoundException();
            }
        }
        if ($user->inRole('customer')) {
            $this->deleteCustomer($user);
        } elseif ($user->inRole('worker')) {
            $this->deleteWorker($user);
        } else {
            $this->deleteAdmin($user);
        }
    }

    protected function deleteCustomer(User $user)
    {
        if ($user->orders()
            ->whereNotIn('status', [
                Order::STATUS_ARCHIVED,
                Order::STATUS_CANCELED,
                Order::STATUS_ACCEPTED])
            ->where('completed', 1)
            ->exists()
        ) {
            throw new UserDeleteException("User has orders in progress. Cancel or unassign user in order to delete");
        }
        $user->delete();
    }

    protected function deleteWorker(User $user)
    {
        $assignment = DB::table('order_assignments')
            ->join('orders', 'orders.id', '=', 'order_assignments.order_id')
            ->whereNotIn('orders.status', [Order::STATUS_ARCHIVED, Order::STATUS_CANCELED, Order::STATUS_ACCEPTED])
            ->where('orders.completed', 1)
            ->where('order_assignments.user_id', $user->id)->first();
        if ($assignment) {
            throw new UserDeleteException("User has orders in progress. Cancel or unassign user in order to delete");
        }
        $user->delete();
    }

    protected function deleteAdmin(User $user)
    {
        $user->delete();
    }

}