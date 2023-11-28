<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthoUserController::class, 'login']);
    Route::post('register', [AuthoUserController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::get('logoutalldevices', [AuthoUserController::class, 'logout']);
      Route::get('logout', [AuthoUserController::class, 'logoutonly']);
      //      
    });
});
