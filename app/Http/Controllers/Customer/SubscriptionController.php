<?php

namespace App\Http\Controllers\Customer;

use App\Repositories\AttachmentRepository;
use App\Repositories\UserRepository;
use App\User;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

//use ;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(Redirector $redirector, UserRepository $userRepository, AttachmentRepository $attachmentRepository)
    {
        $this->userRepository = $userRepository;
        $user = $this->userRepository->find(Sentinel::getUser()->id);
        if ($this->userRepository->inTrial($user) || $user->subscribed()) {
            if ((!empty($user->stripe_subscription) && !empty($user->stripe_plan)))
                $redirector->to('/dashboard')->send();
        };
    }

    public function index()
    {
        $return_array['is_subsription'] = true;
        $return_array['user'] = User::find(Sentinel::getUser()->id);
        $return_array['trial'] = $this->userRepository->inTrial($return_array['user']);
        return view('subscription.index')->with($return_array);
    }

    public function subscribe(Request $request)
    {
        $user = User::find(Sentinel::getUser()->id);
        $this->validate($request, [
            'plan_id' => 'required',
            'plan_name' => 'required',
        ]);
        if (($user->subscribed() && !$request->use_existing_card) || (!$user->subscribed())) {
            $this->validate($request, [
                'stripeToken' => 'required'
            ]);
        }
        try {
            if ($user->subscribed() && !$request->use_existing_card) {
                $user->subscription()->cancel(false);
                $user->subscription($request->plan_id)->create($request->stripeToken, [
                    'email' => $user->email
                ]);
            } elseif ($user->subscribed() && $request->use_existing_card) {
                $user->subscription($request->plan_id)->swap();
            } else {
                $user->subscription($request->plan_id)->create($request->stripeToken, [
                    'email' => $user->email
                ]);
            }
//            if (!$user->is_free_trial_used) {
//                $user->is_free_trial_used = 1;
//                $user->trial_ends_at = Carbon::now()->addDays(7);
//                $user->save();
//            }
            if ($user->subscribed()) {
                return redirect(url('/dashboard'))->with('success', 'You have successfully subscribed to ' . $request->plan_name);
            } else {
                dd('not subscribed please try again shortly');
            }
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
