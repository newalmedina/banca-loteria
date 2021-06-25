<?php

// Module Events
Route::group([
    'module' => 'Events',
    'middleware' => ['web'],
    'namespace' => 'App\Modules\Events\Controllers'
    ], function () {
        Route::get("events/{date_search?}", 'FrontEventsController@index')->name('events');
        Route::get("events/list/{date_search?}", 'FrontEventsController@list');
        Route::get("events/event/{slug}", 'FrontEventsController@eventDetail')->name('events.event');
        Route::post("events/month", 'FrontEventsController@loadMonth');


        Route::group(array('prefix' => 'admin'), function () {
            Route::group(array('prefix' => 'events'), function () {
                Route::post("tags/list", 'AdminEventTagsController@getData');
                Route::get('tags/state/{id}', 'AdminEventTagsController@setChangeState')->where('id', '[0-9]+');
                Route::get('tags/state_home/{id}', 'AdminEventTagsController@setChangeHome')->where('id', '[0-9]+');
                Route::resource('tags', 'AdminEventTagsController');
            });

            Route::post("events/list", 'AdminEventsController@getData');
            Route::get('events/state/{id}', 'AdminEventsController@setChangeState')->where('id', '[0-9]+');
            Route::get('events/state_home/{id}', 'AdminEventsController@setChangeHome')->where('id', '[0-9]+');
            Route::resource('events', 'AdminEventsController');
        });
    });
