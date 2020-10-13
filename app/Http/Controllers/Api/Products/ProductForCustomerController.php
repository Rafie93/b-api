<?php

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Products\Category;
use App\Models\Products\Product;
use App\Models\Products\ProductStock;
use App\Models\Products\ProductComment;
use App\Models\Products\ProductImage;
use App\Models\Products\ProductVariant;
use App\Http\Resources\Products\ProductCustomerList as ProductResource;
use App\Http\Resources\Products\ProductCustomerItem as ProductResourceItem;

class ProductForCustomerController extends Controller
{
    public function getProducts(Request $request)
    {
        $offset = $request->offset!=null ? $request->offset : 0;
        $limit = $request->limit!=null ? $request->limit : 50;
        $products = Product::where('product.is_active',1)
                    ->when($request->productType, function ($query) use ($request){
                        $query->where('product_type', '=', "{$request->productType}");
                    })
                    ->leftJoin('product_stock', function($join){
                         $join->on('product.id', '=', 'product_stock.product_id')
                            ->where('product_stock.source', '=', '1');
                     })->withCount([
                        'sale_detail as terjual' => function($query) {
                            $query->select(DB::raw('SUM(quantity)'));
                        }
                    ])
                     ->when($request->filterKategori, function ($query) use ($request) {
                        $categoryCheck = Category::where('id',$request->filterKategori)->first();
                        if($categoryCheck->parent_id==null){
                            $all_category =  Category::select('id')->where('parent_id',$request->filterKategori);
                            $query->whereIn('category_id', $all_category->toArray());
                        }else{
                            $query->where('category_id', '=', "{$request->filterKategori}");
                        }
                    })
                    ->when($request->keyword, function ($query) use ($request) {
                        $query->where('name', 'like', "%{$request->keyword}%")
                                ->orWhere('barcode','like', "%{$request->keyword}%");
                    });


        if($request->product_sorting == "1"){ //nama
            $products = $products->orderBy('product.name','asc');
        }else if($request->product_sorting=="2"){ // terbaru
            $products = $products->orderBy('product.id','desc');
        }else if($request->product_sorting=="3"){ // terlaris
            $products = $products->orderBy('terjual','desc');
        }else if($request->product_sorting=="4"){ // termurah
            $products = $products->orderBy('product.price','asc');
        }else if($request->product_sorting=="5"){ // termahal
            $products = $products->orderBy('product.price','desc');
        }else{
            $products = $products->orderBy('product.id','asc');
        }

        $total_utama = $products->get()->count();
        $products = $products->skip($offset)->take($limit)->get();
        return response()->json([
            'success' => true,
            'total_data' => $total_utama,
            'total_show'=> $products->count(),
            'products' => new ProductResource($products)

           ],200);
    }

    public function getProductDetail(Request $request,$id)
    {
        $product = Product::where('id',$id)->withCount([
                            'sale_detail as terjual' => function($query) {
                                $query->select(DB::raw('SUM(quantity)'));
                            }
                        ])->first();
        $stock = ProductStock::where('product_id',$id)->where('source',1)->first();
        $image = ProductImage::where('product_id',$id)->get();
        return response()->json([
            'success' => true,
            'product' => new ProductResourceItem($product),
            'stock' => $stock->stock,
            'image' => $image,
            'variant' => ProductVariant::where('product_id',$id)->get()
           ],200);
    }

    public function getCommentar(Request $request,$id)
    {
        $commentar = ProductComment::where('product_id',$id)->get();
        return response()->json([
            'success' => true,
            'commentars' => $commentar
           ],200);
    }

    public function commentar_create(Request $request)
    {
        ProductComment::create([
            'product_id'=>$request->id,
            'commentar' => $request->pesan,
            'creator_id' => auth()->user()->id,
            'to' => 'admin',
            'type' => 'user',
            'is_read' => 0
        ]);

        return response()->json([
            'success' =>true
        ], 200);
    }
}
