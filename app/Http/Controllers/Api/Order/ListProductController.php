<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders\Order;
use App\Models\Orders\OrderDetail;
use App\Models\Products\ProductStock;
use App\Http\Resources\Orders\ProductList as Resource;
use Illuminate\Support\Facades\DB;

class ListProductController extends Controller
{
    public function pesananStore(Request $request)
    {
        $product = DB::table('order_product_detail')
            ->select('order_product_detail.product_id',
                'product.sku','product.barcode','product.name',
                'order_product_detail.unit',
                DB::raw('SUM(order_product_detail.quantity_order) as quantity_order'))
            ->groupBy('order_product_detail.product_id','order_product_detail.unit','product.sku','product.barcode','product.name')
            ->leftJoin('order_product', 'order_product.id', '=', 'order_product_detail.order_product_id')
            ->leftJoin('product', 'product.id', '=', 'order_product_detail.product_id')
            ->where('order_product_detail.status',1)
            ->where('order_product.type',1)
            ->orderBy('order_product_detail.product_id','asc')
            ->get();

        // $product = OrderDetail::orderBy('order_product_id','asc')
        //                     ->where('status',1)->get();
        return response()->json([
            'success' => true,
            'orders' => $product
           ],200);
    }
    public function pesananStoreGudang(Request $request)
    {
        $product = DB::table('product')
            ->select('product.product_id',
                'product.sku','product.barcode','product.name',
                'order_product_detail.unit',
                DB::raw('SUM(order_product_detail.quantity_order) as quantity_order'))
            ->groupBy('product.product_id','order_product_detail.unit','product.sku','product.barcode','product.name')
            ->leftJoin('order_product_detail','order_product_detail.product_id','=','product.id')
            ->leftJoin('order_product', 'order_product.id', '=', 'order_product_detail.order_product_id')
            ->where('order_product_detail.status',1)
            ->where('order_product.type',1)
            ->orderBy('order_product_detail.product_id','asc')
            ->get();

        // $product = OrderDetail::orderBy('order_product_id','asc')
        //                     ->where('status',1)->get();
        return response()->json([
            'success' => true,
            'orders' => $product
           ],200);
    }
    public function pesananGudang(Request $request)
    {
        $product = DB::table('order_product_detail')
            ->select('order_product_detail.product_id',
                'product.sku','product.barcode','product.name','product.price','product.price_modal',
                'order_product_detail.unit',
                DB::raw('SUM(order_product_detail.quantity_order) as quantity_order'))
            ->groupBy('order_product_detail.product_id','order_product_detail.unit',
            'product.sku','product.barcode','product.name','product.price','product.price_modal')
            ->leftJoin('order_product', 'order_product.id', '=', 'order_product_detail.order_product_id')
            ->leftJoin('product', 'product.id', '=', 'order_product_detail.product_id')
            ->where('order_product_detail.status',1)
            ->where('order_product.type',2)
            ->orderBy('order_product_detail.product_id','asc')
            ->get();

        // $product = OrderDetail::orderBy('order_product_id','asc')
        //                     ->where('status',1)->get();
        return response()->json([
            'success' => true,
            'orders' => $product
           ],200);
    }
}
