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

Route::group(['prefix' => 'auth'], function() {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'user'], function() {
    Route::post('get/{id}', 'UserController@get');
    Route::post('change.link', 'UserController@changeLink');
});

Route::group(['prefix' => 'friends', 'middleware' => 'auth:api'], function() {
    Route::get('send.request', 'FriendController@send');
    Route::get('accept.request', 'FriendController@accept');
    Route::get('pending.list', 'FriendController@getAllFriendships');
});
