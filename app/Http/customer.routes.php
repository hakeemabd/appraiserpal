<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
return function () {
    Route::get('/', ['as' => 'landing', 'uses' => 'LandingController@showLanding']);

    Route::get('/about-us', ['uses' => 'Customer\PageController@getAboutUsPage']);
    Route::get('/faq', ['uses' => 'Customer\PageController@getFaqPage']);

    Route::get('/{page}.html', ['as' => 'page', 'uses' => 'Customer\PageController@show'])->where('page', 'terms|privacy');

    Route::get('subscription-plans', [
        'as' => 'subscription-plans',
        'uses' => 'Customer\SubscriptionController@index',
    ]);

    Route::post('subscribe', 'Customer\SubscriptionController@subscribe');

    // Route::get('/dashboard', 'ManageController@index');


    Route::group([
        'before' => 'customer',
        'middleware' => ['user.subscription']
    ], function () {
        Route::get('dashboard', [
            'as' => 'dashboard',
            'uses' => 'Order\ManageController@index',
        ]);
        Route::get('messages', [
            'as' => 'messages',
            'uses' => 'CustomerMessageController@index',
        ]);
        Route::post('messages.store', [
            'as' => 'messages.store',
            'uses' => 'CustomerMessageController@store',
        ]);
        
        Route::get('order/create', [
            'as' => 'createOrder',
            'uses' => 'Order\ManageController@create',
        ]);
        Route::get('order/getData', [
            'as' => 'getOrdersData',
            'uses' => 'Order\ManageController@getOrdersData',
        ]);
        Route::get('order/{order}/show', [
            'as' => 'order.show',
            'uses' => 'Order\ManageController@show',
        ]);
        Route::get('order/{order}/edit', [
            'as' => 'order.edit',
            'uses' => 'Order\ManageController@edit',
        ]);
        Route::get('profile', [
            'as' => 'profile',
            'uses' => 'Customer\UserController@profile'
        ]);
        Route::get('profile/edit', [
            'as' => 'profileEdit',
            'uses' => 'Customer\UserController@fileManager',
        ]);
        Route::get('documents/final-files/{orderId}', [
            'as' => 'documents.final.files',
            'uses' => 'DocumentsController@getFinalFiles',
        ]);
        Route::post('user/billing', 'Customer\UserController@updateStripeInfo');

        Route::get('comments', [
            'as' => 'comments',
            'uses' => 'CommentController@getComments',
        ]);

        Route::post('comments', [
            'as' => 'comments',
            'uses' => 'CommentController@createComment',
        ]);

        Route::post('invitation/reworking', [
            'as' => 'invitation.reworking',
            'uses' => 'Order\AssignmentController@reworkingOrderByCustomer',
        ]);

        Route::get('order/{orderId}/complete', [
            'as' => 'order.complete', 'uses' => 'Order\TrackingController@deliver',
        ]);

        Route::post('attachment/{type}', 'AttachmentController@create');

        Route::get('download/{orderId}', [
            'as' => 'download',
            'uses' => 'DocumentsController@downloadDeliverables',
        ]);
    });

// api
    Route::group([
        'prefix' => 'api',
        'before' => 'customer',
        'middleware' => ['user.order', 'user.subscription'],
    ], function () {
        Route::post('order', 'Order\WizardController@createOrder');

        Route::post('confirm-order', 'Order\ManageController@confirmOrder');

        Route::put('order/{order}', 'Order\WizardController@saveOrder');
        Route::get('order/{id}', 'Order\WizardController@show');

        Route::get('software', 'SoftwareController@index');

        Route::get('attachment/{type}', 'AttachmentController@getUserFiles');

        Route::get('label', 'LabelController@index');
        Route::post('label', 'LabelController@create');
        Route::put('label/{id}', 'LabelController@update');
        Route::delete('label/{id}', 'LabelController@destroy');

        Route::get('report-types', 'ReportTypeController@index');

        Route::delete('attachment/{attachment}', 'AttachmentController@destroy');

        Route::post('stripe/{order}', 'Payment\StripeController@charge');

        Route::get('paypal/{order}', 'Payment\PaypalController@index');
        // this is after make the payment, PayPal redirect back to your site
        Route::get('paypal/status/{transaction}', 'Payment\PaypalController@getPaymentStatus');

        Route::get('promo-code/{transaction}/code/{promoCode}', 'PromoCodeController@applyCode');

        Route::post('user', ['as' => 'updateUser', 'uses' => 'Customer\UserController@updatePersonalInfo']);

        //file manager
        Route::post('attachment/{type}', 'AttachmentController@create');

    });

    Route::post('login', ['as' => 'login', 'uses' => 'Auth\AuthController@processLogin']);
    Route::get('logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@processLogout']);

    Route::get('recover', ['as' => 'recoverPasswordForm', 'uses' => 'Auth\AuthController@showRecoverPassword']);
    Route::post('recover', ['as' => 'recoverPassword', 'uses' => 'Auth\AuthController@processRecoverPassword']);

    Route::get('changePassword/{email}/{code}', ['as' => 'changePasswordForm', 'uses' => 'Auth\AuthController@showChangePassword']);
    Route::post('changePassword', ['as' => 'changePassword', 'uses' => 'Auth\AuthController@processChangePassword']);


    Route::get('register', ['as' => 'registration', 'uses' => 'Customer\UserController@showRegistration']);
    Route::post('register', ['as' => 'register', 'uses' => 'Customer\UserController@processRegistration']);

    Route::get('confirm/{email}/{code}', ['as' => 'confirmSignup', 'uses' => 'Customer\UserController@processActivation']);
};
