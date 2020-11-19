<?php

namespace App\Http\Controllers\Api\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products\ProductStock;
use App\Http\Resources\Products\StockList as StockResource;
use App\Http\Resources\Products\StockItem as StockItem;
use App\Models\Products\ProductStockExpired;
use App\Models\Products\Product;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function ready(Request $request,$source=2)
    {
       if($source=="g"){
           $source="2";
       }else
        if($source=="s"){
            $source="1";
        }

    $stock = DB::table('product_stock as s')
        ->join('product as p', 'p.id', '=', 's.product_id')
        ->where('s.source',$source)
        ->get();
    //    $stock = ProductStock::orderBy('stock','asc')
    //                         ->where('source',$source)->get();

       return response()->json([
        'success' => true,
        'stocks' =>  $stock
       ],200);
    }

    public function detail($id)
    {
       $stock = ProductStock::orderBy('stock','asc')
                            ->where('product_id',$id)
                            ->where('source',2)
                            ->first();
       return response()->json([
             'stocks'=>new StockItem($stock),
             'detail'=>ProductStockExpired::where('product_id',$id)->where('source',2)->get()
       ],200);
    }

    public function stockGudang(Request $request)
    {
        $products = DB::table('product')
        ->leftJoin('product_stock', 'product.id', '=', 'product_stock.product_id','product_stock.source','=','2')//1 is stock store
        ->leftJoin('category', 'product.category_id', '=', 'category.id')
        ->select('product.id','product.sku','product.barcode','product.name',
                'product.alert_quantity_warehouse as alert_quantity','category.name as category',
                'product.brand','product.price','product.price_modal',
                'product.thumbnail',
                DB::raw('(CASE WHEN product_stock.stock IS NULL THEN 0 ELSE product_stock.stock END) AS stock')
                )
        ->where('product_stock.stock','=',1)
        ->orderBy('product_stock.stock','asc')
        ->get();

        return response()->json([
            'stocks'=>$products
      ],200);
    }

}
