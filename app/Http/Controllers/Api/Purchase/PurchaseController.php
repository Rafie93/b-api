<?php

namespace App\Http\Controllers\Api\Purchase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Models\Purchases\Purchase;
use App\Models\Purchases\PurchaseDetail;
use App\Http\Resources\Purchases\PurchaseList as Resource;
use App\Http\Resources\Purchases\PurchaseItem as ItemResource;
use App\Models\Orders\OrderDetail;
use App\Models\Products\ProductStock;
use App\Models\Products\ProductStockHistory;
use App\Models\Products\ProductStockExpired;
use App\Models\Products\Product;

class PurchaseController extends Controller
{
    public $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index(Request $request)
    {
        $list = Purchase::orderBy('id','desc')
                                    ->when($request->status, function ($query) use ($request) {
                                        $query->where('status', '=',$request->status);

                                    })
                                    ->when($request->keyword, function ($query) use ($request) {
                                        $query->where('code', 'LIKE','%'.$request->keyword.'%');
                                    })
                                    ->get();
        return response()->json([
            'success' => true,
            'purchases' => new Resource($list)
           ],200);
    }
    public function detail($id)
    {
        $list = Purchase::where('id',$id)->first();
        return response()->json([
            'purchases' => new ItemResource($list)
           ],200);
    }

    public function store(Request $request)
    {
        $code = $request->code=="" ? generateCode("BPU","PO") : $request->code;
        $date = $request->date=="" ? date('Y-m-d H:i:s') : $this->replaceDate($request->date);
        $request->merge(['code'=>$code,'creator_id'=>$this->user->id,'status'=>1,'date'=>$date]);
        try
        {
             DB::beginTransaction();
                 $purchase = Purchase::create($request->all());
                 $purchaseId = $purchase->id;
                 foreach ($request->products as $key => $value) {
                     $product_id = $value["product_id"];
                     $unit = $value["unit"];
                     $price = $value["price"];
                     $quantity = $value["quantity"];

                     $detail = new \App\Models\Purchases\PurchaseDetail;
                     $detail->purchase_id = $purchaseId;
                     $detail->product_id = $product_id;
                     $detail->unit = $unit;
                     $detail->price = $price;
                     $detail->quantity = $quantity;
                     $detail->quantity_received = $quantity;
                     $detail->save();

                     //Update Di product order detail :
                     OrderDetail::join('order_product', function ($join) {
                                $join->on('order_product.id', '=', 'order_product_detail.order_product_id')
                                        ->where('order_product.type', '=', 2);
                                })
                                ->where('order_product_detail.product_id',$product_id)
                                ->where('order_product_detail.status',1)
                                ->update([
                                    'order_product_detail.status'=>2,
                                    'order_product.status'=>2,
                                    'order_product.proses_date'=>date('Y-m-d H:i:s')
                                ]);

                 }

             DB::commit();
             return response()->json([
                 'success'=>true,
                 'message'=> "Pembelian Berhasil dibuat",
                 'purchase_id' => $purchaseId,
                 'purchase_code' => $purchase->code
                 ], 200);

         } catch (\PDOException $e) {
             DB::rollBack();

             return response()->json([
                 'success'=>false,
                 'message'=>$e
             ], 400);
         }

    }
    public function approvePurchase(Request $request,$id)
    {
        $purchase = Purchase::find($id);
        $purchase->update([
            'status'=>4,
            'approved_id'=>$this->user->id,
            'approved_date'=> date('Y-m-d H:i:s')
        ]);
        return response()->json([
            'success'=>true,
            'message'=> "Purchase Di Approve",
            'purchase_id' => $id,
            ], 200);
    }

    public function updateByWarehouse(Request $request,$id)
    {
        $request->merge([
            'receive_date'=>date('Y-m-d H:i:s'),
            'receive_id' => $this->user->id,
            'status'=>2]);
        try
        {
             DB::beginTransaction();
                 $purchase = Purchase::find($id);
                 $purchase->update($request->all());
                 foreach ($request->detail as $key => $value) {
                     $product_id = $value["product_id"];
                     $quantity_received = $value["quantity_received"]!=null ?  $value["quantity_received"] : 0 ;
                     $unit = $value["unit"];
                     $date_expired =$value["expired_date"];

                     $_date_expired = date("Y-m-d", strtotime($date_expired));

                     $purchaseDetail = PurchaseDetail::where('purchase_id',$id)
                                                ->where('product_id',$product_id)
                                                ->update([
                                                    'quantity_received'=> $quantity_received,
                                                    'unit' => $unit,
                                                    'exp_date'=>$_date_expired
                                                ]) ;
                    $this->updatePriceProduct($product_id,$purchaseDetail);
                    $this->insertStockExpired($product_id,$quantity_received,$unit,$_date_expired,2);
                    $this->stokIn($product_id,$quantity_received,$unit,2,$purchase->code);
                 }

             DB::commit();
             return response()->json([
                 'success'=>true,
                 'message'=> "Pembelian Sukses Dikonfirmasi, dan stok gudang telah ditambahkan",
                 'order_id' => $id,
                 ], 200);

         } catch (\PDOException $e) {
             DB::rollBack();

             return response()->json([
                 'success'=>false,
                 'message'=>$e
             ], 400);
         }
    }
    public function stokIn($product_id,$quantity_received,$unit,$source,$refCode="")
    {
       $productStock = ProductStock::where('product_id',$product_id)
                    ->where('source',$source)
                    ->where('unit',$unit)
                    ->first();
        if ($productStock){
            $stockNow = $productStock->stock;
            $stockNew = $stockNow+$quantity_received;
            $productStock->update(['stock'=>$stockNew]);
        }else{
            ProductStock::insert([
                'product_id'=>$product_id,
                'unit'  => $unit,
                'stock' => $quantity_received,
                'source' => $source
            ]);
        }
        $this->insertHistoryStock($product_id,$quantity_received,$unit,$source,$refCode);

    }
    public function insertHistoryStock($product_id,$quantity,$unit,$source,$refCode)
    {
        ProductStockHistory::insert([
            'date' => date('Y-m-d H:i:s'),
            'product_id'=>$product_id,
            'unit'  => $unit,
            'quantity' => $quantity,
            'source' => $source,
            'ref_code' => $refCode,
        ]);
    }

    public function insertStockExpired($product_id,$quantity,$unit,$date_expired,$source)
    {

        ProductStockExpired::insert([
            'product_id'=>$product_id,
            'stock' => $quantity,
            'unit'  => $unit,
            'source' => $source,
            'expired_date' => $date_expired
        ]);
    }
    public function updatePriceProduct($product_id,$purchaseDetail)
    {
        // Product::where($product_id)->update([
        //     //'price_modal' => $purchaseDetail->price
        // ]);
    }


}
