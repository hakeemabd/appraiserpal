<?php

return function () {
    Route::get('/', ['as' => 'landing', 'uses' => function () {
        return view('welcome');
    }]);

    Route::get('register', ['as' => 'registration', 'uses' => 'Customer\UserController@showRegistration']);
    Route::post('register', ['as' => 'register', 'uses' => 'Customer\UserController@processRegistration']);

    Route::get('confirm/{email}/{code}', ['as' => 'confirmSignup', 'uses' => 'Customer\UserController@processActivation']);

    Route::get('login', ['as' => 'login', 'uses' => 'Auth\AuthController@showLogin']);
    Route::post('login', ['as' => 'login', 'uses' => 'Auth\AuthController@processLogin']);
    Route::get('logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@processLogout']);

    Route::get('recover', ['as' => 'recoverPasswordForm', 'uses' => 'Auth\AuthController@showRecoverPassword']);
    Route::post('recover', ['as' => 'recoverPassword', 'uses' => 'Auth\AuthController@processRecoverPassword']);

    Route::get('changePassword/{email}/{code}', ['as' => 'changePasswordForm', 'uses' => 'Auth\AuthController@showChangePassword']);
    Route::post('changePassword', ['as' => 'changePassword', 'uses' => 'Auth\AuthController@processChangePassword']);

    Route::group(['before' => 'worker'], function () {
        Route::get('dashboard', [
            'as' => 'dashboard',
            'uses' => 'WorkerDashboardController@index',
        ]);
        Route::get('messages', [
            'as' => 'messages',
            'uses' => 'WorkerMessageController@index',
        ]);

        Route::get('availability/{available}', [
            'as' => 'availability',
            'uses' => 'Customer\UserController@setAvailability',
        ]);

        Route::get('invitation/{code}/accept', [
            'as' => 'invitation.accept',
            'uses' => 'Order\AssignmentController@acceptInvitation',
        ]);
        Route::get('invitation/{code}/reject', [
            'as' => 'invitation.reject',
            'uses' => 'Order\AssignmentController@rejectInvitation',
        ]);
        Route::get('invitation/{orderId}/{groupId}/finish', [
            'as' => 'invitation.finish',
            'uses' => 'Order\AssignmentController@finishOrder',
        ]);
        Route::post('invitation/{orderId}/reworking', [
            'as' => 'invitation.reworking',
            'uses' => 'Order\AssignmentController@reworkingOrder',
        ]);
        Route::get('invitation/{orderId}/{groupId}/complete', [
            'as' => 'invitation.complete',
            'uses' => 'Order\AssignmentController@completeOrder',
        ]);

        Route::resource('order', 'Order\TrackingController');
        Route::get('getOrders', [
            'as' => 'order.data',
            'uses' => 'Order\TrackingController@getWorkerOrders',
        ]);

        Route::get('documents/files/{orderId}', [
            'as' => 'documents.files',
            'uses' => 'DocumentsController@getFiles',
        ]);


        Route::get('documents/files/{orderId}', [
            'as' => 'documents.files',
            'uses' => 'DocumentsController@getFiles',
        ]);

        Route::get('documents/uploaded-files/{orderId}', [
            'as' => 'documents.uploaded.files',
            'uses' => 'DocumentsController@getUploadedFiles',
        ]);

        Route::get('attachment/{attachment}/mark/{isFinal}', [
            'as' => 'attachment.mark',
            'uses' => 'AttachmentController@mark',
        ]);

        Route::delete('attachment/{attachment}', [
            'as' => "attachment.destroy",
            'uses' => 'AttachmentController@destroy'
        ]);

        Route::post('attachment/{type}', 'AttachmentController@create');

        Route::get('comments', [
            'as' => 'comments',
            'uses' => 'CommentController@getComments',
        ]);

        Route::post('comments', [
            'as' => 'comments',
            'uses' => 'CommentController@createComment',
        ]);
    });
};