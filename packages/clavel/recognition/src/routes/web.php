<?php

// Module Posts
Route::group(array('namespace' => 'Clavel\Recognition\Controllers', 'middleware' => ['web']), function () {

    // Recognition
    Route::get('/recognition', "RecognitionController@index");

    // Reckognition
    Route::get('/recognition/rekognition', "RecognitionRekognitionController@getRekognition");
    Route::get(
        '/recognition/rekognition/process/{bucket}/{file}',
        "RecognitionRekognitionController@getRekognitionProcess"
    );
    Route::get(
        '/recognition/rekognition/process-label/{bucket}/{file}',
        "RecognitionRekognitionController@getRekognitionProcessLabel"
    );

    // S3
    Route::get('/recognition/s3', "RecognitionS3Controller@getS3");
    Route::get('/recognition/s3/{bucket}', "RecognitionS3Controller@getS3BucketContent");
    Route::get('/recognition/s3/upload/{bucket}', "RecognitionS3Controller@getS3BucketUpload");
    Route::post('/recognition/s3/upload', "RecognitionS3Controller@getS3BucketUploadFile");
    Route::get('/recognition/s3/download/{bucket}/{file}', "RecognitionS3Controller@getS3BucketDownload");
    Route::get('/recognition/s3/process/{bucket}/{file}', "RecognitionS3Controller@getS3Process");
    Route::get('/recognition/s3/view/{bucket}/{file}', "RecognitionS3Controller@getS3View");
    Route::get('/recognition/s3/delete/{bucket}/{file}', "RecognitionS3Controller@getS3Delete");


    // Textract
    Route::get('/recognition/textract', "RecognitionTextractController@getTextract");
    Route::get('/recognition/textract/process/{bucket}/{file}', "RecognitionTextractController@getTextractProcess");

    Route::get('/recognition/storage/{filename}', function ($filename) {
        $path = storage_path('app/dni/' . $filename);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    });
});
