<?php

use Illuminate\Support\Facades\Route;

// Module Media
Route::group(array('namespace' => 'Clavel\Basic\Controllers\Media'), function () {
    Route::get('/media/getAnnex/{id}', 'MediaController@getAnnex');

    Route::group(array('prefix' => 'admin', 'middleware' => ['web']), function () {
        // Media Management
        Route::get('media', 'AdminMediaController@index')->name('media');
        Route::post('media/dir/create', "AdminMediaController@createDirectory");
        Route::get('media/dir/delete/{routedir}', 'AdminMediaController@deleteDirectory');
        Route::post('media/file/upload', 'AdminMediaController@uploadFiles');
        Route::get('media/file/{id}/destroy', 'AdminMediaController@destroy');
        Route::get('media/file/{id}/optimize', 'AdminMediaController@optimize');
        Route::post('media/list/{path?}', 'AdminMediaController@getData');

        Route::get('media/file/{id}', 'AdminMediaController@getFile');

        // Media Viewer
        Route::get('/media/viewer-simple/{only_img?}', 'AdminMediaViewerController@index');
        Route::post('/media/load', 'AdminMediaViewerController@loadImages');
    });
});

// Module Pages
Route::group(array('namespace' => 'Clavel\Basic\Controllers\Pages', 'middleware' => ['web']), function () {
    Route::get("/pages/{slug}/{id?}", "FrontPagesController@index");

    Route::group(array('prefix' => 'admin', 'middleware' => ['web']), function () {
        Route::post("pages/list", 'AdminPagesController@getData');
        Route::get('pages/state/{id}', 'AdminPagesController@setChangeState')->where('id', '[0-9]+');
        Route::get("/pages/preview/{id}", 'AdminPagesController@getPagePreview')->where("id", '[0-9]+');
        Route::post("/pages/preview", 'AdminPagesController@postPagePreview');
        Route::resource('pages', 'AdminPagesController');
    });
});

// Module Menus
Route::group(array('namespace' => 'Clavel\Basic\Controllers\Menu'), function () {
    Route::group(array('prefix' => 'admin', 'middleware' => ['web']), function () {
        Route::post("menu/list", 'AdminMenuController@getData');
        Route::resource('menu', 'AdminMenuController');

        Route::group(array('prefix' => 'menu'), function () {
            Route::post("structure/list", 'AdminMenuStructureController@getData');
            Route::get('structure/{menu_id}', 'AdminMenuStructureController@index')
                ->where('menu_id', '[0-9]+');
            Route::get("structure/{menu_id}/edit/{idnode}", "AdminMenuStructureController@openform")
                ->where('menu_id', '[0-9]+')
                ->where("node_id", '[0-9]+');
            Route::get(
                "structure/{menu_id}/tree/{node_id}/{parent_id}/{prev_id}",
                "AdminMenuStructureController@reordenarArbol"
            )
                ->where('menu_id', '[0-9]+')
                ->where("node_id", '[0-9]+')
                ->where("parent_id", '[0-9]+')
                ->where("prev_id", '[0-9]+');
            Route::get('structure/{node_id}/destroy', "AdminMenuStructureController@destroy")
                ->where("node_id", '[0-9]+');
            Route::post(
                'structure/store',
                ['as' => 'admin.menu.structure.store', "uses" => "AdminMenuStructureController@store"]
            );
        });
    });
});
