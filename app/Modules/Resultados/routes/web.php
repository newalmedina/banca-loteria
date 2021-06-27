<?php

use Illuminate\Support\Facades\Route;

// Modulo prueba
Route::group(
    [
        'namespace' => 'App\Modules\Resultados\Controllers',
        'middleware' => ['web']
    ],
    function () {
        Route::group(array('prefix' => 'admin', 'middleware' => ['web']), function () {
            
            Route::resource('resultados', 'adminResultadosController');

        });
    }
);

