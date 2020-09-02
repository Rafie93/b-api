<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders\Order;
use App\Models\Orders\OrderDetail;
use App\Models\Products\ProductStock;
use App\Models\Products\Product;
use App\Models\Products\ProductStockHistory;
use App\Http\Resources\Orders\OrderList as Resource;
use JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Models\Sistem\NumberSequence;

class OrderController extends Controller
{
    public $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index(Request $request)
    {
          $listOrder = Order::orderBy('id','desc')
                                    ->where('type',1)
                                    ->when($request->status, function ($query) use ($request) {
                                        $query->where('status', '=',$request->status);

                                    })
                                    ->when($request->keyword, function ($query) use ($request) {
                                        $query->where('code', 'LIKE','%'.$request->keyword.'%');
                                    })
                                    ->get();
        return response()->json([
            'success' => true,
            'orders' => new Resource($listOrder)
           ],200);
    }
    public function listApprovedOrderGudang(Request $request)
    {
          $listOrder = Order::orderBy('id','desc')
                                    ->where('type',2)
                                    ->when($request->status, function ($query) use ($request) {
                                        $query->where('status', '=',$request->status);

                                    })
                                    ->when($request->keyword, function ($query) use ($request) {
                                        $query->where('code', 'LIKE','%'.$request->keyword.'%');
                                    })
                                    ->get();
        return response()->json([
            'success' => true,
            'orders' => new Resource($listOrder)
           ],200);
    }

    public function listApproveOrderStore(Request $request)
    {
          $listOrder = Order::orderBy('id','desc')
                                    ->where('type',1)
                                    ->when($request->status, function ($query) use ($request) {
                                        $query->where('status', '=',$request->status);

                                    })
                                    ->when($request->keyword, function ($query) use ($request) {
                                        $query->where('code', 'LIKE','%'.$request->keyword.'%');
                                    })
                                    ->get();
        return response()->json([
            'success' => true,
            'orders' => new Resource($listOrder)
           ],200);
    }

    public function pesananGudang(Request $request)
    {
          $listOrder = Order::orderBy('id','desc')
                                    ->where('type',2)
                                    ->when($request->status, function ($query) use ($request) {
                                        $query->where('status', '=',$request->status);

                                    })
                                    ->when($request->keyword, function ($query) use ($request) {
                                        $query->where('code', 'LIKE','%'.$request->keyword.'%');
                                    })
                                    ->get();
        return response()->json([
            'success' => true,
            'orders' => new Resource($listOrder)
           ],200);
    }

    public function requestStore(Request $request)
    {
        $listOrder = Order::orderBy('id','desc')
                        ->where('type',1)
                        ->whereIn('status',[11,1,2,5,4,6,7])
                        ->whereNotNull('approved_order_id')
                        ->when($request->status, function ($query) use ($request) {
                            $query->where('status', '=',$request->status);
                        })
                        ->when($request->keyword, function ($query) use ($request) {
                            $query->where('code', 'LIKE','%'.$request->keyword.'%');
                        })
                        ->get();
        return response()->json([
        'success' => true,
        'orders' => new Resource($listOrder)
        ],200);
    }

    public function detail($orderId)
    {
        $listOrder = OrderDetail::where('order_product_id',$orderId)->get();
        $det=[];
        foreach($listOrder as $d){
            $det[] = array(
                'id' => $d->id,
                'product_id' => $d->product_id,
                'sku' => $d->product->sku,
                'barcode'=> $d->product->barcode,
                'name' => $d->product->name,
                'quantity' => $d->quantity_order,
                'price'=> $d->product->price,
                'stock_gudang' => $this->cekStock($d->product_id,2),
                'satuan_gudang' => $this->cekUnit($d->product_id,2),
                'jumlah_kirim' => $d->quantity_send,
                'quantity_received' => $d->quantity_received,
                'unit'  => $d->unit,
                'notes' => $d->notes,
                'note_gudang' => $d->note_gudangm
            );
        }
        $order = Order::where('id',$orderId)->first();
        return response()->json([
            'id' => $order->id,
            'type' => $order->type,
            'approved_order_date' => $order->approved_order_date,
            'approved_date'=> $order->approved_date,
            'code'=> $order->code,
            'code_gudang'=> $order->code_gudang,
            'date' => $order->date,
            'notes' => $order->notes,
            'status'=> $order->status,
            'status_display' => $order->status(),
            'penanggung_jawab' => $order->penanggung_jawab(),
            'detail' =>  $det
           ],200);
    }

    public function store(Request $request)
    {
       $type = $request->type=="2"? "GD" : "ST";
       $code = $request->code=="" ? generateCode($type) : $request->code;
       $date = $request->date=="" ? date('Y-m-d H:i:s') : $this->replaceDate($request->date);
       $request->merge(['code'=>$code,'creator_id'=>$this->user->id,'status'=>1,'date'=>$date]);
       try
       {
            DB::beginTransaction();
                $order = Order::create($request->all());
                $orderId = $order->id;
                foreach ($request->products as $key => $value) {
                    $product_id = $value["product_id"];
                    $notes = $value["notes"];
                    $unit = $value["unit"];
                    $quantity = $value["quantity"];
                    if($type==1){
                        $stock = $value["stock_gudang"];
                        if($quantity>$stock){
                            DB::rollBack();
                            return response()->json([
                                'success'=>false,
                                'message'=>"Jumlah Order Not Valid"
                            ], 400);
                        }

                    }

                    $detail = new \App\Models\Orders\OrderDetail;
                    $detail->order_product_id = $orderId;
                    $detail->product_id = $product_id;
                    $detail->unit = $unit;
                    $detail->notes = $notes;
                    $detail->quantity_send = $quantity;
                    $detail->quantity_order = $quantity;
                    $detail->save();
                }

            DB::commit();
            return response()->json([
                'success'=>true,
                'message'=> "Pesanan Berhasil dibuat",
                'order_id' => $orderId,
                'order_code' => $order->code
                ], 200);

        } catch (\PDOException $e) {
            DB::rollBack();

            return response()->json([
                'success'=>false,
                'message'=>$e
            ], 400);
        }
    }

    public function confirmArrival(Request $request,$id)
    {
        try
        {
             DB::beginTransaction();
                 $order = Order::find($id);
                 $order->update([
                     'status'=>6,
                     'arrival_date'=> date('Y-m-d H:i:s')
                 ]);
                 foreach ($request->detail as $key => $value) {
                     $product_id = $value["product_id"];
                     $quantity_received = $value["quantity_received"];
                     $unit = $value["unit"];
                     OrderDetail::where('product_id',$product_id)
                            ->where('order_product_id',$id)
                            ->update([
                                'quantity_received'=> $quantity_received
                            ]);
                    $this->stokIn($product_id,$quantity_received,$unit,1,$order->code);
                 }

             DB::commit();
             return response()->json([
                 'success'=>true,
                 'message'=> "Pesanan Di Perbaharui",
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

    public function approvePengiriman(Request $request,$id)
    {
        $order = Order::find($id);
        $order->update([
            'status'=>4,
            'approved_id'=>$this->user->id,
            'approved_date'=> date('Y-m-d H:i:s')
        ]);
        return response()->json([
            'success'=>true,
            'message'=> "Pengiriman Di Approve",
            'order_id' => $id,
            ], 200);
    }

    public function approveOrderStore(Request $request,$id)
    {
        $order = Order::where('id',$id)->where('type',1)->first();
        $order->update([
            'approved_order_id'=>$this->user->id,
            'status'=> 11,
            'approved_order_date'=> date('Y-m-d H:i:s')
        ]);
        return response()->json([
            'success'=>true,
            'message'=> "Pesanan Di Approve",
            'order_id' => $id,
            ], 200);
    }

    public function approveOrderGudang(Request $request,$id)
    {
        $order = Order::where('id',$id)->where('type',2)->first();
        $order->update([
            'approved_order_id'=>$this->user->id,
            'status'=> 11,
            'approved_order_date'=> date('Y-m-d H:i:s')
        ]);
        return response()->json([
            'success'=>true,
            'message'=> "Pesanan Di Approve",
            'order_id' => $id,
            ], 200);
    }

    public function updateByWarehouse(Request $request,$id)
    {
        $code_gudang = $request->code_gudang=="" ? generateCode("DO") : $request->code_gudang;
        $dateProses = date('Y-m-d H:i:s');
        $pengiriman = false;
        $request->merge([
            'code_gudang'=>$code_gudang,
            'status'=>$request->status_update]);
        if($request->status_update=="2"){
            $set = 1;
            $request->merge(['proses_date'=>$dateProses]);
        }else if($request->status_update=="5"){
            $set = 2;
            $pengiriman=true;
            $request->merge(['send_date'=>$dateProses,'send_id'=>$this->user->id]);
        }

        try
        {
             DB::beginTransaction();
                 $order = Order::find($id);
                 $order->update($request->all());
                 foreach ($request->detail as $key => $value) {
                     $product_id = $value["product_id"];
                     $jumlah_kirim = $value["jumlah_kirim"]!=null ?  $value["jumlah_kirim"] : 0 ;
                     $note_gudang = $value["note_gudang"];
                     $stock_gudang = $value["stock_gudang"];
                     $unit = $value["unit"];

                     if($jumlah_kirim>$stock_gudang){
                        DB::rollBack();
                        return response()->json([
                            'success'=>false,
                            'message'=>"Stock Gudang Tidak Cukup"
                        ], 400);
                     }


                     $orderDetail = OrderDetail::where('order_product_id',$id)
                                                ->where('product_id',$product_id)
                                                ->update([
                                                    'quantity_send'=> $jumlah_kirim,
                                                    'quantity_received'=>$jumlah_kirim,
                                                    'note_gudang'  => $note_gudang,
                                                    'status' => $set,
                                                ]) ;
                    if($pengiriman){
                        $this->stokOut($product_id,$jumlah_kirim,$unit,2,$code_gudang);
                    }

                 }

             DB::commit();
             return response()->json([
                 'success'=>true,
                 'message'=> "Pesanan Di Perbabaharui",
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

    public function cekStock($product_id,$source)
    {
        $produk =  ProductStock::where('product_id',$product_id)->where('source',2)->first();
        if($produk){
            return $produk->stock;
        }
        return 0;
    }
    public function cekUnit($product_id,$source)
    {
        $produk =  ProductStock::where('product_id',$product_id)->where('source',2)->first();
        if($produk){
            return $produk->unit;
        }
        return '';
    }

    public function replaceDate($date)
    {
        return date("Y-m-d H:i:s", strtotime($date));
    }

    public function stokOut($product_id,$quantity,$unit,$source,$refCode="")
    {
       $productStock = ProductStock::where('product_id',$product_id)
                    ->where('source',$source)
                    ->where('unit',$unit)
                    ->first();
        if ($productStock){
            $stockNow = $productStock->stock;
            $stockNew = $stockNow-$quantity;
            $productStock->update(['stock'=>$stockNew]);
        }else{
            ProductStock::insert([
                'product_id'=>$product_id,
                'unit'  => $unit,
                'stock' => $quantity,
                'source' => $source
            ]);
        }
        $this->insertHistoryStock($product_id,$quantity,$unit,$source,$refCode);

    }
    public function stokIn($product_id,$quantity,$unit,$source,$refCode="")
    {
       $productStock = ProductStock::where('product_id',$product_id)
                    ->where('source',$source)
                    ->where('unit',$unit)
                    ->first();
        if ($productStock){
            $stockNow = $productStock->stock;
            $stockNew = $stockNow-$quantity;
            $productStock->update(['stock'=>$stockNew]);
        }else{
            ProductStock::insert([
                'product_id'=>$product_id,
                'unit'  => $unit,
                'stock' => $quantity,
                'source' => $source
            ]);
        }
        $this->insertHistoryStock($product_id,$quantity,$unit,$source,$refCode);
    }

    public function insertHistoryStock($product_id,$quantity,$unit,$source,$refCode)
    {
        ProductStockHistory::insert([
            'date' => date('Y-m-d H:i:s'),
            'product_id'=>$product_id,
            'unit'  => $unit,
            'quantity' => intval('-'.$quantity),
            'source' => $source,
            'ref_code' => $refCode,
        ]);
    }
}
