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
Route::get('pos/struk2/{id}', 'StrukController@struk');

Route::get('sale/excel', 'SaleController@exportExcel');
Route::get('pendapatan/excel', 'SaleController@pendapatanExcel');
Route::get('total_pendapatan/excel', 'SaleController@pendapatanTotalExcel');
Route::get('barang_keluar/store/excel', 'SaleController@reportProductOut');
Route::get('barang_keluar/gudang/excel', 'OrderController@excel');
Route::get('pembelian/excel', 'PurchaseController@excel');
Route::get('barang_masuk/gudang/excel', 'PurchaseController@product_in');
Route::get('barang_masuk/store/excel', 'OrderController@product_in');

Route::get('stock/excel', 'StockController@excel');
Route::get('stock_history/excel', 'StockController@history_excel');

Route::get('product_terjual/excel', 'SaleController@product_terjual');



