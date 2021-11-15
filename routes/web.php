<?php

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

Route::post('/clothes', 'ClothController@store');
Route::patch('/clothes/{cloth}', 'ClothController@update');
Route::delete('/clothes/{cloth}', 'ClothController@destroy');
Route::get('/clothes/{cloth}', 'ClothController@show');

Route::post('/days', 'DayController@store');
Route::patch('/days/{day}', 'DayController@update');
Route::delete('/days/{day}', 'DayController@destroy');
Route::get('/days/{day}', 'DayController@show');
