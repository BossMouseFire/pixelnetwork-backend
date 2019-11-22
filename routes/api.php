<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');
});

// Routes if you authorized
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('logout', 'AuthController@logout');
    Route::get('user', 'AuthController@user');
    Route::get('user.get/{id}', 'UserController@get');
    Route::post('user.change.link', 'UserController@changeId');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'account'], function(){
    Route::post('user.friend/add', 'UserController@add');
    Route::put('user.friend/accept', 'UserController@accept');
});
