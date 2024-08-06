<?php

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
Route::get('success', function () {
    return view('success');
});
Route::get('/payment/{userId}', function ($userId) {
    return view('payment',compact('userId'));
});
Route::get('/test', function () {
    dd("test");
});
Route::get('test','App\Http\Controllers\AdminController@index');
use App\Http\Controllers\PaymentController;

Route::post('create-charge', [PaymentController::class, 'createCharge']);
Route::get('charge-customer', [PaymentController::class, 'chargeCustomer']);
Route::get('get-detail', [PaymentController::class, 'getChargeDetails']);
