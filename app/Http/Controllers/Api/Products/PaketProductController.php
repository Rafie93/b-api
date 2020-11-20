<?php

namespace App\Http\Controllers\Api\Products;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products\Product;
use App\Models\Products\PaketPromoDetail;
use App\Models\Products\ProductStock;
use App\Http\Resources\Products\PromoItem as PromoItem;
use App\Http\Resources\Products\PromoList as PromoList;
use Illuminate\Support\Facades\DB;
use App\Models\Products\ProductStockHistory;
use JWTAuth;
use App\Models\Sales\SaleDetail;
class PaketProductController extends Controller
{
    public $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            $requestData = $request->data;
            $someRequest = json_decode($requestData, true);
            $category_id = $request->category_id;
            if($category_id=="") $category_id = 2;

            $request->merge($someRequest);
            $request->merge(['product_type'=>2,'sku'=>$request->sku=="" ? $this->auto_sku($request->name) : $request->sku,
                                'barcode_type'=>'128', 'creator_id' => $this->user->id,'category_id'=>$category_id
                            ]);
            try{
                DB::beginTransaction();
                    $product = Product::create($request->all());
                    if($request->stock!=0 || $request->stock!=""){
                        ProductStock::insert([
                            'product_id' => $product->id,
                            'stock' => $request->stock,
                            'unit'=> 'PAKET',
                            'source'=>1
                        ]);
                        ProductStockHistory::insert([
                            'date' => date('Y-m-d H:i:s'),
                            'product_id'=>$product->id,
                            'unit'  => 'PAKET',
                            'quantity' => $request->stock,
                            'source' => 1,
                            'ref_code' => "STOCK_AWAL",
                        ]);
                    }

                    $pakets = $request->paket;
                    foreach($pakets as $s){
                        $product_id = $s["product_id"];
                        $quantity = $s["quantity"];
                        PaketPromoDetail::create([
                            'product_id'=>$product_id,
                            'paket_product_id' => $product->id,
                            'quantity' =>  $quantity
                        ]);
                        $invalid = $this->pengurangan_stock($product_id,$quantity,$request->stock,$product->id);
                        if($invalid){
                            DB::rollBack();
                            return response()->json([
                                'success' => false,
                                'message' =>  "Oopps, stok di toko tidak cukup untuk dijadikan stok promo"
                            ],400);
                        }
                    }
                    $foto = $request->file('file');
                    $fileName = $foto->getClientOriginalName();
                    $request->file('file')->move('images/product/'.$product->id,$fileName);
                    $fotoUpdate = Product::where('id',$product->id)->update(['thumbnail' => $fileName]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'product' =>  $product
                ],200);
            }catch (\PDOException $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' =>  "Gagal membuat paket produk"
                ],400);
            }


        }else{
            $sku = $request->sku;
            $category_id = $request->category_id;
            if($sku=="") $sku = $this->auto_sku($request->name);
            if($category_id=="") $category_id = 2;

            $request->merge(['product_type'=>2,'sku'=>$sku,'barcode_type'=>'128','category_id'=>$category_id,'creator_id' => $this->user->id]);
            try{
                DB::beginTransaction();
                    $product = Product::create($request->all());
                    if($request->stock!=0 || $request->stock!=""){
                        ProductStock::insert([
                            'product_id' => $product->id,
                            'stock' => $request->stock,
                            'unit'=> 'PAKET',
                            'source'=>1
                        ]);
                        ProductStockHistory::insert([
                            'date' => date('Y-m-d H:i:s'),
                            'product_id'=>$product->id,
                            'unit'  => 'PAKET',
                            'quantity' => $request->stock,
                            'source' => 1,
                            'ref_code' => "STOCK_AWAL",
                        ]);
                    }

                    $pakets = $request->paket;
                    foreach($pakets as $s){
                        $product_id = $s["product_id"];
                        $quantity = $s["quantity"];
                        PaketPromoDetail::create([
                            'product_id'=>$product_id,
                            'paket_product_id' => $product->id,
                            'quantity' =>  $quantity
                        ]);
                    $invalid = $this->pengurangan_stock($product_id,$quantity,$request->stock,$product->id);
                    if($invalid){
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' =>  "Oopps, stok di toko tidak cukup untuk dijadikan stok promo"
                        ],400);
                    }

                    }
                DB::commit();
                return response()->json([
                    'success' => true,
                    'product' =>  $product
                ],200);
            }catch (\PDOException $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' =>  "Gagal membuat paket produk"
                ],400);
            }
        }

    }
    public function pengurangan_stock($product_id,$quantity,$stock_paket,$paketId)
    {
       $stock_after = $quantity * $stock_paket;
       $stock_toko = ProductStock::orderBy('stock','desc')
                    ->where('product_id',$product_id)
                    ->where('source',1)
                    ->first()
                    ->stock;
       if($stock_after > $stock_toko){
           return true;
       }else{
           $newStock = $stock_toko - $stock_after;
           ProductStock::orderBy('stock','desc')
                    ->where('product_id',$product_id)
                    ->where('source',1)
                    ->update([
                        'stock' => $newStock
                    ]);
            ProductStockHistory::insert([
                'date' => date('Y-m-d H:i:s'),
                'product_id' => $product_id,
                'quantity' => 0 - $stock_after,
                'unit' => 'Pcs',
                'source' => 1,
                'ref_code' => 'PAKET_ID:'.$paketId
            ]);
           return false;
       }
    }

    public function return_stock($product_id,$quantity,$stock_paket,$paketId)
       {
        $stock_after = $quantity * $stock_paket;
        $stock_toko = ProductStock::orderBy('stock','desc')
                     ->where('product_id',$product_id)
                     ->where('source',1)
                     ->first()
                     ->stock;

        $newStock = $stock_toko + $stock_after;
        ProductStock::orderBy('stock','desc')
                    ->where('product_id',$product_id)
                    ->where('source',1)
                    ->update([
                        'stock' => $newStock
                    ]);
         ProductStockHistory::insert([
                'date' => date('Y-m-d H:i:s'),
                'product_id' => $product_id,
                'quantity' => $stock_after,
                'unit' => 'Pcs',
                'source' => 1,
                'ref_code' => 'PAKET_ID:'.$paketId
            ]);


    }

    public function update(Request $request,$productId)
    {
        if ($request->hasFile('file')) {
            $requestData = $request->data;
            $someRequest = json_decode($requestData, true);
            $category_id = $request->category_id;
            if($category_id=="") $category_id = 2;

            $request->merge($someRequest);
            $request->merge(['product_type'=>2,'sku'=>$request->sku=="" ? $this->auto_sku($request->name) : $request->sku,
                                'barcode_type'=>'128', 'creator_id' => $this->user->id,'category_id'=>$category_id
                            ]);
            try{
                DB::beginTransaction();
                    $product = Product::find($productId);
                    $product->update($request->all());

                    if($request->stock!=0 || $request->stock!=""){
                        $stockDataStore = [
                            'product_id'      => $productId,
                            'source'          => 1,
                            'stock'           => $request->stock,
                            'unit'            => 'PAKET',
                            'created_at'       => date('Y-m-d H:i:s')
                        ];
                        DB::table('product_stock')->updateOrInsert([
                            'product_id' => $productId,
                            'source' => 1,
                        ],$stockDataStore);
                    }

                    $pakets = $request->paket;
                    foreach($pakets as $s){
                        $product_id = $s["product_id"];
                        $quantity = $s["quantity"];

                        $paketPromoDataStore = [
                            'product_id'      => $product_id,
                            'paket_product_id'       => $productId,
                            'quantity'           => $quantity,
                        ];

                        DB::table('paket_promo_product_detail')->updateOrInsert([
                            'product_id' => $product_id,
                            'paket_product_id' => $productId,
                            'source' => 1,
                        ],$paketPromoDataStore);

                        // PaketPromoDetail::create([
                        //     'product_id'=>$product_id,
                        //     'paket_product_id' => $product->id,
                        //     'quantity' =>  $quantity
                        // ]);

                        // $invalid = $this->pengurangan_stock($product_id,$quantity,$request->stock,$product->id);
                        // if($invalid){
                        //     DB::rollBack();
                        //     return response()->json([
                        //         'success' => false,
                        //         'message' =>  "Oopps, stok di toko tidak cukup untuk dijadikan stok promo"
                        //     ],400);
                        // }
                    }
                    $foto = $request->file('file');
                    $fileName = $foto->getClientOriginalName();
                    $request->file('file')->move('images/product/'.$productId,$fileName);
                    $fotoUpdate = Product::where('id',$productId)->update(['thumbnail' => $fileName]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'product' =>  $product
                ],200);
            }catch (\PDOException $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' =>  "Gagal memperbaharui paket produk"
                ],400);
            }


        }else{
            $sku = $request->sku;
            $category_id = $request->category_id;
            if($sku=="") $sku = $this->auto_sku($request->name);
            if($category_id=="") $category_id = 2;

            $request->merge(['product_type'=>2,'sku'=>$sku,'barcode_type'=>'128','category_id'=>$category_id,'creator_id' => $this->user->id]);
            try{
                DB::beginTransaction();
                    // $product = Product::create($request->all());
                    $product = Product::find($productId);
                    $product->update($request->all());


                    if($request->stock!=0 || $request->stock!=""){
                        $stockDataStore = [
                            'product_id'      => $productId,
                            'source'          => 1,
                            'stock'           => $request->stock,
                            'unit'            => 'PAKET',
                            'created_at'       => date('Y-m-d H:i:s')
                        ];
                        DB::table('product_stock')->updateOrInsert([
                            'product_id' => $productId,
                            'source' => 1,
                        ],$stockDataStore);
                    }

                    $pakets = $request->paket;
                    foreach($pakets as $s){
                        $product_id = $s["product_id"];
                        $quantity = $s["quantity"];

                        $paketPromoDataStore = [
                            'product_id'      => $product_id,
                            'paket_product_id'       => $productId,
                            'quantity'           => $quantity,
                        ];

                        DB::table('paket_promo_product_detail')->updateOrInsert([
                            'product_id' => $product_id,
                            'paket_product_id' => $productId,
                            'source' => 1,
                        ],$paketPromoDataStore);
                        // PaketPromoDetail::create([
                        //     'product_id'=>$product_id,
                        //     'paket_product_id' => $product->id,
                        //     'quantity' =>  $quantity
                        // ]);
                    // $invalid = $this->pengurangan_stock($product_id,$quantity,$request->stock,$product->id);
                    // if($invalid){
                    //     DB::rollBack();
                    //     return response()->json([
                    //         'success' => false,
                    //         'message' =>  "Oopps, stok di toko tidak cukup untuk dijadikan stok promo"
                    //     ],400);
                    // }

                    }
                DB::commit();
                return response()->json([
                    'success' => true,
                    'product' =>  $product
                ],200);
            }catch (\PDOException $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' =>  "Gagal memperbaharui paket produk : ".$e
                ],400);
            }
        }

    }


    public function auto_sku($brand)
    {
        $codeAwal = substr($brand,0,3).'-PAKET-';
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

    public function batal(Request $request,$id)
    {
        try{
            DB::beginTransaction();
                $update = Product::where('id',$id)->update(['is_active'=>0]);
                $stock_paket = ProductStock::where('product_id',$id)->where('source',1)->first();

                $pakets = PaketPromoDetail::where('paket_product_id',$id)->get();
                foreach($pakets as $s){
                    $product_id = $s->product_id;
                    $quantity = $s->quantity;
                    $invalid = $this->return_stock($product_id,$quantity,$stock_paket->stock,$id);

                }
                ProductStock::where('product_id',$id)->where('source',1)->update(['stock'=>0]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message'=> 'Paket dibatalkan dan stok di kembalikan',
                'product' =>  $update
            ],200);
        }catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' =>  "Gagal membatalkan produk"
            ],400);
        }
    }

    public function edit(Request $request,$id)
    {
        $paket = PaketPromoDetail::where('paket_product_id',$id)->get();
        return response()->json([
            'success'=>true,
            'promos'=> new PromoList($paket)
        ]);

    }

    public function delete(Request $request,$id)
    {
        $cekPenjualan = SaleDetail::where('product_id',$id)->get()->count();
        if($cekPenjualan > 0){
            return response()->json([
                'success' => false,
                'message' =>  "Paket product ini tidak bisa di hapus karena sudah terjual, harap batalkan promo jika ingin melanjutkan"
            ],400);
        }else{
            try
            {
                DB::beginTransaction();
                $product = Product::where('id',$id)->first();
                $isActive = $product->is_active;
                if($isActive==1){
                    $stock_paket = ProductStock::where('product_id',$id)->where('source',1)->first();
                    $pakets = PaketPromoDetail::where('paket_product_id',$id)->get();
                    foreach($pakets as $s){
                        $product_id = $s->product_id;
                        $quantity = $s->quantity;
                        $invalid = $this->return_stock($product_id,$quantity,$stock_paket->stock,$id);

                    }
                    ProductStock::where('product_id',$id)->where('source',1)->update(['stock'=>0]);
                }

                $product->delete();
                $pakets = PaketPromoDetail::where('paket_product_id',$id)->delete();
                $stock = ProductStock::where('product_id',$id)->delete();
                $stock = ProductStockHistory::where('product_id',$id)->delete();

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message'=> 'Paket dibatalkan dan stok di kembalikan'
                ],200);

            }catch (\PDOException $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' =>  "Gagal membatalkan produk"
                ],400);
            }

        }
    }
}
