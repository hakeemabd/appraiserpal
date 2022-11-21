<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

Route::filter('customer', function () {
    if (Sentinel::guest() || !Sentinel::getUser()->inRole('customer')) {
        if (Request::ajax()) {
            return Response::make('Unauthorized', SymfonyResponse::HTTP_UNAUTHORIZED);
        } else {
            Session::flash('__msg', [
                'type' => 'error',
                'text' => 'You are not logged in. Please log in by clicking "log in".',
            ]);
            return Redirect::guest(route('customer:landing'));
        }
    }
});

Route::filter('worker', function () {
    if (Sentinel::guest() || !Sentinel::getUser()->inRole('worker')) {
        if (Request::ajax()) {
            return Response::make('Unauthorized', SymfonyResponse::HTTP_UNAUTHORIZED);
        } else {
            return Redirect::guest(route('worker:login'));
        }
    }
});

Route::filter('administrator', function () {
    if (Sentinel::guest() || (!Sentinel::getUser()->inRole('administrator') && !Sentinel::getUser()->inRole('sub-admin'))) {
        if (Request::ajax()) {
            return Response::make('Unauthorized', SymfonyResponse::HTTP_UNAUTHORIZED);
        } else {
            return Redirect::guest(route('admin:login'));
        }
    }
}); 

Route::group([
    "domain" => env('CUSTOMER_HOST'),
    "as" => 'customer:'
], include(__DIR__ . '/customer.routes.php'));

Route::group([
    "domain" => env('ADMIN_HOST'),
    "as" => 'admin:'
], include(__DIR__ . '/admin.routes.php'));

Route::group([
    "domain" => env('WORKER_HOST'),
    "as" => 'worker:'
], include(__DIR__ . '/worker.routes.php'));