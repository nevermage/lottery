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
    Route::post('join2/{id}', 'App\Http\Controllers\LotController@joinLot');

    Route::get('check-user', 'App\Http\Controllers\Auth\LoginController@checkUser');
    Route::get('users', 'App\Http\Controllers\UserController@getAll');
    Route::get('user/{id}', 'App\Http\Controllers\UserController@getById');

    Route::get('active-lots', 'App\Http\Controllers\LotController@getActive');
    Route::get('lot/{id}', 'App\Http\Controllers\LotController@getById');

});
