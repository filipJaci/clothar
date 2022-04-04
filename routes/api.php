<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ClothController;
use App\Http\Controllers\API\ClothDayController;

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

// Auth routes.
Route::post('login', [UserController::class, 'login'])->middleware('verified.email');
Route::post('register', [UserController::class, 'register']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::post('verify', [UserController::class, 'verifyEmail']);

// Clothes.
Route::group(['prefix' => 'clothes', 'middleware' => 'auth:sanctum'], function () {
  Route::get('/', [ClothController::class, 'index']);
  Route::post('/', [ClothController::class, 'store']);
  Route::patch('/{cloth}', [ClothController::class, 'update']);
  Route::delete('/{cloth}', [ClothController::class, 'destroy']);
});

// Days.
Route::group(['prefix' => 'days', 'middleware' => 'auth:sanctum'], function () {
  Route::get('/', [ClothDayController::class, 'index']);
  Route::post('/', [ClothDayController::class, 'store']);
  Route::patch('/{day}', [ClothDayController::class, 'update']);
  Route::delete('/{day}', [ClothDayController::class, 'destroy']);
  Route::get('/{day}', [ClothDayController::class, 'show']);
});