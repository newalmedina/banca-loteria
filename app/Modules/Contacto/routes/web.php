<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'namespace' => 'App\Modules\Contacto\Controllers',
        'middleware' => ['web']
    ],
    function () {
        Route::get('contactus', 'FrontContactoController@create')->name("contactus");
        Route::post('contactus', 'FrontContactoController@store');
    }
);
