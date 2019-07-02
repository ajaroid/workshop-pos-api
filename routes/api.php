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

Route::middleware('auth:api')->get('/v1/user', function (Request $request) {
    return $request->user();
});

Route::post('v1/login', 'Api\V1\AuthController@login');

Route::group([
    'middleware' => ['auth:api'],
    'namespace' => 'Api\V1',
    'prefix' => 'v1'
], function () {
    Route::post('logout', 'AuthController@logout');
});
