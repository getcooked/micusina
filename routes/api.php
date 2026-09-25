<?php

use App\Http\Controllers\MobileApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('mobile')->middleware('throttle:120,1,mobile-api:')->group(function () {
    Route::post('/login', [MobileApiController::class, 'login'])->middleware('throttle:5,1,mobile-login:');
    Route::post('/register/send-verification', [MobileApiController::class, 'sendRegistrationVerification'])
        ->middleware('throttle:mobile-registration-email');
    Route::post('/register/verify', [MobileApiController::class, 'verifyRegistration'])
        ->middleware('throttle:mobile-registration-code');
    Route::get('/foods', [MobileApiController::class, 'foods']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [MobileApiController::class, 'logout']);
        Route::get('/me', [MobileApiController::class, 'me']);
        Route::get('/cart', [MobileApiController::class, 'cart']);
        Route::post('/cart/{food}', [MobileApiController::class, 'addCart']);
        Route::patch('/cart/{cart}', [MobileApiController::class, 'updateCart']);
        Route::delete('/cart/{cart}', [MobileApiController::class, 'removeCart']);
        Route::post('/checkout', [MobileApiController::class, 'checkout']);
        Route::get('/orders', [MobileApiController::class, 'orders']);
        Route::get('/reservations', [MobileApiController::class, 'reservations']);
        Route::post('/reservations', [MobileApiController::class, 'createReservation']);
        Route::get('/staff/dashboard', [MobileApiController::class, 'staffDashboard']);
        Route::get('/staff/orders', [MobileApiController::class, 'staffOrders']);
        Route::patch('/staff/orders/{order}', [MobileApiController::class, 'updateOrder']);
        Route::get('/staff/inventory', [MobileApiController::class, 'inventory']);
        Route::patch('/staff/inventory/{food}', [MobileApiController::class, 'updateStock']);
    });
});
