<?php

namespace App\Http\Middleware;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Closure;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class UserOrder
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Sentinel::getUser();
        if ($user->inRole('administrator') || $user->inRole('sub-admin')) {
            if (!$request->has('user_id')) {

                return response()->json([
                    'success' => false,
                    'message' => '"user_id" field is required',
                ], SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        } elseif ($user->inRole('customer')) {
            $request['user_id'] = $user->id;
        }
        return $next($request);
    }
}
