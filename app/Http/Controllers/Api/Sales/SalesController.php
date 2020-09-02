<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Sale;
use JWTAuth;
use Illuminate\Support\Facades\DB;

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
        $data = array(
            'code' => $request->code ? $request->code : $this->generateCode(),
            'discount' => $request->discount,
            'total_tax' => $request->pajak,
            'payment_methode' => $request->payment_method,
            'total_price' => $request->total_price,
            'total_before_tax' => $request->total_berfore_tax,
            'total_service' => $request->total_service,
            'total_price_product' => $request->total_price_product,
            'total_shipping' => $request->pengiriman,
            'creator_id' => $this->user->id,
            'notes' => $request->notes,
            'customer_id' => $request->cutomer_id,
            'date' => $request->date,
            'time' => $request->time
        );

        try
        {
             DB::beginTransaction();
                 $sale = Sales::create($data);
                 $saleId = $sale->id;
                 foreach ($request->products as $key => $value) {
                    $product_id = $value["product_id"];
                    $price = $value["price_sale"];
                    $price_product = $value["price_product"];
                    $quantity = $value["quantity"];
                    $type = $value["type"];

                    $detail = new \App\Models\Sales\SaleDetail;
                    $detail->sale_id = $saleId;
                    $detail->product_id = $product_id;
                    $detail->price_product = $price_product;
                    $detail->price_sale = $price;
                    $detail->quantity = $quantity;
                    $detail->type = $type;
                    $detail->save();
                 }

             DB::commit();
             return response()->json([
                 'success'=>true,
                 'message'=> "Penjualan Berhasil dibuat",
                 'sale_id' => $saleId,
                 'sale_code' => $sale->code
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
}
