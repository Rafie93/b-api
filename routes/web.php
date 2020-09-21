<?php

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

Route::get('order/order-pdf/{id}', 'OrderController@order_pdf');
Route::get('order/pengiriman-pdf/{id}', 'OrderController@pengiriman_pdf');
Route::get('order/order-gudang-pdf/{id}', 'OrderController@order_gudang_pdf');
Route::get('purchase/purchase-pdf/{id}', 'PurchaseController@pdf');
Route::get('purchase/arrival/{id}', 'PurchaseController@arrifal_pdf');

Route::get('pos/struk/{id}', 'StrukController@pdf');

