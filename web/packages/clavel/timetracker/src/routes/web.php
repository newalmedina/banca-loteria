<?php
use Illuminate\Support\Facades\Route;

// Clientes
Route::group(array('namespace' => 'Clavel\TimeTracker\Controllers\Customers', 'middleware' => ['web']), function () {
    Route::group(array('prefix' => 'admin',  "as" => "admin.", 'middleware' => ['web']), function () {
        Route::get('customers/state/{id}', 'AdminCustomersController@setChangeState')
            ->where('id', '[0-9]+');
        Route::post("customers/getData", 'AdminCustomersController@getData');
        Route::resource('customers', 'AdminCustomersController');
    });
});


// Proyectos
Route::group(array('namespace' => 'Clavel\TimeTracker\Controllers\Projects', 'middleware' => ['web']), function () {
    Route::group(array('prefix' => 'admin',  "as" => "admin.", 'middleware' => ['web']), function () {
        Route::get('projects/state/{id}', 'AdminProjectsController@setChangeState')
            ->where('id', '[0-9]+');
        Route::get('projects/clearFilter', 'AdminProjectsController@clearFilter');

        Route::get('projects/invoiced/{id}', 'AdminProjectsController@setInvoiceState')
            ->where('id', '[0-9]+');
        Route::post('projects/historify/{id}', 'AdminProjectsController@setHistorify')
            ->where('id', '[0-9]+');
        Route::post('projects/recover/{id}', 'AdminProjectsController@setRecovery')
            ->where('id', '[0-9]+');
        Route::post("projects/getData", 'AdminProjectsController@getData');
        Route::get('projects/historified', 'AdminProjectsController@setShowHistorified');
        Route::get('projects/generateExcel/{q?}', 'AdminProjectsController@generateExcel');

        Route::get('projects/stateProject/{id}', 'AdminProjectsController@getProjectStates')
            ->where('id', '[0-9]+');
        Route::post('projects/stateProject/{id}', 'AdminProjectsController@changeStateProject')
            ->where('id', '[0-9]+')
            ->name('projects.stateProject');

        Route::post('projects/order-number/{id}', 'AdminProjectsController@getOrderNumber')
            ->where('id', '[0-9]+');
        Route::post('projects/budge-number/{id}', 'AdminProjectsController@getBudgetNumber')
            ->where('id', '[0-9]+');

        Route::get('projects/typeProject/{id}', 'AdminProjectsController@getProjectTypes')
            ->where('id', '[0-9]+');
        Route::post('projects/typeProject/{id}', 'AdminProjectsController@changeTypeProject')
            ->where('id', '[0-9]+')
            ->name('projects.typeProject');
        Route::post('projects/saveFilter', 'AdminProjectsController@saveFilter');

        Route::resource('projects', 'AdminProjectsController');
    });
});

// Actividades
Route::group(array('namespace' => 'Clavel\TimeTracker\Controllers\Activities', 'middleware' => ['web']), function () {
    Route::group(array('prefix' => 'admin',  "as" => "admin.", 'middleware' => ['web']), function () {
        Route::get('activities/state/{id}', 'AdminActivitiesController@setChangeState')
            ->where('id', '[0-9]+');
        Route::post("activities/getData", 'AdminActivitiesController@getData');
        Route::resource('activities', 'AdminActivitiesController');
    });
});

// Hoja de tiempo
Route::group(array('namespace' => 'Clavel\TimeTracker\Controllers\TimeSheet', 'middleware' => ['web']), function () {
    Route::group(array('prefix' => 'admin',  "as" => "admin.", 'middleware' => ['web']), function () {
        Route::get('timesheet/state/{id}', 'AdminTimeSheetController@setChangeState')
            ->where('id', '[0-9]+');
        Route::post('timesheet/generateExcel', 'AdminTimeSheetController@generateExcel');
        Route::post('timesheet/activities/{id}', 'AdminTimeSheetController@getActivities')
            ->where('id', '[0-9]+');
        Route::post("timesheet/getData", 'AdminTimeSheetController@getData');
        Route::resource('timesheet', 'AdminTimeSheetController');
    });
});

// Mis tiempos
Route::group(array('namespace' => 'Clavel\TimeTracker\Controllers\MyTimes', 'middleware' => ['web']), function () {
    Route::group(array('prefix' => 'admin',  "as" => "admin.", 'middleware' => ['web']), function () {
        Route::get('mytimes/state/{id}', 'AdminMyTimesController@setChangeState')
            ->where('id', '[0-9]+');
        Route::get('mytimes/restart/{id}', 'AdminMyTimesController@restartTimeSheet')
            ->where('id', '[0-9]+');
        Route::get(
            'mytimes/restart-activity/{customer_id}/{project_id}/{activity_id}',
            'AdminMyTimesController@restartTimeSheetActivity'
        )
            ->where('id', '[0-9]+');

        Route::post("mytimes/getData", 'AdminMyTimesController@getData');
        Route::post('mytimes/generateExcel', 'AdminMyTimesController@generateExcel');
        Route::post('mytimes/generateMyActivity', 'AdminMyTimesController@generateMyActivity');
        Route::post('mytimes/generateMyDay', 'AdminMyTimesController@generateMyDay');
        Route::post('mytimes/generateMyWeek', 'AdminMyTimesController@generateMyWeek');

        Route::resource('mytimes', 'AdminMyTimesController');

        Route::get('mytimes/description/{id}', 'AdminMyTimesController@getDescription')
            ->where('id', '[0-9]+');
        Route::post('mytimes/description/{id}', 'AdminMyTimesController@changeDescription')
            ->where('id', '[0-9]+')
            ->name('mytimes.description');
    });
});


// Time Tracker Dashboard
Route::group(array('namespace' => 'Clavel\TimeTracker\Controllers\Dashboard', 'middleware' => ['web']), function () {
    Route::group(array('prefix' => 'admin',  "as" => "admin.", 'middleware' => ['web']), function () {
        Route::get('timetracker-dashboard', 'AdminDashboardController@index');
    });
});


// Time Tracker Config
Route::group(array('namespace' => 'Clavel\TimeTracker\Controllers\Config', 'middleware' => ['web']), function () {
    Route::group(array('prefix' => 'admin',  "as" => "admin.", 'middleware' => ['web']), function () {
        Route::get('timetracker-config', 'AdminConfigController@index')->name('timetracker-config.index');
        Route::post("timetracker-config", 'AdminConfigController@update')->name('timetracker-config.update');
    });
});
