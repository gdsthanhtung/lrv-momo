<?php

use App\Http\Controllers\PaymentMomoController;
use App\Http\Controllers\PaymentVNPayController;
use App\Http\Controllers\PaymentZaloPayController;
use Illuminate\Support\Facades\Auth;
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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/payment-momo',                [PaymentMomoController::class, 'checkout']); //Execute request to MoMo to create payment
Route::get( '/payment-callback-momo',       [PaymentMomoController::class, 'callback']); //Momo call this url to send payment result
Route::get( '/payment-check-status-momo',   [PaymentMomoController::class, 'checkStatus']); //Merchant (us) call this url to check payment status

Route::post('/payment-vnpay',               [PaymentVNPayController::class, 'checkout']);
Route::get( '/payment-complete-vnpay',      [PaymentVNPayController::class, 'paymentComplete']);

Route::post('/payment-zalopay',             [PaymentZaloPayController::class, 'checkout']);
Route::get( '/payment-callback-zalopay',    [PaymentZaloPayController::class, 'callback']);
Route::get( '/payment-check-status-zalopay',[PaymentZaloPayController::class, 'checkStatus']); //Merchant (us) call this url to check payment status

