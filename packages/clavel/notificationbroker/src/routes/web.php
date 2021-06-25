<?php

// Module Plantillas
Route::group(array('namespace' => 'Clavel\NotificationBroker\Controllers\Plantillas'), function () {
    // Admin Module Routes
    Route::group(array('prefix' => 'admin', "as" => "admin.", 'middleware' => ['web']), function () {
        Route::post("plantillas/getData", 'AdminPlantillasController@getData');
        Route::resource('plantillas', 'AdminPlantillasController');
    });
});

// Module Log notificaciones
Route::group(array('namespace' => 'Clavel\NotificationBroker\Controllers\Notifications'), function () {
    // Admin Module Routes
    Route::group(array('prefix' => 'admin', "as" => "admin.", 'middleware' => ['web']), function () {
        Route::post("notifications-group/getData", 'AdminNotificationsGroupController@getData');
        Route::get("notifications-group/getFileInfo/{id}", 'AdminNotificationsGroupController@getFileInfo')
            ->where('id', '[0-9]+');
        Route::resource('notifications-group', 'AdminNotificationsGroupController');
    });
});

Route::group(array('namespace' => 'Clavel\NotificationBroker\Controllers\Notifications'), function () {
    // Admin Module Routes
    Route::group(array('prefix' => 'admin', "as" => "admin.", 'middleware' => ['web']), function () {
        Route::post("notifications/getData", 'AdminNotificationsController@getData');
        Route::get("notifications/getDetail/{id}", "AdminNotificationsController@getDetail");
        Route::resource('notifications', 'AdminNotificationsController');
        Route::get("notifications/viewCertificate/{id}", 'AdminNotificationsController@viewCertificate')
            ->where('id', '[0-9]+');
    });
});

// Modulo BlackList
Route::group(
    [
        'namespace' => 'Clavel\NotificationBroker\Controllers\Blacklists',
        'middleware' => ['web']
    ],
    function () {
        Route::group(array('prefix' => 'admin', "as" => "admin.", 'middleware' => ['web']), function () {
            Route::get('blacklists/state/{id}', 'AdminBlacklistsController@setChangeState')->where('id', '[0-9]+');
            Route::post("blacklists/list", 'AdminBlacklistsController@getData');
            Route::resource('blacklists', 'AdminBlacklistsController');
        });
    }
);

// Module Whatsapp
Route::group(array('namespace' => 'Clavel\NotificationBroker\Controllers\Whatsapp'), function () {
    Route::post('/whatsapp/twilio-whatsapp', 'WhatsappController@index');
});

// Bounce Types
Route::group(
    [
        'namespace' => 'Clavel\NotificationBroker\Controllers\BounceTypes',
        'middleware' => ['web']
    ],
    function () {
        Route::group(array('prefix' => 'admin', 'middleware' => ['web']), function () {
            Route::get("bouncetypes/export", 'AdminBounceTypesController@generateExcel');
            Route::get('bouncetypes/state/{id}', 'AdminBounceTypesController@setChangeState')->where('id', '[0-9]+');
            Route::post("bouncetypes/list", 'AdminBounceTypesController@getData');
            Route::resource('bouncetypes', 'AdminBounceTypesController');
        });
    }
);


// Bounced Emails
Route::group(
    [
        'namespace' => 'Clavel\NotificationBroker\Controllers\BouncedEmails',
        'middleware' => ['web']
    ],
    function () {
        Route::group(array('prefix' => 'admin', 'middleware' => ['web']), function () {
            Route::get("bouncedemails/export", 'AdminBouncedEmailsController@generateExcel');
            Route::get('bouncedemails/state/{id}', 'AdminBouncedEmailsController@setChangeState')
                ->where('id', '[0-9]+');
            Route::post("bouncedemails/list", 'AdminBouncedEmailsController@getData');
            Route::resource('bouncedemails', 'AdminBouncedEmailsController');
        });
    }
);
