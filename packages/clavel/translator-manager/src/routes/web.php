<?php

// Module Pages
Route::group(array('namespace' => 'Clavel\TranslatorManager\Controllers', 'middleware' => ['web']), function () {
    Route::group(array('prefix' => 'admin', 'middleware' => ['web']), function () {
        Route::post('translator/postImport', 'TranslatorManagerController@postImport');
        Route::post('translator/postFind', 'TranslatorManagerController@postFind');
        Route::post('translator/postAddGroup', 'TranslatorManagerController@postAddGroup');
        Route::post('translator/postRemoveLocale', 'TranslatorManagerController@postRemoveLocale');
        Route::post('translator/postAddLocale', 'TranslatorManagerController@postAddLocale');
        Route::post('translator/postAdd', 'TranslatorManagerController@postAdd');
        Route::post('translator/postTranslateMissing', 'TranslatorManagerController@postTranslateMissing');
        Route::post('translator/postPublish', 'TranslatorManagerController@postPublish');
        Route::post('translator/postDelete', 'TranslatorManagerController@postDelete');
        Route::get('translator/getView', 'TranslatorManagerController@getView');
        Route::get('translator/getIndex', 'TranslatorManagerController@getIndex');


        Route::resource('translator', 'TranslatorManagerController');
        Route::post('translator/import', 'TranslatorManagerController@import');
        Route::post('translator/find', 'TranslatorManagerController@find');
        Route::post('translator/publish', 'TranslatorManagerController@publish');


        Route::post('translator/group/auto_translate', 'TranslatorGroupController@autoTranslate');
        Route::post('translator/group/publish', 'TranslatorGroupController@publish');
        Route::post('translator/group/publish_locale', 'TranslatorGroupController@publishLocale');
        Route::post('translator/group/manage', 'TranslatorGroupController@index');
        Route::resource('translator/group', 'TranslatorGroupController');
    });
});
