<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Models\Products\Product;
use App\Models\Products\ProductStock;
use App\Models\Products\ProductStockHistory;
use App\Http\Resources\Sales\SaleList as SaleResource;
use App\Http\Resources\Sales\SaleItem as SaleItem;
use App\Http\Resources\Sales\SaleDetail as SaleDetailResource;
use App\User;

class SalesController extends Controller
{
    public $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index(Request $request)
    {
        $list = Sale::orderBy('id','desc')
                                    ->where('transaction_by','Kasir')
                                    ->when($request->status, function ($query) use ($request) {
                                        $query->where('status', '=',$request->status);

                                    })
                                    ->when($request->keyword, function ($query) use ($request) {
                                        $query->where('code', 'LIKE','%'.$request->keyword.'%');
                                    })
                                    ->get();
        return response()->json([
            'success' => true,
            'sales' => new SaleResource($list)
           ],200);
    }
    public function customer(Request $request)
    {
        $list = Sale::orderBy('id','desc')
                    ->where('transaction_by','Customer')
                    ->when($request->status, function ($query) use ($request) {
                        // $query->where('status', '=',$request->status);
                        if($request->status==3){
                            $query->whereIn('status_order',[2,3]);
                        }else{
                            $query->where('status_order', '=',$request->status);
                        }
                    })
                    // ->when($request->status_order, function ($query) use ($request) {
                    //     if($request->status_order==3){
                    //         $query->whereIn('status_order',[2,3]);
                    //     }else{
                    //         $query->where('status_order', '=',$request->status_order);
                    //     }
                    // })
                    ->when($request->keyword, function ($query) use ($request) {
                        $query->where('code', 'LIKE','%'.$request->keyword.'%');
                    })
                    ->get();
        return response()->json([
            'success' => true,
            'sales' => new SaleResource($list)
           ],200);
    }
    public function list_pembayaran(Request $request)
    {
        $list = Sale::orderBy('id','desc')
                    ->where('transaction_by','Customer')
                    ->when($request->status, function ($query) use ($request) {
                        if($request->status==1){
                            $query->where('status',1);
                        }else  if($request->status==2){
                            $query->where('status',1)->whereNotNull('date_payment')->whereNull('date_payment_confirmation');
                        }
                        else{
                            $query->where('status', 0);
                        }
                    })
                    ->when($request->keyword, function ($query) use ($request) {
                        $query->where('code', 'LIKE','%'.$request->keyword.'%');
                    })
                    ->get();
        return response()->json([
            'success' => true,
            'total_perlu_tindakan' => Sale::where('transaction_by','Customer')->where('status',1)->whereNotNull('date_payment')->whereNull('date_payment_confirmation')->get()->count(),
            'sales' => new SaleResource($list)
           ],200);
    }

    public function detail($id)
    {
        $list = Sale::where('id',$id)->get()->first();
        $detail = SaleDetail::where('sale_id',$id)->get();
        return response()->json([
            'success' => true,
            'sales' => new SaleItem($list),
            'detail' => new SaleDetailResource($detail)
           ],200);
    }

    public function store(Request $request)
    {
        $code = $request->code ? $request->code : $this->generateCode();
        $data = array(
            'code' => $code,
            'discount' => $request->diskon,
            'total_tax' => $request->pajak,
            'payment_methode' => $request->payment_metode,
            'total_price' => $request->grand_total,
            'total_before_tax' => $request->grand_total,
            'total_service' => 0,
            'total_price_product' => $request->total_price,
            'total_shipping' => $request->pengiriman,
            'creator_id' => $this->user->id,
            'notes' => $request->notes,
            'customer_id' => $request->customer_id,
            'status'    => $request->status,
            'date' => $request->date ? $request->date : date('Y-m-d'),
            'time' => $request->time ? $request->time : date('H:i:s')
        );

        try
        {
             DB::beginTransaction();
                 $sale = Sale::create($data);
                 $saleId = $sale->id;
                 foreach ($request->cart as $key => $value) {
                    $product_id = $value["product_id"];
                    $price = $value["price_sale"];
                    $price_product = $value["price_modal"];
                    $quantity = $value["quantity"];
                    $unit = Product::where('id',$product_id)->first()->sale_unit;

                    if($this->cekStock($product_id,$quantity)){
                        DB::rollBack();
                        return response()->json([
                            'success'=>false,
                            'message'=>"Jumlah Order Not Valid"
                        ], 400);
                    }

                    $detail = new \App\Models\Sales\SaleDetail;
                    $detail->sale_id = $saleId;
                    $detail->product_id = $product_id;
                    $detail->price_product = $price_product;
                    $detail->price_sale = $price;
                    $detail->quantity = $quantity;
                    $detail->save();

                    if($request->status!=0){
                        $this->pengurangan_stock($product_id,$quantity,$unit,$code);
                    }
                 }

             DB::commit();
             return response()->json([
                 'success'=>true,
                 'message'=> "Penjualan Berhasil dibuat",
                 'sales' => $sale,
                 ], 200);
        }catch (\PDOException $e) {
            DB::rollBack();

            return response()->json([
                'success'=>false,
                'message'=>$e
            ], 400);
        }
    }
    public function generateCode()
    {
        return "OD".date('ymdhis').$this->user->id;
    }
    public function cekStock($product_id,$quantity)
    {
        $ps = ProductStock::where('product_id',$product_id)
                    ->where('source',1)
                    ->first();
        if($ps){
            $stock = $ps->stock;
            if($quantity > $stock){
                return true;
            }else{
                return false;
            }
        }
        return true;
    }
    public function pengurangan_stock($product_id,$quantity,$unit,$code)
    {
        $ps = ProductStock::where('product_id',$product_id)
                    ->where('source',1)
                    ->where('unit',$unit)
                    ->first();
        $stock_sekarang = $ps->stock;
        $ps->update([
            'stock' => $stock_sekarang - $quantity
        ]);
        ProductStockHistory::insert([
            'date' => date('Y-m-d H:i:s'),
            'product_id' => $product_id,
            'quantity' => 0 - $quantity,
            'unit' => $unit,
            'source' => 1,
            'ref_code' => $code
        ]);
    }

    // endpoint update transaction sales
    public function update_transaction(Request $request,$id)
    {
        $now = date('Y-m-d H:i:s');
        $status = $request->status_update;
        $sale = Sale::where('id',$id)->first();
        if($sale->payment_methode=='Transfer' && $sale->status==0 && $sale->date_payment_confirmation!=null){
            return response()->json([
                'success' => false,
                'message' => 'Customer ini belum melakukan pembayaran, tidak bisa melanjutkan proses'
            ],400);
        }else{
            $sale->update(['status_order'=>$status]);
            if($status==3){
            $sale->update(['date_shipping'=>$now]);
            }else  if($status==99){
            $sale->update(['date_cancel'=>$now]);
            }
            if ($sale){
                $creatorId = $sale->creator_id;
                $code = $sale->code;
                $user = User::where('id',$creatorId)->first();
                if($user->fcm_token!=null){
                    $judul = "Hai ".$user->name;
                    $isi = "No. Pesanan Anda ".$code;
                    if($status==99){
                    $isi .= " Telah dibatalkan";
                    }else{
                        $isi .= " Akan dikirim oleh kurir kami, harap tunggu kedatangan pesanan anda!!";
                    }
                    sendMessageToDevice($judul,
                                        $isi,
                                        $user->fcm_token);

                }
                return response()->json([
                    'success' => true,
                    'message' => 'Status Pesanan sudah diperbaharui',
                    'sales' => new SaleItem($sale)
                ],200);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Ooooppsss gagal memperbaharui'
                ],400);
            }
        }
    }

    public function konfirmasi_pembayaran(Request $request,$id)
    {
        $now = date('Y-m-d H:i:s');
        $sale = Sale::where('id',$id)->first();
        if($sale->date_payment==null){
            return response()->json([
                'success' => false,
                'message' => 'Customer ini belum melakukan pembayaran'
            ],400);
        }else  if($sale->date_payment_confirmation!=null){
            return response()->json([
                'success' => false,
                'message' => 'Customer ini sudah bayar'
            ],400);
        }
        else{
            $sale->update(['date_payment_confirmation'=>$now]);
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran pada pesanan ini sudah di konfirmasi',
                'sales' => new SaleItem($sale)
            ],200);
        }
    }
}
