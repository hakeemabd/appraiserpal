<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 12/22/15
 * Time: 6:18 PM
 */

namespace App\Http\Controllers\Customer;

use App\Events\UserCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\UserProfileRequest;
use App\Repositories\AttachmentRepository;
use App\Repositories\UserRepository;
use App\User;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository, AttachmentRepository $attachmentRepository)
    {
        $this->userRepository = $userRepository;
        $this->attachmentRepository = $attachmentRepository;
    }

    /**
     * Show the main page
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistration()
    {
        return view('auth.register');
    }

    public function processRegistration(RegistrationRequest $request)
    {
        $role = Sentinel::findRoleBySlug(env('ALLOWED_ROLE'));
        $user = Sentinel::register(array_merge(
                $request->only(['email', 'password', 'mobile_phone']),
                ['free_order_count' => User::FREE_ORDERS]
            )
        );
        $role->users()->attach($user);
        $activation = Activation::create($user);
        event(new UserCreated($user, $activation, $request->password));

        if (env('SYSTEM_SCOPE') == 'customer') {
            return Redirect::back()->with('status', 'User registered successfully');
        };

        return response()->json(['redirect' => route(env('SYSTEM_SCOPE') . ':landing')]);
    }

    public function processActivation($email, $code)
    {   
        $user = Sentinel::findByCredentials([
            'email' => $email
        ]);
        if (!$user) {
            return view('auth.activationFailure', ['error' => 'Could not find user with email ' . $email]);
        }

        if (Activation::complete($user, $code)) {
            Sentinel::login($user);
            //return view('auth.activated');
            return Redirect::to('/');
        } else {
            return view('auth.activationFailure', ['error' => 'Probably wrong link or activation has expired']);
        }
    }

    public function setAvailability($available)
    {
        $this->userRepository->setAvailable($available);
        return response()->json();
    }

    public function updatePersonalInfo(UserProfileRequest $request)
    {
        return response()->json(
            $this->userRepository->update($request->except(['user_id']), Sentinel::check()->id)
        );
    }

    public function profile()
    {
        $user = Sentinel::check();

        return view('profile.profile', [
            'user' => $user
        ]);
    }

    public function fileManager()
    {
        $uploadConfig = $this->attachmentRepository->getUploadConfig();
        $user = Sentinel::check();

        return view('profile.fileManager', compact('uploadConfig', 'user'));
    }

    public function updateStripeInfo(Request $request)
    {
        User::setStripeKey(env('STRIPE_API_SECRET'));

        return response()->json($this->userRepository->updateCustomer(Sentinel::check(), $request->get('token')));
    }
}