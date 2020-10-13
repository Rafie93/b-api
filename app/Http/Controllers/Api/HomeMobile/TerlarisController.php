<?php

namespace App\Http\Controllers\Api\HomeMobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Products\Product;
use App\Http\Resources\Products\ProductCustomerList as ProductResource;

class TerlarisController extends Controller
{
    public function getProductTerlaris(Request $request)
    {
        $products = Product::where('product.is_active',1)
                    ->when($request->productType, function ($query) use ($request){
                        $query->where('product_type', '=', "{$request->productType}");
                    })
                    ->withCount([
                        'sale_detail as terjual' => function($query) {
                            $query->select(DB::raw('SUM(quantity)'));
                        }
                    ])

                    ->orderBy('terjual','desc')
                    ->take(20)
                    ->get();

        $total_utama = $products->count();
        return response()->json([
            'success' => true,
            'total_data' => $total_utama,
            'products' => new ProductResource($products)

           ],200);
    }

}
