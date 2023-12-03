<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthoUserController;
use App\Http\Controllers\Api\AuthCustomController;
use App\Http\Controllers\Api\NewPasswordController;
use App\Http\Controllers\Api\EmailVerificationController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthCustomController::class, 'login']);
    Route::post('register', [AuthCustomController::class, 'register']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::get('logout', [AuthCustomController::class, 'logout']);
      Route::get('logout2', [AuthCustomController::class, 'logoutonly']);
      //      
    });

});
Route::group(['middleware' => 'auth:sanctum'], function () {
  Route::get('verify/otp/resend', [AuthCustomController::class, 'resendEmailVerificationOtp']);
  Route::post('verifiedby/otp', [EmailVerificationController::class, 'email_verificationOtp']);
  });
  Route::post('forgot-password/otp', [NewPasswordController::class, 'forgetpasswordotp']);
  Route::post('resetpassword/otp', [NewPasswordController::class, 'resetpasswordotp']);
