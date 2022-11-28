<?php

/**
 * Created by PhpStorm.
 * User: konst
 * Date: 12/22/15
 * Time: 8:36 PM
 */

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\RecoverRequest;
use App\Jobs\MailJob;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Comment;

class AuthController extends Controller
{

    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle posting of the form for logging the user in.
     *
     * @return \Illuminate\Http\Response
     */
    public function processLogin()
    {
        //  dd('sdf');
        try {
            $input = Input::all();
            $rules = [
                'email' => 'required|email',
                'password' => 'required',
            ];
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {
                return Response::json([
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ]);
            }
            $user = Sentinel::findUserByCredentials([
                'email' => Input::get('email')
            ]);
            if (!$user) {
                return Response::json([
                    'success' => false,
                    'errors' => ['email' => 'Does not exist']
                ]);
            }
            //            print_r($user);
            //            echo env('ALLOWED_ROLE');
            //            die;
            $allowedRole = env('ALLOWED_ROLE');
            if (env('ALLOWED_ROLE') == 'administrator') {
                if ($user->inRole('sub-admin')) {
                    $allowedRole = 'sub-admin';
                }

            }
            if (!$user->inRole($allowedRole)) {
                return Response::json([
                    'success' => false,
                    'errors' => 'You are not allowed to log in on this site'
                ]);
            }
            $remember = (bool)Input::get('remember', false);
            if (Sentinel::authenticate(Input::all(), $remember)) {
                $newComments = 0;
                if (Sentinel::check()->inRole('administrator') || Sentinel::check()->inRole('sub-admin')) {
                     $newComments = Comment::pending()->count();
                    //$newComments = 5;
                };

                if (Sentinel::check()->inRole('customer')) {
                    return Redirect::to('/dashboard');
                };
                return Response::json([
                    'success' => true,
                    'redirect' => Session::pull('url.intended', route(env('SYSTEM_SCOPE') . ':dashboard'))
                ])->withCookie(cookie('comments', $newComments));
            }
            $errors = 'Invalid login or password.';
        } catch (NotActivatedException $e) {
            $errors = 'Account is not activated!';
        } catch (ThrottlingException $e) {
            $delay = $e->getDelay();
            $errors = "Your account is blocked for {$delay} second(s).";
        }
        if (env('SYSTEM_SCOPE') == 'customer') {
            return Redirect::back()->withErrors([$errors]);
        };
        return Response::json([
            'success' => false,
            'errors' => $errors
        ]);
    }

    public function processLogout()
    {
        Sentinel::logout();
        return Redirect::to('/');
    }

    public function showRecoverPassword()
    {
        return view('auth.recoverPassword');
    }

    public function processRecoverPassword(RecoverRequest $request)
    {
        $user = Sentinel::findByCredentials(['email' => $request->email]);
        if (!$user->inRole(env('ALLOWED_ROLE'))) {
            return response()->json(['errors' => "You don't have access to this site"]);
        }
        $reminder = Reminder::create($user);
        $this->dispatch(new MailJob('emails.recoverPass', compact('user', 'reminder'), function ($m) use ($user) {
            if ($user->inRole('worker')) {
                $m->from(env('WORKER_MAIL_FROM_ADDRESS'), env('WORKER_MAIL_FROM_NAME'));
                $m->to($user->email)->subject('Appraiser Solutions password recovery');
            } else {
                $m->to($user->email)->subject('Appraiser Pal password recovery');
            };
        }));

        Session::flash('__msg', [
            'type' => 'success',
            'text' => 'Message for password recovery has been sent',
        ]);

        return response()->json(['success' => true, 'redirect' => '/']);
    }

    public function showChangePassword($email, $code)
    {
        return view('auth.changePassword', compact('email', 'code'));
    }

    public function processChangePassword(ChangePasswordRequest $request)
    {
        $user = Sentinel::findByCredentials([
            'email' => $request->email
        ]);
        if (!$user->inRole(env('ALLOWED_ROLE'))) {
            return response()->json(['errors' => "You don't have access to this site"]);
        }
        if (Reminder::complete($user, $request->code, $request->password)) {
            if (Activation::exists($user)) {
                return redirect('/');
            }
            Sentinel::login($user);
            return Response::json(['success' => true, 'redirect' => route(env('SYSTEM_SCOPE') . ':dashboard')]);
        } else {
            return response()->json(['success' => false, 'errors' => 'Reminder code not found']);
        }
    }
}
