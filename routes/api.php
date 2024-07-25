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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('register','App\Http\Controllers\AuthController@register');
Route::post('gmail','App\Http\Controllers\AuthController@gmail');
Route::post('login','App\Http\Controllers\AuthController@login');
Route::post('categories-get','App\Http\Controllers\CategoryController@index');
Route::post('update','App\Http\Controllers\AuthController@update');
Route::middleware('auth:sanctum')->group(function () {
	
	Route::post('categories-store','App\Http\Controllers\CategoryController@store');
	Route::post('categories-update','App\Http\Controllers\CategoryController@update');
	
	Route::post('categories-delete','App\Http\Controllers\CategoryController@delete');
	Route::post('business-update','App\Http\Controllers\BusinessController@update');
	Route::post('business-approve','App\Http\Controllers\BusinessController@businessApprove');
	Route::post('business-reject','App\Http\Controllers\BusinessController@businessReject');
	Route::post('points-store','App\Http\Controllers\PointController@store');
	Route::post('points-update','App\Http\Controllers\PointController@update');
	Route::post('points-get','App\Http\Controllers\PointController@index');
	Route::post('points-delete','App\Http\Controllers\PointController@delete');
	Route::post('consume','App\Http\Controllers\PointController@consume');
	Route::post('get-consumes','App\Http\Controllers\PointController@getConsumes');
	Route::post('get-user-points','App\Http\Controllers\PointController@getUserPoints');

	Route::post('purchases-against-business','App\Http\Controllers\PointController@purchasesAgainstBusiness');
	Route::post('counts-against-business','App\Http\Controllers\PointController@countsAgainstBusiness');
	Route::post('counts-against-business2','App\Http\Controllers\PointController@countsAgainstBusiness2');
	Route::post('consumes-against-business','App\Http\Controllers\PointController@consumesAgainstBusiness');
	Route::post('consumes-against-business2','App\Http\Controllers\PointController@consumesAgainstBusiness2');

	Route::post('businesses-get','App\Http\Controllers\BusinessController@index');
	Route::post('businesses-get2','App\Http\Controllers\BusinessController@index2');

	Route::post('items-store','App\Http\Controllers\ItemController@store');
	Route::post('items-update','App\Http\Controllers\ItemController@update');
	Route::post('items-get','App\Http\Controllers\ItemController@index');
	Route::post('items-delete','App\Http\Controllers\ItemController@delete');

	Route::post('discounts-store','App\Http\Controllers\DiscountController@store');
	Route::post('discounts-update','App\Http\Controllers\DiscountController@update');
	Route::post('discounts-get','App\Http\Controllers\DiscountController@index');
	Route::post('discounts-delete','App\Http\Controllers\DiscountController@delete');

	Route::post('promos-store','App\Http\Controllers\PromoController@store');
	Route::post('promos-update','App\Http\Controllers\PromoController@update');
	Route::post('promos-get','App\Http\Controllers\PromoController@index');
	Route::post('promos-delete','App\Http\Controllers\PromoController@delete');

	Route::post('get-all-users','App\Http\Controllers\AuthController@getAllUsers');

});
Route::post('submit-to-calculate','App\Http\Controllers\PointController@submitToCalculate');
Route::get('invalid',function(){
	 return response()->json(['message'=>'Access token not matched'],422);
})->name('invalid');