<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Authen จากตาราง web_service
Route::group([
  'prefix' => 'v1'
], function () {

    //ผปก.
    Route::post('auth', 'API\UserController@Auth');
    Route::post('login', 'API\UserController@Login');

    //จนท. สมอ.
    Route::post('officer_auth', 'API\UserController@officer_auth');
    Route::post('officer_login', 'API\UserController@officer_login');
    Route::post('officer_role', 'API\UserController@officer_role');

    Route::get('sessions', 'API\SessionController@Sessions');
});
