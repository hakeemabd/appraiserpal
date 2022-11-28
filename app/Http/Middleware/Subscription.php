<?php

namespace App\Http\Middleware;

use App\Repositories\UserRepository;
use Closure;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class Subscription
{

    private $userRepository;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function handle($request, Closure $next)
    {
        $user = Sentinel::getUser();
//        if ($user->inRole('administrator')) {
//            if (!$request->has('user_id')) {
//                return response()->json([
//                    'success' => false,
//                    'message' => '"user_id" field is required',
//                ], SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY);
//            }
//        }
//        $userRepo = new UserRepository();
//        || !$user->hasFreeOrders()
//        echo $this->userRepository->inTrial($user);
//        die;
        if ($user->inRole('customer')) {
//            if ($this->userRepository->inTrial($user) && !$user->hasFreeOrders()) {
//                return redirect(url('/subscription-plans'));
//            }
            // if (!$this->userRepository->inTrial($user) && !$user->subscribed()) {
            //     return redirect(url('/subscription-plans'));
            // } elseif (!$this->userRepository->inTrial($user) && $user->subscribed() && (empty($user->stripe_subscription) || empty($user->stripe_plan))) {
            //     return redirect(url('/subscription-plans'));
            // }
        }
        return $next($request);
    }
}
