<?php

use Illuminate\Support\Facades\Route;

// Modulo Idioma
Route::group(
    [
        'namespace' => 'App\Modules\Idiomas\Controllers',
        'middleware' => ['web']
    ],
    function () {
        Route::group(array('prefix' => 'admin', 'middleware' => ['web']), function () {
            Route::get("idiomas/export", 'AdminIdiomasController@generateExcel');
            Route::get('idiomas/state/{id}', 'AdminIdiomasController@setChangeState')->where('id', '[0-9]+');
            Route::get('idiomas/default/{id}', 'AdminIdiomasController@setDefaultState')->where('id', '[0-9]+');
            Route::post("idiomas/list", 'AdminIdiomasController@getData');
            Route::post("idiomas/delete-selected", 'AdminIdiomasController@destroySelected');
            Route::resource('idiomas', 'AdminIdiomasController');
        });
    }
);
