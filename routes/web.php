<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PriceController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/live_prices', function () {
    return PriceController::live_stream();
});


Route::get('/get_product_price/{product_codes}', 'App\Http\Controllers\API\PriceController@get_product_price');
