<?php

use App\Http\Controllers\AirlineController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\StreetController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check() || !auth()->check()) return view('pages.front', [
        'title' => 'Register',
        'show_sidebar' => false,
    ]);
});

Route::prefix('/auth')->middleware('guest')->controller(AuthenticationController::class)->group(function () {
    Route::get('/login', 'indexLogin');
    Route::post('/login', 'loginProcess');

    Route::get('forgot-password', 'showForgotPassword');
    Route::post('send-password-reset-link', 'submitForgetPasswordForm');
    Route::get('reset-password/{token}', 'showResetPasswordForm')->name('reset_password');
    Route::post('reset-password', 'submitResetPasswordForm')->name('reset_password.post');

    Route::get('/register', 'indexRegister');
    Route::post('/register', 'registerProcess');

    Route::post('/logout', 'logout')->withoutMiddleware('guest');
});

Route::prefix('/dashboard')->middleware('auth')->group(function () {
    Route::get('/index', DashboardController::class);

    Route::prefix('/airlines')->controller(AirlineController::class)->group(function () {
        Route::get('/index', 'index');
        Route::get('/get-data', 'getData');
        Route::get('/create', 'create');
        Route::post('/store', 'store');
        Route::get('/{airline:id}/edit', 'edit');
        Route::put('/{airline:id}/update', 'update');
        Route::delete('/{airline:id}/delete', 'destroy');
    });

    Route::prefix('/streets')->controller(StreetController::class)->group(function () {
        Route::get('/index', 'index');
        Route::get('/get-data', 'getData');
        Route::post('/store', 'store');
        Route::get('/{street:id}/edit', 'edit');
        Route::put('/{street:id}/update', 'update');
        Route::delete('/{street:id}/delete', 'destroy');
    });

    Route::prefix('/payment-methods')->controller(PaymentMethodController::class)->group(function () {
        Route::get('/index', 'index');
        Route::get('/get-data', 'getData');
        Route::get('/create', 'create');
        Route::post('/store', 'store');
        Route::get('/{paymentMethod:id}/edit', 'edit');
        Route::put('/{paymentMethod:id}/update', 'update');
        Route::delete('/{paymentMethod:id}/delete', 'destroy');
    });

    Route::prefix('/tickets')->controller(TicketController::class)->group(function () {
        Route::get('/index', 'index');
        Route::get('/get-data', 'getData');
        Route::get('/create', 'create');
        Route::post('/store', 'store');
        Route::get('/{ticket:id}/edit', 'edit');
        Route::put('/{ticket:id}/update', 'update');
        Route::delete('/{ticket:id}/delete', 'destroy');
        Route::get('/check-price', 'checkPrice');
    });

    Route::prefix('/orders')->controller(OrderController::class)->group(function () {
        Route::get('/get-payment-methods', 'getPaymentMethods');
        Route::get('/create', 'create');
        Route::post('/store', 'store');
        Route::prefix('/history')->group(function () {
            Route::get('/index', 'index');
            Route::get('/get-data', 'getData');
            Route::delete('/{order:id}/delete', 'destroy');
        });
    });

    Route::post('complaints', [ComplaintController::class, 'store']);
    Route::get('print', PrintController::class);

    Route::prefix('/transactions/history')->controller(TransactionController::class)->group(function () {
        Route::get('/index', 'index');
        Route::get('/get-data', 'getData');
        Route::get('/{transaction:id}/edit', 'edit');
        Route::post('/{transaction:id}/store-img', 'storeImg');
        Route::put('/{transaction:id}/update-status', 'updateStatus');
    });

    Route::prefix('/users')->controller(UserController::class)->group(function () {
        Route::get('/index', 'index');
        Route::get('/get-data', 'getData');
        Route::get('/create', 'create');
        Route::post('/store', 'store');
        Route::get('/{user:id}/edit', 'edit');
        Route::put('/{user:id}/update', 'update');
        Route::delete('/{user:id}/delete', 'destroy');
    });
});
