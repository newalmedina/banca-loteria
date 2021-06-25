<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

/* Version 1 */

Route::group(['prefix' => 'v1'], function () {
    /* Auth */
    Route::group(['prefix' => 'auth'], function () {
        // /api/v1/auth/signin
        Route::post('signin', 'Api\AuthController@signin');
        //Route::post('register', 'Api\AuthController@register');

        Route::group(['middleware' => 'auth:api'], function () {
            // /api/v1/auth/signout
            Route::post('signout', 'Api\AuthController@signout');

            Route::get('need-reset-password', 'Api\AuthController@needResetPassword');
            Route::post('need-reset-password', 'Api\AuthController@changePassword');
            Route::get('verify-authorization-token', 'Api\AuthController@verifyToken');

        });

    });

    /* User */
    Route::group(['prefix' => 'user'], function () {
        Route::post('password/email', 'Api\UserController@postEmail');
        Route::post('password/token', 'Api\UserController@getPasswordToken');
        Route::post('password/reset', 'Api\UserController@resetPassword');
        Route::group(['middleware' => 'auth:api'], function () {
            Route::get('profile', 'Api\UserController@getProfile')->name('profile.get');
            Route::patch('/profile/{id?}', 'Api\UserController@updateProfile')->name('profile.update');
        });



    });




});
