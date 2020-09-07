<?php

namespace App\Models\Products;
use App\Models\Products\Product;
use App\Models\Products\ProductStockHistory;
use App\Models\Products\ProductStockExpired;
use App\Models\Products\Brand;
use App\Models\Products\Unit;
use App\Models\Products\Category;
use App\Models\Products\ProductVariant;
use App\Models\Products\ProductStock;
use Illuminate\Support\Facades\DB;
use App\Models\Sales\SaleDetail;
use App\Models\Orders\OrderDetail;
use App\Models\Products\SupplierProduct;

class ProductQuery
{
    public function product_get($request)
    {
       return Product::all();
    }
    public function product_stok_store_get($request)
    {
        $products = DB::table('product')
                    ->leftJoin('product_stock', function($join){
                        $join->on('product.id', '=', 'product_stock.product_id')
                             ->where('product_stock.source', '=', '1');
                     })
                     ->leftJoin('product_stock as stock_gudang', function($join){
                        $join->on('product.id', '=', 'stock_gudang.product_id')
                             ->where('stock_gudang.source', '=', '2');
                     })
                    ->leftJoin('category', 'product.category_id', '=', 'category.id')
                    ->select('product.id','product.sku','product.barcode','product.name',
                            'product.alert_quantity','category.name as category',
                            'product.brand','product.price','product.price_modal',
                            'product.thumbnail',
                                DB::raw('(CASE WHEN product_stock.stock IS NULL THEN 0 ELSE product_stock.stock END) AS stock'),
                                DB::raw('(CASE WHEN stock_gudang.stock IS NULL THEN 0 ELSE stock_gudang.stock END) AS stock_gudang')
                            )
                            ->where('product.is_active',1);

        if($request->product_sorting==1){
            $products = $products->orderBy('product.name','asc')
                                 ->get();
        }else{
            $products = $products->where('product.product_type',1)
                                 ->orderBy('product_stock.stock','asc')
                                 ->get();
        }
        return $products;
    }


    public function product_stok_gudang_get($request)
    {
        $products = DB::table('product')
                    ->leftJoin('product_stock', function($join){
                        $join->on('product.id', '=', 'product_stock.product_id')
                             ->where('product_stock.source', '=', '2');
                     })
                    ->leftJoin('category', 'product.category_id', '=', 'category.id')
                    ->select('product.id','product.sku','product.barcode','product.name',
                            'product.alert_quantity','category.name as category',
                            'product.brand','product.price','product.price_modal',
                            'product.thumbnail',
                            DB::raw('(CASE WHEN product_stock.stock IS NULL THEN 0 ELSE product_stock.stock END) AS stock')
                            )
                     ->where('product.is_active',1)
                     ->where('product.product_type',1)
                    ->orderBy('product_stock.stock','asc')
                    ->get();
        return $products;
    }
    public function product_getById($id)
    {
        return Product::where('id',$id)->get();
    }
    public function product_delete($id)
    {
        $sale = SaleDetail::where('product_id',$id)->get()->count();
        $order = OrderDetail::where('product_id',$id)->get()->count();
        if(($sale > 0) || ($order > 0)){
            return true;
        }else{
            $products = Product::find($id);
            $products->delete();
            ProductStock::where('product_id',$id)->delete();
            ProductStockHistory::where('product_id',$id)->delete();
            ProductStockExpired::where('product_id',$id)->delete();
            SupplierProduct::where('product_id',$id)->delete();
            return false;
        }
    }

    public function product_store($request,$userId)
    {
        $sku = $request->sku;
        $category_id = $request->category_id;
        if($sku=="") $sku = $this->auto_sku($request->brand);
        if($category_id=="") $category_id = 2;

        if ($request->hasFile('file')) {
            $requestData = $request->data;
            $someRequest = json_decode($requestData, true);
            $request->merge($someRequest);
            $request->merge(['sku'=>$requestData->sku=="" ? $this->auto_sku($requestData->brand) : $requestData->sku,
                                'barcode_type'=>'128', 'creator_id' => $userId
                            ]);

            try{
                DB::beginTransaction();
                $product = Product::create($request->all());
                $foto = $request->file('file');
                $fileName = $foto->getClientOriginalName();
                $request->file('file')->move('images/product/'.$product->id,$fileName);
                $fotoUpdate = Product::where('id',$product->id)->update(['thumbnail' => $fileName]);
                if($request->stock!=0 || $request->stock!=""){
                    ProductStock::create([
                        'product_id' => $product->id,
                        'stock' => $requestData->stock,
                        'unit'=> $requestData->purchase_unit,
                        'source'=>2
                    ]);
                }
                $suppliers = $requestData->supplier;
                foreach($suppliers as $s){
                    $supplier_id = $s["supplier_id"];
                    SupplierProduct::create([
                        'supplier_id'=>$supplier_id,
                        'product_id' => $product->id
                    ]);
                }
                DB::commit();
            }catch (\PDOException $e) {
                DB::rollBack();
            }
       }else{
            $request->merge(['sku'=>$sku,'barcode_type'=>'128','category_id'=>$category_id,'creator_id' => $userId]);
            try{
                DB::beginTransaction();
                    $product = Product::create($request->all());
                    if($request->stock!=0 || $request->stock!=""){
                        ProductStock::insert([
                            'product_id' => $product->id,
                            'stock' => $request->stock,
                            'unit'=> $request->purchase_unit,
                            'source'=>2
                        ]);
                        ProductStockHistory::insert([
                            'date' => date('Y-m-d H:i:s'),
                            'product_id'=>$product->id,
                            'unit'  => $request->purchase_unit,
                            'quantity' => $request->stock,
                            'source' => 2,
                            'ref_code' => "STOCK_AWAL",
                        ]);
                    }
                    $suppliers = $request->supplier;
                    foreach($suppliers as $s){
                        $supplier_id = $s["supplier_id"];
                        SupplierProduct::create([
                            'supplier_id'=>$supplier_id,
                            'product_id' => $product->id
                        ]);
                    }
                DB::commit();
            }catch (\PDOException $e) {
                DB::rollBack();
            }

       }
       return $product;
    }

    public function product_update($request,$id,$userId)
    {
        $sku = $request->sku;
        $category_id = $request->category_id;
        if($sku=="") $sku = $this->auto_sku($request->brand);
        if($category_id=="") $category_id = 2;

        if ($request->hasFile('file')) {
            $requestData = $request->data;
            $someRequest = json_decode($requestData, true);
            $request->merge($someRequest);
            $request->merge(['sku'=>$requestData->sku=="" ? $this->auto_sku($requestData->brand) : $requestData->sku,
                                'barcode_type'=>'128','creator_id' => $userId
                            ]);
            try{
                DB::beginTransaction();
                    $product = Product::find($id);
                    $product->update($request->all());
                    $foto = $request->file('file');
                    $fileName = $foto->getClientOriginalName();
                    $request->file('file')->move('images/product/'.$product->id,$fileName);
                    $fotoUpdate = Product::where('id',$product->id)->update(['thumbnail' => $fileName]);

                    $suppliers = $requestData->supplier;
                    $sp = SupplierProduct::where('product_id',$id)->delete();
                    foreach($suppliers as $s){
                        $supplier_id = $s["supplier_id"];
                        SupplierProduct::create([
                            'supplier_id'=>$supplier_id,
                            'product_id' => $product->id
                        ]);
                    }
                DB::commit();
            }catch (\PDOException $e) {
                DB::rollBack();
            }
       }else{
            $request->merge(['sku'=>$sku,'barcode_type'=>'128','category_id'=>$category_id,'creator_id' => $userId]);
            try{
                DB::beginTransaction();
                    $product = Product::find($id);
                    $product->update($request->all());

                    $suppliers = $request->supplier;
                    $sp = SupplierProduct::where('product_id',$id)->delete();
                    foreach($suppliers as $s){
                        $supplier_id = $s["supplier_id"];
                        SupplierProduct::create([
                            'supplier_id'=>$supplier_id,
                            'product_id' => $product->id
                        ]);
                    }
                DB::commit();
            }catch (\PDOException $e) {
                DB::rollBack();
            }
       }
       return $product;
    }

    public function auto_sku($brand)
    {
        $codeAwal = substr($brand,0,3).'-PRD-';
        $getProduct = Product::where('sku','LIKE','%'.$codeAwal.'%')->get();
        $id = count($getProduct);
        $id = $id+1;
        if($id < 10) $no = "000".$id;
        elseif($id < 100) $no = "00".$id;
        elseif($id < 1000) $no = "0".$id;
        else $no = $id;

        $sku = $codeAwal.$no;
        return strtoupper($sku);
    }

      // public function product_update($request,$id)
    // {
    //     $sku = $request->sku;
    //     $category_id = $request->category_id;
    //     if($sku=="") $sku = $this->auto_sku($request->brand);
    //     if($category_id=="") $category_id = 2;

    //     $request->merge(['sku'=>$sku,'barcode_type'=>'128','category_id'=>$category_id]);
    //     $product = Product::update($request->all());
    //     if ($request->hasFile('file')) {
    //         $foto = $request->file('file');
    //         $fileName = $foto->getClientOriginalName();
    //         $request->file('file')->move('images/product/'.$product->id,$fileName);
    //         $fotoUpdate = Product::where('id',$product->id)->update(['thumbnail' => $fileName]);
    //    }
    //    return $product;
    // }

}
