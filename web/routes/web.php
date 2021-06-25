<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Front Routes
Route::get('/', 'Home\FrontHomeController@index')->name('home');


// Generales
Route::group(['middleware' => ['web']], function () {
    // Cambio de idioma
    Route::get('/changelanguage/{lang}', 'Language\LanguageController@switchLang');

    // Rutas de estadísticas
    Route::post('estadistica', 'EstadisticasController@index');

    // Cookies
    Route::get('/aceptar_cookies', function () {
        Session::put("cookies", '1');
    });
});

// Authentication Routes...
Route::get('login', 'Auth\FrontLoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\FrontLoginController@login');
Route::any('logout', 'Auth\FrontLoginController@logout')->name('logout');


// Front Registration Routes...
Route::get('register', 'Auth\FrontRegisterController@create')->name('register');
Route::post('register', 'Auth\FrontRegisterController@store');

// Front Password Reset Routes...
Route::get('password/reset', 'Auth\FrontForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\FrontForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\FrontResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\FrontResetPasswordController@reset')->name('password.update');


Route::get('/home', 'Home\FrontHomeController@indexPrivate')->name('home');


// Admin Routes
Route::group(array('prefix' => 'admin'), function() {

    // Back Authentication Routes...
    Route::get('login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('login', 'Auth\AdminLoginController@login');
    Route::post('logout', 'Auth\AdminLoginController@logout')->name('admin.logout');

    // Back Registration Routes...
    Route::get('register', 'Auth\AdminRegisterController@showRegistrationForm')->name('admin.register');
    Route::post('register', 'Auth\AdminRegisterController@register');

    // Back Password Reset Routes...
    Route::get('password/reset/{token}', 'Auth\AdminResetPasswordController@showResetForm')->name('admin.password.reset');
    Route::get('password/reset', 'Auth\AdminForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
    Route::post('password/email', 'Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
    Route::post('password/reset', 'Auth\AdminResetPasswordController@reset');

    // Password Confirmation Routes...
    Route::get('password/confirm', 'Auth\AdminConfirmPasswordController@showConfirmForm')->name('admin.password.confirm');
    Route::post('password/confirm', 'Auth\AdminConfirmPasswordController@confirm');

    // Admin General Routes
    Route::group(['middleware' => 'admin'], function() {
        Route::get('/', '\App\Http\Controllers\DashboardController\DashboardController@index')->name('admin');
        Route::post('dashboard/savestate', '\App\Http\Controllers\DashboardController\DashboardController@saveState');
        Route::post('dashboard/changeskin', '\App\Http\Controllers\DashboardController\DashboardController@changeSkin');
    });
});


// Modules Routes

// Module Users
Route::group(array('namespace' => 'User'), function() {

    // Admin Module Routes
    Route::group(array('prefix' => 'admin'), function () {
        // Ruta para volver al usuario original...
        Route::get("users/suplantar/revertir", "AdminSuplantacionController@revertir");
        // Ruta de suplantación...
        Route::get("users/suplantar/{id}", "AdminSuplantacionController@suplantar");


        Route::post("users/list", 'AdminUserController@getData');
        Route::get('users/state/{id}', 'AdminUserController@setChangeState')->where('id', '[0-9]+');
        Route::get('users/edit_user/{id}', 'AdminUserController@getUserForm')->where('id', '[0-9]+');
        Route::post('users/exists/login',  'AdminUserController@checkLoginExists');
        Route::post('users/generate/pass',  'AdminUserController@generatePassword');
        Route::get('users/generateExcel', 'AdminUserController@generateExcel');
        Route::get('users/userStats', 'AdminUserController@getUserStats');

        Route::get("users/roles/{id}", 'AdminRolesController@edit')->where('id', '[0-9]+');
        Route::post('users/roles/update', 'AdminRolesController@update');

        Route::get("users/social/{id}", 'AdminSocialController@edit')->where('id', '[0-9]+')->name('users.social.edit');
        Route::patch('users/social/update/{id}', 'AdminSocialController@update')->where('id', '[0-9]+')->name('users.social.update');

        Route::resource('users', 'AdminUserController');

    });
});

// Module Roles
Route::group(array('namespace' => 'Roles'), function() {

    // Admin Module Routes
    Route::group(array('prefix' => 'admin'), function () {

        Route::post("roles/list", 'AdminRolesController@getData');
        Route::get('roles/state/{id}', 'AdminRolesController@setChangeState')->where('id', '[0-9]+');
        Route::get('roles/edit_role/{id}', 'AdminRolesController@getRoleForm')->where('id', '[0-9]+');

        Route::get("roles/permissions/{id}", 'AdminPermissionsController@edit')->where('id', '[0-9]+');
        Route::post('roles/permissions/update', 'AdminPermissionsController@update');

        Route::resource('roles', 'AdminRolesController');

    });


});


// Module User Profile
Route::group(array('namespace' => 'Profile'), function() {

    Route::group(['middleware' => 'auth'], function() {
        Route::get("profile", "FrontProfileController@edit");
        Route::get('profile/getphoto/{photo}', 'FrontProfileController@getPhoto');
        Route::post('profile', 'FrontProfileController@update');
        Route::post('profile/exists/login', 'FrontProfileController@checkLoginExists');
    });

    Route::group(array('prefix' => 'admin'), function() {
        Route::get('profile', 'AdminProfileController@edit');
        Route::get('profile/getphoto/{photo}',  'AdminProfileController@getPhoto');
        Route::post('profile/photo',  'AdminProfileController@upload');
        Route::post('profile', 'AdminProfileController@update');
        Route::post('profile/exists/login',  'AdminProfileController@checkLoginExists');

        Route::patch('profile/social/update/{id}', 'AdminProfileController@updateSocial')->where('id', '[0-9]+')->name('profile.social.update');

    });
});

// Module Notificaciones del sistema
Route::group(array('namespace' => 'SystemNotification'), function() {
    Route::post('notification/mark',  'FrontSystemNotificationController@mark');
    Route::post('notification/mark_all',  'FrontSystemNotificationController@markAll');

    Route::group(array('prefix' => 'admin'), function() {
        Route::post('notification/mark',  'AdminSystemNotificationController@mark');
        Route::post('notification/mark_all',  'AdminSystemNotificationController@markAll');
    });
});

//Admin Module Control de Acceso
Route::group(array('namespace' => 'Acceso'), function() {

    // Admin Module Routes
    Route::group(array('prefix' => 'admin'), function () {
        Route::get('acceso/generateExcel', 'AdminAccesoController@generateExcel');
        Route::post("acceso/list", 'AdminAccesoController@getData');

        Route::resource('acceso', 'AdminAccesoController');
    });

});


Route::get('exp', function() {
    throw new Exception('total fail');
});
