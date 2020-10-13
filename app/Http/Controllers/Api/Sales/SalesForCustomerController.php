<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Models\Customer\Customer;
use App\Models\Products\Product;
use App\Models\Products\ProductStock;
use App\Models\Products\ProductStockHistory;
use App\Http\Resources\Sales\SaleList as SaleResource;
use App\Http\Resources\Sales\SaleItem as SaleItem;
use App\Http\Resources\Sales\SaleDetail as SaleDetailResource;
use Carbon\Carbon;
class SalesForCustomerController extends Controller
{
    public $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index(Request $request)
    {
        $list = Sale::orderBy('id','desc')
                    ->where('transaction_by','Customer')
                    ->where('creator_id',$this->user->id)
                    ->when($request->status, function ($query) use ($request) {
                        if($request->status==3){
                            $query->whereNotIn('status_order', [99,3,4]);
                        }else{
                            $query->where('status', '=',$request->status);
                        }

                    })
                    ->when($request->status_order, function ($query) use ($request) {
                        if($request->status_order==3){
                            $query->whereIn('status_order',[2,3]);
                        }else{
                            $query->where('status_order', '=',$request->status_order);
                        }
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

    public function detail($id)
    {
        $list = Sale::where('id',$id)
                    ->where('creator_id',$this->user->id)
                    ->get()->first();

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
        $grand_total = $request->grand_total ? $request->grand_total : 0;
        $payment_methode = $request->payment_method;
        $unik=0;
        $total_bill = $grand_total;
        if($payment_methode=="Transfer"){
            $unik = $this->unik_kode_transfer();
            $total_bill = $grand_total+$unik;
        }

        $customer_id = Customer::where('user_id',$this->user->id)->first()->id;

        $data = array(
            'code' => $code,
            'coupon' => $request->coupon,
            'discount' => $request->diskon ? $request->diskon : 0,
            'total_tax' => $request->pajak ? $request->pajak : 0,
            'payment_methode' => $payment_methode,
            'payment_channel' => $request->payment_channel,
            'coupon'=> $request->coupon,
            'jarak'=> $request->jarak,
            'total_price' => $grand_total,
            'total_before_tax' => $request->grand_total ? $request->grand_total : 0,
            'total_service' => $request->total_service ? $request->total_service : 0,
            'total_price_product' => $request->sub_total ? $request->sub_total : 0,
            'total_shipping' => $request->pengiriman ?  $request->pengiriman : 0,
            'creator_id' => $this->user->id,
            'notes' => $request->notes,
            'customer_id' => $customer_id,
            'status'    => 0,
            'status_order' => 1,
            'date' => $request->date ? $request->date : date('Y-m-d'),
            'time' => $request->time ? $request->time : date('H:i:s'),
            'transaction_by' => 'Customer',
            'address' => $request->address_shipping,
            'lattitude' => $request->lattitude,
            'longitude' => $request->longitude,
            'total_bill' => $total_bill,
            'unik_code_transfer' => $unik,
            'date_order' => date('Y-m-d H:i:s'),
            'notes' => $request->notes,
        );

        try
        {
             DB::beginTransaction();
                 $sale = Sale::create($data);
                 $saleId = $sale->id;
                 $productcss = $request->products;
                 if($productcss){
                     $produkArray = json_decode($productcss, true);
                     $macamProduk = count($produkArray);
                     for ($i=0; $i < $macamProduk; $i++) {
                         $product_id = $produkArray[$i]["product_id"];
                         $price =  $produkArray[$i]["price"];
                         $variant_id =  $produkArray[$i]["variant_id"];
                         $quantity =  $produkArray[$i]["qty"];
                         $keterangan =  $produkArray[$i]["keterangan"];

                         $products_data = Product::where('id',$product_id)->first();
                         $unit = $products_data->sale_unit;
                         $type = $products_data->product_type;
                         $price_product = $products_data->price_modal;

                         if($this->cekStock($product_id,$quantity)){
                             DB::rollBack();
                             return response()->json([
                                 'success'=>false,
                                 'message'=>"Stok Habis"
                             ], 400);
                         }

                         $detail = new \App\Models\Sales\SaleDetail;
                         $detail->sale_id = $saleId;
                         $detail->product_id = $product_id;
                         $detail->price_product = $price_product;
                         $detail->variant_id = $variant_id;
                         $detail->price_sale = $price;
                         $detail->quantity = $quantity;
                         $detail->keterangan = $keterangan;
                         $detail->type=$type;
                         $detail->save();

                         if($detail){ $this->pengurangan_stock($product_id,$quantity,$unit,$code);}
                     }
                  }

             DB::commit();

             $date_order = $sale->date_order;
             $date_jatuh_tempo = Carbon::parse($date_order);
             $date_jatuh_tempo2 = $date_jatuh_tempo->addHours(24)->format('Y-m-d H:i:s');


             return response()->json([
                 'success'=>true,
                 'message'=> "Pesanan Berhasil dibuat",
                 'code' => $sale->code,
                 'sale_id'=> $sale->id,
                 'total_bill'=> $sale->total_bill,
                 'date_order' => $date_order,
                 'date_jatuh_tempo' => $date_jatuh_tempo2
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
        $last = rand(100,999);
        $code = "OD".date('ymdhis').$last;
        if($this->codeNumberExists($code)){
            return generateCode();
        }
        return $code;
    }

    public function unik_kode_transfer()
    {
        $code = rand(10,99);
        return $code;
    }

    function codeNumberExists($number) {
        return Sale::where('code',$number)->exists();
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

    public function bayar(Request $request)
    {
        $saleId = $request->sale_id;
        $total_bill = $request->total;
        try
        {
            DB::beginTransaction();
                $sale =Sale::where('id',$saleId)->update([
                    'date_payment'=> date('Y-m-d H:i:s'),
                    'status' => 1,
                ]);
                if ($request->hasFile('file')) {
                    $foto = $request->file('file');
                    $fileName = $foto->getClientOriginalName();
                    $request->file('file')->move('images/sale/'.$saleId,$fileName);
                    $fotoUpdate = Sale::where('id',$saleId)
                                     ->update(['image' => $fileName]);
                }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Pembayaran Berhasil dilakukan"
            ],200);

        }  catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Gagal melakukan pembayaran",
                'error' => $e
            ],400);

        }
    }

    public function batal(Request $request)
    {
        $saleId = $request->sale_id;
        $sale = $sale =Sale::find($saleId);
        if($saleId==null || $sale->status==1 || $sale->status_order != 1){
            return response()->json([
                'success' => false,
                'message' => "Transaksi ini tidak diijinkan untuk dibatalkan.."
            ],400);
        }

        try
        {
            DB::beginTransaction();
                $sale->update([
                    'date_cancel'=> date('Y-m-d H:i:s'),
                    'status_order' => 99,
                ]);

                $detail = SaleDetail::where('sale_id',$saleId)->get();
                foreach ($detail as $det){
                    $det_id = $det->id;
                    SaleDetail::where('id',$det_id)->update([
                        'status'=>2
                    ]);

                    $products_data = Product::where('id',$det->product_id)->first();
                    $unit = $products_data->sale_unit;
                    $this->penambahan_stock($det->product_id,$det->quantity,$unit,$sale->code);
                }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Transaksi Berhasil dibatalkan"
            ],200);

        }  catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Gagal melakukan pembatalan",
                'error' => $e
            ],400);

        }
    }

    public function penambahan_stock($product_id,$quantity,$unit,$code)
    {
        $ps = ProductStock::where('product_id',$product_id)
                    ->where('source',1)
                    ->where('unit',$unit)
                    ->first();
        $stock_sekarang = $ps->stock;
        $ps->update([ 'stock' => $stock_sekarang + $quantity]);
        ProductStockHistory::insert([
            'date' => date('Y-m-d H:i:s'),
            'product_id' => $product_id,
            'quantity' => $quantity,
            'unit' => $unit,
            'source' => 1,
            'ref_code' => $code
        ]);
    }

    public function terima(Request $request)
    {
        $saleId = $request->sale_id;
        $sale = $sale =Sale::find($saleId);
        if($saleId==null || $sale->status_order != 3){
            return response()->json([
                'success' => false,
                'message' => "Pesanan ini tidak diijinkan untuk diselesaikan.."
            ],400);
        }

        try
        {
            DB::beginTransaction();
                $sale->update([
                    'date_complete'=> date('Y-m-d H:i:s'),
                    'status'=>1,
                    'status_order' => 4,
                ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Pesanan sudah diterima"
            ],200);

        }  catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => "Gagal melakukan penerimaan barang",
                'error' => $e
            ],400);

        }
    }
}
