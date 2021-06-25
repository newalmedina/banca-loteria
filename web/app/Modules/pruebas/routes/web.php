<?php

use Illuminate\Support\Facades\Route;

// Modulo prueba
Route::group(
    [
        'namespace' => 'App\Modules\pruebas\Controllers',
        'middleware' => ['web']
    ],
    function () {
        Route::group(array('prefix' => 'admin', 'middleware' => ['web']), function () {
            Route::get("pruebas/export", 'AdminpruebasController@generateExcel');
            Route::get('pruebas/state/{id}', 'AdminpruebasController@setChangeState')->where('id', '[0-9]+');
            Route::post("pruebas/list", 'AdminpruebasController@getData');
            Route::post("pruebas/delete-selected", 'AdminpruebasController@destroySelected');
            Route::get("pruebas/viewimage/{image}", 'AdminpruebasController@viewImage');
            Route::resource('pruebas', 'AdminpruebasController');

        });
    }
);

