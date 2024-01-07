<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\AuthCustomController;
use App\Http\Controllers\Api\NewPasswordController;
use App\Http\Controllers\Api\Product\CartController;
use App\Http\Controllers\Api\Product\HomeController;
use App\Http\Controllers\Api\Product\OrderController;
use App\Http\Controllers\Api\User\AddresseController;
use App\Http\Controllers\Api\Product\ReviewController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\Product\FavouriteController;

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
Route::group([], function () {

  Route::post('forgot-password/otp', [NewPasswordController::class, 'forgetpasswordotp']);
  Route::post('resetpassword/otp', [NewPasswordController::class, 'resetpasswordotp']);
}); 
//show products & filter
Route::group(['middleware' => ['auth:sanctum']], function () {

  Route::get('home', [HomeController::class, 'index']);
  Route::get('product/{id}',[ProductController::class,'show']);
  Route::get('product/review/{id}',[ReviewController::class,'showreview']);
  Route::get('product/myreview/{id}',[ReviewController::class,'showmyreview']);
  Route::post('product/review',[ReviewController::class,'store']);
  Route::get('product',[ProductController::class,'index']);
  Route::get('productcategory',[ProductController::class,'productCategory']);
  Route::get('/products/filter', [ProductController::class, 'filterProducts']);
});
//carts && order
Route::group(['middleware' => ['auth:sanctum']], function () {

  Route::post('cart', [CartController::class, 'addToCart']);
  Route::post('cart/toorder', [CartController::class, 'createOrder']);
  Route::get('cart', [CartController::class, 'getCart']);
  Route::delete('/cart/{productId}',[CartController::class, 'deleteProductFromCart'] );

  Route::post('/orders/{orderId}/cancel', [CartController::class, 'cancelOrder']);
  Route::post('/productorder', [OrderController::class, 'createOrderForProduct']);

});
//addresse
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::get('/addresses', [AddresseController::class, 'getAllAddresses']);
  
  Route::post('/addresses/create', [AddresseController::class, 'createAddress']);
  Route::post('/addresses/default', [AddresseController::class, 'makeDefaultAddress']);

  
});
//favourite
Route::group(['middleware' => ['auth:sanctum']], function () {
  Route::get('/favourite', [FavouriteController::class, 'getAllFavorites']);
  Route::post('/favourite/create', [FavouriteController::class, 'addToFavorites']);
  Route::delete('/favourite/delete', [FavouriteController::class, 'removeFromFavorites']);
  
});
Route::post('/user/profile', [UserController::class, 'updateProfile'])->middleware('auth:sanctum');


