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

// Route::middleware('auth:api')->get('user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'clothes'], function () {
    Route::get('/', 'ClothController@index');
    Route::post('/', 'ClothController@store');
    Route::patch('/{cloth}', 'ClothController@update');
    Route::delete('/{cloth}', 'ClothController@destroy');
    Route::get('/{cloth}', 'ClothController@show');
});

Route::group(['prefix' => 'days'], function () {
    Route::post('/', 'DayController@store');
    Route::patch('/{day}', 'DayController@update');
    Route::delete('/{day}', 'DayController@destroy');
    Route::get('/{day}', 'DayController@show');
});




