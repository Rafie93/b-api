<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Sale;
use JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Models\Products\Product;
use App\Models\Products\ProductStock;
use App\Models\Products\ProductStockHistory;

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
                                    ->when($request->status, function ($query) use ($request) {
                                        $query->where('status', '=',$request->status);

                                    })
                                    ->when($request->keyword, function ($query) use ($request) {
                                        $query->where('code', 'LIKE','%'.$request->keyword.'%');
                                    })
                                    ->get();
        return response()->json([
            'success' => true,
            'sales' => $list
           ],200);
    }

    public function detail($id)
    {
        # code...
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
}
