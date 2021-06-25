<?php

use Illuminate\Support\Facades\Route;

/* Version 1 */
/* Version 1 */
Route::group(['prefix' => 'api/v1'], function () {
    Route::group([
        'namespace' => 'Clavel\TimeTracker\Controllers',
        'prefix' => 'timetracker',
        'middleware' => ['web']
    ], function () {
        Route::post('projects/{id}', 'Api\ProjectsController@getProjects')
            ->where('id', '[0-9]+');
    });
});
