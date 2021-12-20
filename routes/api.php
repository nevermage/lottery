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

Route::middleware('auth:api')
    ->get('/user', function (Request $request) {
        return $request->user();
    });

Route::group(['middleware' => 'api'], function () {
    Route::get('getusers', 'App\Http\Controllers\UserController@getUsers');
    Route::get('getuser/{id}', 'App\Http\Controllers\UserController@getUser');

    Route::get('getlots', 'App\Http\Controllers\LotController@getLots');
    Route::get('getlot/{id}', 'App\Http\Controllers\LotController@getLot');

    Route::post('register', 'App\Http\Controllers\Auth\RegisterController@register');
    Route::post('login', 'App\Http\Controllers\Auth\LoginController@login');
    Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout');

    Route::post('createlot', 'App\Http\Controllers\LotController@createLot');
    Route::put('updatelot/{id}', 'App\Http\Controllers\LotController@updateLot');
    Route::delete('deletelot/{id}', 'App\Http\Controllers\LotController@deleteLot');

    Route::get('lot/{id}/users', '\App\Http\Controllers\LotUserController@getLotUsers');

    Route::get('email/verify/{id}', 'App\Http\Controllers\Auth\VerificationController@verify')->name('verification.verify');
    Route::get('email/resend', 'VerificationController@resend')->name('verification.resend');
});
