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

Route::middleware('auth:sanctum')->group(function () {
	Route::post('categories-store','App\Http\Controllers\CategoryController@store');
	Route::post('categories-update','App\Http\Controllers\CategoryController@update');
	Route::post('categories-get','App\Http\Controllers\CategoryController@index');
	Route::post('categories-delete','App\Http\Controllers\CategoryController@delete');
	Route::post('business-update','App\Http\Controllers\BusinessController@update');
});
Route::get('invalid',function(){
	 return response()->json(['message'=>'Access token not matched'],422);
})->name('invalid');