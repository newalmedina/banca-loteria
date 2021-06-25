<?php
use Illuminate\Http\Request;

/* Version 1 */
Route::group(['prefix' => 'api/v1'], function () {

    /* Notifications */
    Route::group([
        'namespace' => 'Clavel\NotificationBroker\Controllers',
        'prefix' => 'notifications'
    ], function () {
        Route::get('trackonline/{guid}', 'Api\TrackController@trackonline');


        Route::group(['middleware' => 'auth:api'], function () {
            Route::post('email/sync', 'Api\NotificationController@storeEmail');
            Route::post('email', 'Api\NotificationController@storeEmailQueue');

            Route::post('email/status', 'Api\NotificationController@showEmailStatus');
            Route::get('email/{guid}', 'Api\NotificationController@showEmail');

            Route::post('sms/sync', 'Api\NotificationController@storeSms');
            Route::post('sms', 'Api\NotificationController@storeSmsQueue');
            Route::get('sms/{guid}', 'Api\NotificationController@showSms');
            Route::post('sms/status', 'Api\NotificationController@showSmsStatus');
        });
    });
});
