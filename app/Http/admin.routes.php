<?php

return function () {

    Route::get('login', ['as' => 'login', 'uses' => 'Auth\AuthController@showLogin']);
    Route::post('login', ['as' => 'login', 'uses' => 'Auth\AuthController@processLogin']);
    Route::get('logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@processLogout']);

    Route::get('recover', ['as' => 'recoverPasswordForm', 'uses' => 'Auth\AuthController@showRecoverPassword']);
    Route::post('recover', ['as' => 'recoverPassword', 'uses' => 'Auth\AuthController@processRecoverPassword']);

    Route::get('changePassword/{email}/{code}', [
        'as' => 'changePasswordForm',
        'uses' => 'Auth\AuthController@showChangePassword',
    ]);
    Route::post('changePassword', [
        'as' => 'changePassword',
        'uses' => 'Auth\AuthController@processChangePassword',
    ]);

    Route::group(['before' => 'administrator'], function () {

        Route::get('/', [
            'as' => 'dashboard',
            'uses' => 'AdminDashboardController@index',
        ]);

        Route::get('/pages', [
            'as' => 'pages',
            'uses' => 'Admin\PagesController@index',
        ]);

        Route::get('/pages/add', [
            'as' => 'pages.addnew',
            'uses' => 'Admin\PagesController@create',
        ]);

        Route::post('pages/update/{id?}', [
            'as' => 'pages.store',
            'uses' => 'Admin\PagesController@store',
        ]);

        Route::get('pages/data', [
            'as' => 'pages.data',
            'uses' => 'Admin\PagesController@getPages',
        ]);

        Route::get('pages/{id}/cancel', [
            'as' => 'pages.destroy',
            'uses' => 'Admin\PagesController@destroy'
        ]);

        Route::get('pages/{id}/edit', [
            'as' => 'pages.edit',
            'uses' => 'Admin\PagesController@show'
        ]);

        Route::get('/user/role/{role?}', [
            'as' => 'usersList',
            'uses' => 'Admin\CrudController@index',
        ]);
        Route::get('/user/create/{role?}', [
            'as' => 'userCreate',
            'uses' => 'Admin\CrudController@create',
        ]);
        Route::resource('user', 'Admin\CrudController');
        Route::get('getUsers/{role?}', 'Admin\CrudController@getUsers');

        Route::get('workerGroup/data', [
            'as' => 'workerGroup.data',
            'uses' => 'WorkerGroupController@getGroups',
        ]);
        Route::resource('workerGroup', 'WorkerGroupController');

        Route::get('workerGroup/{group}/assignedUsers', [
            'as' => 'workerGroup.assigned',
            'uses' => 'WorkerGroupController@assignedUsers',
        ]);
        Route::get('workerGroup/{group}/assignedUsersData', [
            'as' => 'workerGroup.assignedData',
            'uses' => 'WorkerGroupController@assignedUsersData',
        ]);
        Route::post('workerGroup/{group}/assign', [
            'as' => 'workerGroup.assign',
            'uses' => 'WorkerGroupController@assign',
        ]);
        Route::put('workerGroup/{group}/unassign/{user}', [
            'as' => 'workerGroup.unassign',
            'uses' => 'WorkerGroupController@unassign',
        ]);

        Route::get('/user/create/{user_id?}', [
            'as' => 'orderCreate',
            'uses' => 'Order\TrackingController@create',
        ]);
        Route::resource('order', 'Order\TrackingController');
        Route::get('getOrders', [
            'as' => 'order.data',
            'uses' => 'Order\TrackingController@getOrders'
        ]);
        Route::get('order/{order}/cancel', [
            'as' => 'order.cancel',
            'uses' => 'Order\TrackingController@cancel'
        ]);

        Route::get('order/{order}/assignments', [
            'as' => 'order.assignments.index',
            'uses' => 'Order\AssignmentController@index',
        ]);
        Route::get('order/{orderId}/assignmentsData/{groupId}', [
            'as' => 'order.assignments.data',
            'uses' => 'Order\AssignmentController@getAssignments',
        ]);
        Route::get('order/{orderId}/invite/{groupId}/user/{userId}', [
            'as' => 'order.invite',
            'uses' => 'Order\AssignmentController@invite',
        ]);
        Route::get('order/invite/cancel/{code}', [
            'as' => 'order.invite.cancel',
            'uses' => 'Order\AssignmentController@cancelInvite',
        ]);
        Route::get('order/{orderId}/unassign/{groupId}/user/{userId}', [
            'as' => 'order.unassign', 'uses' => 'Order\AssignmentController@unassign',
        ]);
        Route::post('order/{orderId}/addTime', [
            'as' => 'order.add.time', 'uses' => 'Order\AssignmentController@addTime',
        ]);
        Route::post('order/{orderId}/addAdditionalWorkerTime', [
            'as' => 'order.add.worker.time', 'uses' => 'Order\AssignmentController@addAdditionalWorkerTime',
        ]);
        Route::get('order/{orderId}/complete', [
            'as' => 'order.complete', 'uses' => 'Order\TrackingController@completeOrder',
        ]);


        Route::get('getPromoCodes', [
            'as' => 'promoCode.view',
            'uses' => 'PromoCodeController@getPromoCodes'
        ]);
        Route::get('promo-code/{id}', [ // @todo Route::delete
            'as' => 'promoCode.delete',
            'uses' => 'PromoCodeController@destroy',
        ]);
        Route::get('promo-code/{promoCodeById}/edit', [
            'as' => 'promoCode.update',
            'uses' => 'PromoCodeController@showForm',
        ]);
        Route::post('promo-code/update/{id?}', [
            'as' => 'promoCode.store',
            'uses' => 'PromoCodeController@store',
        ]);
        Route::get('promo-code/create/create', [ //@todo  refactor
            'as' => 'promoCode',
            'uses' => 'PromoCodeController@showForm',
        ]);

        Route::get('report-types', [
            'as' => 'reportType.index',
            'uses' => 'ReportTypeController@view',
        ]);
        Route::get('getReportTypes', [
            'as' => 'reportType.view',
            'uses' => 'ReportTypeController@getReportTypes'
        ]);
        Route::get('report_type/{id}', [ // @todo Route::delete
            'as' => 'reportType.delete',
            'uses' => 'ReportTypeController@destroy',
        ]);
        Route::get('report_type/{reportType}/edit', [
            'as' => 'reportType.update',
            'uses' => 'ReportTypeController@showForm',
        ]);
        Route::post('report_type/update/{id?}', [
            'as' => 'reportType.store',
            'uses' => 'ReportTypeController@store',
        ]);
        Route::get('/reportType/create', [
            'as' => 'reportTypeCreate',
            'uses' => 'ReportTypeController@showForm',
        ]);

        Route::get('documents/files/{orderId}', [
            'as' => 'documents.files',
            'uses' => 'DocumentsController@getFiles',
        ]);

        Route::get('documents/uploaded-files/{orderId}', [
            'as' => 'documents.uploaded.files',
            'uses' => 'DocumentsController@getUploadedFiles',
        ]);

        Route::get('documents/final-files/{orderId}', [
            'as' => 'documents.final.files',
            'uses' => 'DocumentsController@getFinalFiles',
        ]);

        Route::get('attachment/{attachment}/mark/{isFinal}', [
            'as' => 'attachment.mark',
            'uses' => 'AttachmentController@mark',
        ]);

        Route::get('attachment/{attachment}/approve/{isApproved}', [
            'as' => 'attachment.approve',
            'uses' => 'AttachmentController@approve',
        ]);

        Route::delete('attachment/{attachment}', [
            'as' => "attachment.destroy",
            'uses' => 'AttachmentController@destroy'
        ]);

        Route::post('attachment/{type}', 'AttachmentController@create');

        Route::get('settings', [
            'as' => "settings",
            'uses' => 'SettingsController@index'
        ]);
        Route::post('settings', 'SettingsController@update');

        Route::get('getPendings', [
            'as' => "comment.pending",
            'uses' => 'CommentController@getPending'
        ]);

        Route::put('comment/{comment_id}/approve/{by}', [
            'as' => 'comment.approve',
            'uses' => 'CommentController@approve'
        ]);

        Route::get('comment/{comment_id}/edit', [
            'as' => 'comment.edit',
            'uses' => 'CommentController@edit'
        ]);

        Route::delete('comment/{comment_id}/delete', [
            'as' => 'comment.delete',
            'uses' => 'CommentController@delete'
        ]);

        Route::put('comment/{comment_id}/update', [
            'as' => 'comment.update',
            'uses' => 'CommentController@update'
        ]);

        Route::put('comment/{comment_id}', 'CommentController@update');

        Route::get('comments', [
            'as' => 'comments',
            'uses' => 'CommentController@getComments',
        ]);

        Route::post('comments', [
            'as' => 'comments',
            'uses' => 'CommentController@createComment',
        ]);

        Route::resource('comment', 'CommentController');

        Route::get('order/{order}/history', [
            'as' => 'order.history',
            'uses' => 'Order\TrackingController@orderhistory',
        ]);

        Route::get('history/{order}', [
            'as' => 'history',
            'uses' => 'Order\TrackingController@history',
        ]);

        Route::get('promo-codes', [
            'as' => 'promoCode.index',
            'uses' => 'PromoCodeController@view',
        ]);
        Route::get('transactions/customer', [
            'as' => 'transactions.customer',
            'uses' => function () {
                if (!empty(\Sentinel::check()) && \Sentinel::check()->inRole('administrator')) {
                    return view('payments.customer');
                }
                return redirect(url('/'));
            }
        ]);

        Route::get('transactions/{status}', [
            'as' => 'transactions',
            'uses' => 'Payment\PaypalController@getPayments'
        ]);

        Route::get('payments/complete', [
            'as' => 'payments.complete',
            'uses' => function () {
                if (!empty(\Sentinel::check()) && \Sentinel::check()->inRole('administrator')) {
                    return view('payments.complete');
                }
                return redirect(url('/'));
            }
        ]);

        Route::get('payments/due', [
            'as' => 'payments.due',
            'uses' => function () {
                if (!empty(\Sentinel::check()) && \Sentinel::check()->inRole('administrator')) {
                    return view('payments.due');
                }
                return redirect(url('/'));
            }
        ]);

        Route::get('payments/{status}/list', [
            'as' => 'payments',
            'uses' => 'Payment\PaymentController@getPayments'
        ]);

        Route::put('payments/{payment_id}/update/{status}', [
            'as' => 'payments.update',
            'uses' => 'Payment\PaymentController@updatePayment'
        ]);

        Route::post('invitation/{orderId}/reworking', [
            'as' => 'invitation.reworking',
            'uses' => 'Order\AssignmentController@reworkingOrder',
        ]);
    });
};
