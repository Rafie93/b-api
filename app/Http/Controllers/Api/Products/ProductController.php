<?php

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use App\Models\Products\ProductQuery;
use App\Models\Products\ProductImage;
use App\Models\Products\Product;
use App\Http\Resources\Products\ProductItem as ProductItem;
use App\Http\Resources\Products\ProductList as ProductResource;
use App\Http\Resources\Products\GambarList as GambarListResource;
use App\Http\Resources\Supplier\SupplierProductList as SupplierResource;
use File;
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
    public function product_stok_store_stok_not_null_get(Request $request)
    {
        $products = $this->queryObject->product_stok_store_stok_not_null_get($request);
        return response()->json([
            'success' => true,
            'product' =>   $products
           ],200);
    }
    public function list_stok_store_alert(Request $request)
    {
        $products = $this->queryObject->product_stok_store_alert_get($request);
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
        $products = $this->queryObject->product_getById($id)->first();
        return response()->json(new ProductItem($products),200);
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
            'product' =>  $products
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
            $path = 'images/product/'.$id;
            File::deleteDirectory(public_path($path));

            return response()->json([
                'success' => true,
                'message' =>  "Produk Berhasil dihapus"
               ],200);
        }
    }

    // UPLOAD GAMBAR
    public function list_gambar_produk(Request $request,$id)
    {
        $gambars = ProductImage::where('product_id',$id)->get();
        return response()->json([
            'success' => true,
            'gambars' => new GambarListResource($gambars)
           ],200);
    }

    public function uploadGambar(Request $request,$id)
    {

        if ($request->hasFile('file')) {
            $requestData = $request->data;
            $someRequest = json_decode($requestData, true);

            $imageData = ProductImage::create([
                'product_id'=>$id
            ]);
            $foto = $request->file('file');
            $fileName = $foto->getClientOriginalName();
            $request->file('file')->move('images/product/'.$id,$fileName);
            $fotoUpdate = ProductImage::where('id',$imageData->id)
                                        ->update(['image' => $fileName]);

            $product = Product::where('id',$id)->first();
            $thumbnail = $product->thumbnail;
            if($thumbnail==null){
                $product->update(['thumbnail'=>$fileName]);
            }
            return response()->json([
                'success'=>true,
                'message'=>'Foto Berhasil diupload'
             ], 200);

          }else{
            return response()->json([
                'success'=>false,
                'message'=>'Tidak ada image yang diupload'
            ], 400);
          }
    }
    public function updateUploadGambar(Request $request,$id)
    {

        if ($request->hasFile('file')) {
            $requestData = $request->data;
            $someRequest = json_decode($requestData, true);
            $imageData = ProductImage::where('id',$id)->first();

            $foto = $request->file('file');
            $fileName = $foto->getClientOriginalName();
            $request->file('file')->move('images/product/'.$imageData->product_id,$fileName);
            $imageData->update(['image' => $fileName]);

            $product = Product::where('id',$imageData->product_id)->first();
            $thumbnail = $product->thumbnail;
            if($thumbnail==null){
                $product->update(['thumbnail'=>$fileName]);
            }else{
                $aktifkanThumbnail = $someRequest['thumbnail'];
                if($aktifkanThumbnail=="Ya"){
                    $product->update(['thumbnail'=>$fileName]);
                }
            }
            return response()->json([
                'success'=>true,
                'message'=>'Foto Berhasil diupload'
             ], 200);

          }else{
            return response()->json([
                'success'=>false,
                'message'=>'Tidak ada image yang diupload'
            ], 400);
          }
    }
    public function deleteGambar(Request $request,$id)
    {
        $productImage = ProductImage::find($id);
        $path = 'images/product/'.$productImage->product_id.'/';
        File::delete($path.$productImage->image);
        $productImage->delete();
        return response()->json([
            'success' => true,
            'message' =>  "Gambar Berhasil dihapus"
           ],200);
    }

}
