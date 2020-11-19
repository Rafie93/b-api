<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
 //START API FOR MOBILE ANDROID TANPA LOGIN
Route::post('register', 'Auth\UserController@register');
Route::post('login', 'Auth\UserController@login');
Route::post('register_customer', 'Api\Sistem\AccountController@register');
Route::post('login_customer', 'Api\Sistem\AccountController@login');
Route::get('products', 'Api\Products\ProductForCustomerController@getProducts');
Route::get('product_detail/{id}', 'Api\Products\ProductForCustomerController@getProductDetail');
Route::get('product_commentar/{id}', 'Api\Products\ProductForCustomerController@getCommentar');

Route::get('bank', 'Api\Sistem\BankController@index');
Route::get('bank_account','Api\Sistem\BankController@bank_account');
Route::get('banner', 'Api\HomeMobile\BannerController@getBanner');
Route::get('terlaris', 'Api\HomeMobile\TerlarisController@getProductTerlaris');
Route::post('rates', 'Api\Transaction\RateShippingController@rate_check');
Route::get('voucher', 'Api\Sistem\VoucherController@index');
Route::get('voucher/check', 'Api\Sistem\VoucherController@checkVoucher');
Route::get('kategori', 'Api\Products\CategoryController@list_parent');
Route::get('transaksi/cloud_data', 'Api\Sistem\SinkronisasiDataController@count_sales_data');
Route::get('count/product', 'Api\Sistem\SinkronisasiDataController@count_product_data');
Route::get('count/stock', 'Api\Sistem\SinkronisasiDataController@count_stock_data');

Route::get('transaksi/data_transaksi', 'Api\Sistem\SinkronisasiDataController@data_transaksi');
Route::get('transaksi/data_product', 'Api\Sistem\SinkronisasiDataController@data_product');

Route::post('transaksi/upload_transaksi', 'Api\Sistem\SinkronisasiDataController@upload_transaksi');


 //END API FOR MOBILE ANDROID TANPA LOGIN

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('user', 'Auth\UserController@getAuthenticatedUser');

    //Endpoint Dashboard
    Route::get('manager/dashboard', 'Api\Dashboard\DashboardManagerController@index');
    Route::get('stores/dashboard', 'Api\Dashboard\DashboardStoreController@index');
    Route::get('warehouse/dashboard', 'Api\Dashboard\DashboardWarehouseController@index');
    Route::get('keuangan/dashboard', 'Api\Dashboard\DashboardKeuanganController@index');
    Route::get('cs/dashboard', 'Api\Dashboard\DashboardCsController@index');
    Route::get('kasir/dashboard', 'Api\Dashboard\DashboardKasirController@index');
    Route::get('transaksi/tarik', 'Api\Dashboard\DashboardKasirController@tarik_transaksi');
    Route::post('transaksi/upload', 'Api\Dashboard\DashboardKasirController@upload_transaksi');
    Route::get('tarik/product', 'Api\Dashboard\DashboardKasirController@tarik_product');

    /*Endpoind Route Kategori
    */
    Route::get('category/list_parent', 'Api\Products\CategoryController@list_parent');
    Route::get('category/list_kategori', 'Api\Products\CategoryController@list_kategori');
    Route::get('category/list_kategori/{id}', 'Api\Products\CategoryController@list_kategori');
    Route::post('category/store', 'Api\Products\CategoryController@store');
    Route::post('category/update/{id}', 'Api\Products\CategoryController@update');
    Route::post('category/delete/{id}', 'Api\Products\CategoryController@delete');

    /**
     * Endpoint Route Brand dan unit */
    Route::get('unit/list', 'Api\Products\UnitController@list');
    Route::post('unit/store', 'Api\Products\UnitController@store');
    Route::post('unit/update/{id}', 'Api\Products\UnitController@update');
    Route::post('unit/delete/{id}', 'Api\Products\UnitController@delete');

    Route::get('brand/list', 'Api\Products\BrandController@list');
    Route::post('brand/store', 'Api\Products\BrandController@store');
    Route::post('brand/update/{id}', 'Api\Products\BrandController@update');
    Route::post('brand/delete/{id}', 'Api\Products\BrandController@delete');

     /*Endpoind Produk
    */
    Route::get('product/list', 'Api\Products\ProductController@list');
    Route::get('product/stock_store_alert', 'Api\Products\ProductController@list_stok_store_alert');
    Route::get('product/stock_store', 'Api\Products\ProductController@list_stok_store');
    Route::get('product/will_add_paket', 'Api\Products\ProductController@product_stok_store_stok_not_null_get');
    Route::get('product/will_edit_paket/{id}', 'Api\Products\PaketProductController@edit');

    Route::get('product/stock_gudang', 'Api\Products\ProductController@list_stok_gudang');
    Route::get('product/supplier_by_product/{id}', 'Api\Products\ProductController@getSupplierByProduct');
    Route::get('product/edit/{id}', 'Api\Products\ProductController@edit');
    Route::post('product/delete/{id}', 'Api\Products\ProductController@delete');
    Route::post('product/store', 'Api\Products\ProductController@store');
    Route::post('product/paket/store', 'Api\Products\PaketProductController@store');
    Route::post('product/paket/batal/{id}', 'Api\Products\PaketProductController@batal');
    Route::post('product/paket/update/{id}', 'Api\Products\PaketProductController@update');
    Route::post('product/paket/delete/{id}', 'Api\Products\PaketProductController@delete');

    Route::post('product/update/{id}', 'Api\Products\ProductController@update');
    Route::get('product/list_gambar_produk/{id}', 'Api\Products\ProductController@list_gambar_produk');
    Route::post('product/uploadGambar/{id}','Api\Products\ProductController@uploadGambar');
    Route::post('product/updateUploadGambar/{id}','Api\Products\ProductController@updateUploadGambar');
    Route::post('product/deleteGambar/{id}','Api\Products\ProductController@deleteGambar');

    /*Endpoind Route Order
    */
    Route::get('order/list', 'Api\Order\OrderController@index');
    Route::get('order/list_pengiriman', 'Api\Order\OrderController@list_pengiriman');

    Route::get('order/list_gudang', 'Api\Order\OrderController@listApprovedOrderGudang');

    Route::get('order/produkStore', 'Api\Order\ListProductController@pesananStore');
    Route::get('order/produkGudang', 'Api\Order\ListProductController@pesananGudang');
    Route::get('order/produkStoreGudang', 'Api\Order\ListProductController@pesananStoreGudang');

    Route::get('order/detail/{id}', 'Api\Order\OrderController@detail');
    Route::post('order/store', 'Api\Order\OrderController@store');
    Route::post('order/update/{id}', 'Api\Order\OrderController@update');
    Route::get('order/requestStore', 'Api\Order\OrderController@requestStore');

    Route::post('order/updateByKeuangan/{id}','Api\Order\OrderController@updateByKeuangan');
    Route::post('order/updateByWarehouse/{id}','Api\Order\OrderController@updateByWarehouse');
    Route::post('order/approveByPengiriman/{id}','Api\Order\OrderController@approvePengiriman');
    Route::post('order/approveOrderStore/{id}','Api\Order\OrderController@approveOrderStore');
    Route::post('order/approveOrderGudang/{id}','Api\Order\OrderController@approveOrderGudang');

    Route::post('order/confirmArrival/{id}','Api\Order\OrderController@confirmArrival');
    Route::get('order/gudang', 'Api\Order\OrderController@pesananGudang');

    /* Endpoint Route Purchase */
    Route::get('purchase/supplier','Api\Purchase\PurchaseSupplierControoler@supplier_order');
    Route::get('purchase/supplier_product_order/{id}', 'Api\Purchase\PurchaseSupplierControoler@supplier_product_order');
    Route::get('purchase/supplier_product_by_supplier/{id}', 'Api\Purchase\PurchaseSupplierControoler@supplier_product_by_supplier');

    Route::get('purchase/list', 'Api\Purchase\PurchaseController@index');
    Route::get('purchase/detail/{id}', 'Api\Purchase\PurchaseController@detail');
    Route::post('purchase/updateByWarehouse/{id}','Api\Purchase\PurchaseController@updateByWarehouse');
    Route::post('purchase/approvePurchase/{id}','Api\Purchase\PurchaseController@approvePurchase');

    Route::post('purchase/store', 'Api\Purchase\PurchaseController@store');
    Route::post('purchase/arrival/{id}','Api\Purchase\PurchaseController@arrival');

    /* Endpoint Route Supplier */
    Route::get('supplier/list','Api\Supplier\SupplierController@index');
    Route::post('supplier/store','Api\Supplier\SupplierController@store');
    Route::post('supplier/update/{id}','Api\Supplier\SupplierController@update');
    Route::post('supplier/delete/{id}','Api\Supplier\SupplierController@delete');
    Route::get('supplier/detail/{id}','Api\Supplier\SupplierController@detail');

    /* Endpoint Route User */
    Route::get('users/list', 'Api\Sistem\UserController@index');
    Route::get('users/edit/{id}', 'Api\Sistem\UserController@edit');
    Route::post('users/store','Api\Sistem\UserController@store');
    Route::post('users/update/{id}','Api\Sistem\UserController@update');
    Route::post('users/delete/{id}','Api\Sistem\UserController@delete');
    Route::post('sistem/createprinter','Api\Sistem\PrinterSetupController@create_printer');
    Route::get('sistem/getprinter', 'Api\Sistem\PrinterSetupController@index');
    Route::get('struk/cetak/{id}', 'Api\Sistem\PrinterSetupController@struk');

    /* Stock Route */
    Route::get('stock/ready/{id}', 'Api\Stock\StockController@ready');
    Route::get('stock/detail/{id}', 'Api\Stock\StockController@detail');
    Route::get('stock/stock_gudang', 'Api\Stock\StockController@stockGudang');

    /* Sales Route */
    Route::get('sales/list', 'Api\Sales\SalesController@index');
    Route::get('sales/customer', 'Api\Sales\SalesController@customer');
    Route::get('sales/list_pembayaran', 'Api\Sales\SalesController@list_pembayaran');
    Route::post('sales/konfirmasi_pembayaran/{id}', 'Api\Sales\SalesController@konfirmasi_pembayaran');
    Route::post('sales/update_transaction/{id}', 'Api\Sales\SalesController@update_transaction');
    Route::get('sales/detail/{id}', 'Api\Sales\SalesController@detail');
    Route::post('sales/store', 'Api\Sales\SalesController@store');

    /* Endpoint Route Add CUstomer */
    Route::get('customer/list','Api\Customer\CustomerController@index');
    Route::post('customer/store','Api\Customer\CustomerController@store');
    Route::post('customer/update/{id}','Api\Customer\CustomerController@update');
    Route::post('customer/delete/{id}','Api\Customer\CustomerController@delete');

    /* Endpoint banner */
    Route::get('banner/list', 'Api\Marketing\BannerController@list');
    Route::post('banner/store', 'Api\Marketing\BannerController@store');
    Route::post('banner/update/{id}', 'Api\Marketing\BannerController@update');
    Route::post('banner/delete/{id}', 'Api\Marketing\BannerController@delete');

    /* Endpoint voucher */
    Route::get('voucher/list', 'Api\Marketing\VoucherController@list');
    Route::post('voucher/store', 'Api\Marketing\VoucherController@store');
    Route::post('voucher/update/{id}', 'Api\Marketing\VoucherController@update');
    Route::post('voucher/delete/{id}', 'Api\Marketing\VoucherController@delete');

    /* Bank Account */
    Route::get('bank/list', 'Api\Sistem\BankController@list');
    Route::post('bank/store', 'Api\Sistem\BankController@store');
    Route::post('bank/update/{id}', 'Api\Sistem\BankController@update');
    Route::get('bank/edit/{id}', 'Api\Sistem\BankController@edit');
    Route::post('bank/delete/{id}', 'Api\Sistem\BankController@delete');

    /* Setting Tarif */
    Route::get('sistem/tarif/list', 'Api\Sistem\RateShippingController@list');
    Route::post('sistem/tarif/update/{id}', 'Api\Sistem\RateShippingController@update');

    //inbox
    Route::get('inbox/list', 'Api\Inbox\InboxController@list_to_admin');
    Route::get('inbox/chat/{customer}/{product}', 'Api\Inbox\InboxController@detail_inbox');
    Route::post('inbox/store_admin/{customer}/{product}', 'Api\Inbox\InboxController@store_admin');

    //end api bahtera

    //START API FOR MOBILE ANDROID
    Route::get('account', 'Api\Sistem\AccountController@getAccount');
    Route::post('commentar/create', 'Api\Products\ProductForCustomerController@commentar_create');
    Route::post('rating/create', 'Api\Products\ProductRatingController@rating_create');
    Route::get('rating/list', 'Api\Products\ProductRatingController@getRating');
    Route::get('transaction', 'Api\Sales\SalesForCustomerController@index');
    Route::get('transaction/detail/{id}', 'Api\Sales\SalesForCustomerController@detail');
    Route::post('transaction/store', 'Api\Sales\SalesForCustomerController@store');
    Route::post('transaction/bayar', 'Api\Sales\SalesForCustomerController@bayar');
    Route::post('transaction/batal', 'Api\Sales\SalesForCustomerController@batal');
    Route::post('transaction/terima', 'Api\Sales\SalesForCustomerController@terima');
    Route::post('changepassword', 'Api\Sistem\AccountController@changepassword');
    Route::get('inbox/product/comment/{id}', 'Api\Inbox\InboxController@detail');
    Route::get('inbox/product/list', 'Api\Inbox\InboxController@list');
    Route::post('inbox/product/store', 'Api\Inbox\InboxController@store');


});
