<?php

// Module File Transfer
Route::group(array('namespace' => 'Clavel\FileTransfer\Controllers', 'middleware' => ['web']), function () {
    Route::get('/file-transfer', "FrontFileTransferController@index");

    Route::group(array('prefix' => 'file-transfer/upload', 'middleware' => ['web']), function () {
        Route::get('/', [
            'uses' => 'FrontFileTransferUploadController@create',
            'as' => 'file-transfer.upload.create'
        ]);
        Route::post('/file', [
            'uses' => 'FrontFileTransferUploadController@store',
            'as' => 'file-transfer.upload.store'
        ]);
        Route::post('/complete', [
            'uses' => 'FrontFileTransferUploadController@complete',
            'as' => 'file-transfer.upload.complete'
        ]);
        Route::post('/destroy', [
            'uses' => 'FrontFileTransferUploadController@destroy',
            'as' => 'file-transfer.upload.destroy'
        ]);

        Route::post('/send', [
            'uses' => 'FrontFileTransferUploadController@send',
            'as' => 'file-transfer.upload.send'
        ]);
    });

    Route::group(array('prefix' => 'file-transfer/bundle', 'middleware' => ['web']), function () {
        Route::get('/{bundle}', [
            'uses' => 'FrontFileTransferBundleController@preview',
            'as' => 'file-transfer.bundle.preview'
        ]);
        Route::get('/{bundle}/download', [
            'uses' => 'FrontFileTransferBundleController@download',
            'as' => 'file-transfer.bundle.download'
        ]);
        Route::get('/{bundle}/file/{file}/download', [
            'uses' => 'FrontFileTransferBundleController@download',
            'as' => 'file-transfer.file.download'
        ]);
        Route::get('/{bundle}/delete', [
            'uses' => 'FrontFileTransferBundleController@delete',
            'as' => 'file-transfer.bundle.delete'
        ]);
    });



    Route::group(array('prefix' => 'admin', 'middleware' => ['web']), function () {
        Route::resource('file-transfer', 'AdminFileTransferController');
    });
});
