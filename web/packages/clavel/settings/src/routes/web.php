<?php

// Module Settings
Route::group(
    [
        'module' => 'Settings',
        'middleware' => ['web'],
        'namespace' => 'App\Modules\Settings\Controllers'
    ],
    function () {
        Route::group(array('prefix' => 'admin'), function () {
            Route::get("settings", 'AdminSettingsController@edit')->name('settings');
            Route::post("settings", 'AdminSettingsController@update');
        });
    }
);
