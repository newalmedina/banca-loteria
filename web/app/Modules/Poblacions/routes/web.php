<?php

use Illuminate\Support\Facades\Route;

// Modulo Poblacion
Route::group(
    [
        'namespace' => 'App\Modules\Poblacions\Controllers',
        'middleware' => ['web']
    ],
    function () {
        Route::group(array('prefix' => 'admin', 'middleware' => ['web']), function () {
            Route::get("poblacions/export", 'AdminPoblacionsController@generateExcel');
            Route::get('poblacions/state/{id}', 'AdminPoblacionsController@setChangeState')->where('id', '[0-9]+');
            Route::post("poblacions/list", 'AdminPoblacionsController@getData');
            Route::post("poblacions/delete-selected", 'AdminPoblacionsController@destroySelected');
            Route::get("poblacions/viewimage/{image}", 'AdminPoblacionsController@viewImage');
            Route::resource('poblacions', 'AdminPoblacionsController');
        });
    }
);
