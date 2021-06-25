<?php

// Module Crud Generator
Route::group(array('namespace' => 'Clavel\Grafos\Controllers', 'middleware' => ['web']), function () {
    Route::group(array('prefix' => 'admin', 'middleware' => ['web']), function () {
        Route::get('grafos', 'GrafosController@index');

        Route::get('grafos/latlong', 'GrafosController@indexLatLong');

        Route::get('grafos/lf', 'GrafosController@indexLF');
        Route::get('grafos/js', 'GrafosController@indexJS');
        Route::get('grafos/ra', 'GrafosController@routingAPI');
        Route::get('grafos/vrpjs', 'GrafosController@routeOptimizationAPIJS');
        Route::get('grafos/vrpphp', 'GrafosController@routeOptimizationAPIPHP');
        Route::get('grafos/vrp', 'GrafosController@routeOptimizationAPI');
        Route::get('grafos/gcjs', 'GrafosController@geocodingJSAPI');
        Route::get('grafos/isjs', 'GrafosController@isochroneJSAPI');




        Route::get('grafos/ma', 'GrafosController@matrixAPI');
        Route::get('grafos/gc', 'GrafosController@geocodingAPI');
        Route::get('grafos/is', 'GrafosController@isochroneAPI');
        Route::get('grafos/ca', 'GrafosController@clusterAPI');
        Route::get('grafos/goo', 'GrafosController@indexGoogle');
    });
});
