<?php

use Illuminate\Support\Facades\Route;

// Modulo Pais
Route::group(
    [
        'namespace' => 'App\Modules\Pais\Controllers',
        'middleware' => ['web']
    ],
    function () {
        Route::group(array('prefix' => 'admin', 'middleware' => ['web']), function () {
            Route::get("pais/export", 'AdminPaisController@generateExcel');
            Route::get('pais/state/{id}', 'AdminPaisController@setChangeState')->where('id', '[0-9]+');
            Route::post("pais/list", 'AdminPaisController@getData');
            Route::post("pais/delete-selected", 'AdminPaisController@destroySelected');
            Route::get("pais/viewimage/{image}", 'AdminPaisController@viewImage');
            Route::resource('pais', 'AdminPaisController');
        });
    }
);
