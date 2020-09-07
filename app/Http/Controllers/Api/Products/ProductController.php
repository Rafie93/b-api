<?php

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use App\Models\Products\ProductQuery;
use App\Http\Resources\Products\ProductList as ProductResource;
use App\Http\Resources\Supplier\SupplierProductList as SupplierResource;

use App\Models\Products\SupplierProduct;
class ProductController extends Controller
{
    public $user;
    public function __construct(ProductQuery $queryObject)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $this->queryObject = $queryObject;
    }
    public function list(Request $request)
    {
        $products = $this->queryObject->product_get($request);
        return response()->json([
            'success' => true,
            'product' =>   new ProductResource($products)
           ],200);
    }

    public function list_stok_store(Request $request)
    {
        $products = $this->queryObject->product_stok_store_get($request);
        return response()->json([
            'success' => true,
            'product' =>   $products
           ],200);
    }

    public function list_stok_gudang(Request $request)
    {
        $products = $this->queryObject->product_stok_gudang_get($request);
        return response()->json([
            'success' => true,
            'product' =>   $products
           ],200);
    }
    public function edit(Request $request,$id)
    {
        $products = $this->queryObject->product_getById($id);
        return response()->json(
            $products->first(),200);
    }

    public function getSupplierByProduct($id)
    {
        $supplierProduct = SupplierProduct::where('product_id',$id)->get();
        return response()->json([
            'success' => true,
            'supplier'=>new SupplierResource($supplierProduct)
        ],200);
    }

    public function store(Request $request)
    {
       $products = $this->queryObject->product_store($request,$this->user->id);
        return response()->json([
            'success' => true,
            'product' =>   $products
           ],200);
    }

    public function update(Request $request,$id)
    {
       $products = $this->queryObject->product_update($request,$id,$this->user->id);
        return response()->json([
            'success' => true,
            'product' =>   $products
           ],200);
    }

    public function delete($id)
    {
        $cek = $this->queryObject->product_delete($id);
        if($cek){
            return response()->json([
                'success' => false,
                'message' => "Produk Tidak Bisa di hapus karena digunakan"
               ],400);
        }else{
            return response()->json([
                'success' => true,
                'message' =>  "Produk Berhasil dihapus"
               ],200);
        }
    }

}
