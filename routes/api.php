<?php

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


Route::group(['middleware' => 'auth'], function () {
    Route::post('register', 'App\Http\Controllers\Auth\RegisterController@register');
    Route::get('verify', 'App\Http\Controllers\Auth\RegisterController@verify');
    Route::post('login', 'App\Http\Controllers\Auth\LoginController@login');
    Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout');
    Route::post('ljoin/{id}', 'App\Http\Controllers\LotController@joinLot');

    Route::get('check-user', 'App\Http\Controllers\Auth\LoginController@checkUser');
    Route::get('users', 'App\Http\Controllers\UserController@getAll');
    Route::get('user/{id}', 'App\Http\Controllers\UserController@getById');
    Route::get('lots-won-by/{id}', 'App\Http\Controllers\LotController@wonById');
    Route::get('lots-created-by/{id}', 'App\Http\Controllers\LotController@createdById');
    Route::get('winners', 'App\Http\Controllers\UserController@winners');

    Route::get('lots', 'App\Http\Controllers\LotController@getActive');
    Route::get('lot/{id}', 'App\Http\Controllers\LotController@getById');
    Route::post('create', 'App\Http\Controllers\LotController@create');
    Route::put('update/{id}', 'App\Http\Controllers\LotController@update');
    Route::get('now', 'App\Http\Controllers\LotController@now');

});

Route::group(['middleware' => 'admin'], function () {
    Route::get('admin/lots', 'App\Http\Controllers\AdminController@getLots');
    Route::put('admin/lot-update/{id}', 'App\Http\Controllers\AdminController@updateLot');
    Route::delete('admin/lot-delete/{id}', 'App\Http\Controllers\AdminController@deleteLot');

    Route::get('admin/users', 'App\Http\Controllers\AdminController@getUsers');
    Route::put('admin/user-update/{id}', 'App\Http\Controllers\AdminController@updateUser');
    Route::delete('admin/user-delete/{id}', 'App\Http\Controllers\AdminController@deleteUser');

});
