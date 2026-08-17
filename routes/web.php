<?php

use App\Http\Controllers\AdminController;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegistrationVerificationController;
use App\Http\Controllers\PayMongoWebhookController;

Route::post('/paymongo/webhook', [PayMongoWebhookController::class, 'webhook'])->name('paymongo.webhook');

route::get('/', [HomeController::class, 'my_home']);

Route::get('/download-app', function () {
    return response()->download(public_path('downloads/Mi-Cusina.apk'), 'Mi-Cusina.apk');
})->name('mobile-app.download');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegistrationVerificationController::class, 'create'])->name('register');
    Route::post('/register', [RegistrationVerificationController::class, 'send'])
        ->middleware('throttle:registration-email')
        ->name('register.send');
    Route::get('/register/verify', [RegistrationVerificationController::class, 'verification'])
        ->name('register.verify');
    Route::post('/register/verify', [RegistrationVerificationController::class, 'confirm'])
        ->middleware('throttle:registration-code')
        ->name('register.confirm');
});


Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index']);
    Route::post('/customer/logout', [HomeController::class, 'logout'])->name('customer.logout');
    Route::post('/admin/profile-photo', [AdminController::class, 'update_profile_photo'])->name('admin.profile-photo.update');
    Route::post('/customer/profile-photo', [HomeController::class, 'update_profile_photo'])->name('customer.profile-photo.update');

    Route::get('/add_food', [AdminController::class, 'add_food']);
    Route::post('/upload_food', [AdminController::class, 'upload_food']);
    Route::get('/view_food', [AdminController::class, 'view_food']);
    Route::get('/inventory', [AdminController::class, 'inventory']);
    Route::post('/update_stock/{id}', [AdminController::class, 'update_stock']);
    Route::delete('/delete_food/{id}', [AdminController::class, 'delete_food']);
    Route::get('/update_food/{id}', [AdminController::class, 'update_food']);
    Route::post('/edit_food/{id}', [AdminController::class, 'edit_food']);

    Route::post('/add_cart/{id}', [HomeController::class, 'add_cart']);
    Route::post('/add_cart_ajax/{id}', [HomeController::class, 'add_cart_ajax']);
    Route::get('/my_cart', [HomeController::class, 'my_cart']);
    Route::post('/update_cart/{id}', [HomeController::class, 'update_cart']);
    Route::delete('/remove_cart/{id}', [HomeController::class, 'remove_cart']);
    Route::get('/my_orders', [HomeController::class, 'my_orders']);
    Route::get('/track_order/{id}', [HomeController::class, 'track_order']);
    Route::get('/order_receipt', [HomeController::class, 'order_receipt'])->name('order.receipt');
    Route::post('/confirm_order', [HomeController::class, 'confirm_order']);

    Route::get('/orders', [AdminController::class, 'orders']);
    Route::get('/sales-report', [AdminController::class, 'sales_report'])->name('admin.sales-report');
    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/riders', [AdminController::class, 'riders']);
    Route::post('/assign_rider/{id}', [AdminController::class, 'assign_rider']);
    Route::post('/rider_availability/{id}', [AdminController::class, 'set_rider_availability']);
    Route::post('/on_the_way/{id}', [AdminController::class, 'on_the_way']);
    Route::post('/delivered/{id}', [AdminController::class, 'delivered']);
    Route::post('/canceled/{id}', [AdminController::class, 'canceled']);
    Route::post('/paid/{id}', [AdminController::class, 'paid']);

    Route::post('/book_table', [HomeController::class, 'book_table'])->middleware('throttle:10,1');
    Route::get('/booking/payment/return/{booking}', [PayMongoWebhookController::class, 'complete'])->name('booking.payment.return');
    Route::get('/booking/payment/cancel/{booking}', [PayMongoWebhookController::class, 'cancel'])->name('booking.payment.cancel');
    Route::get('/reservations', [AdminController::class, 'reservations']);
    Route::post('/approve_reservation/{id}', [AdminController::class, 'approve_reservation']);
    Route::get('/add_staff', [AdminController::class, 'add_staff']);
    Route::post('/store_staff', [AdminController::class, 'store_staff']);
    Route::post('/chatbot/message', [HomeController::class, 'chatbot_message'])->middleware('throttle:30,1');
});











Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/home');
    })->name('dashboard');
});
