<?php

namespace App\Http\Controllers\Api\Sistem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use Illuminate\Support\Facades\DB;
use App\Models\Products\Product;
use App\Models\Products\ProductStock;
use App\User;

class SinkronisasiDataController extends Controller
{
    public function count_sales_data()
    {
       $count =  Sale::where('network',1)->get()->count();
       return response()->json([
        'success' => true,
        'total' => $count
       ],200);
    }

    public function count_product_data()
    {
       $count =  Product::where('is_active',1)->get()->count();
       return response()->json([
        'success' => true,
        'total' => $count
       ],200);
    }

    public function count_stock_data() // stock store
    {
       $count =  ProductStock::where('source',1)->get()->count();
       return response()->json([
        'success' => true,
        'total' => $count
       ],200);
    }

    public function data_transaksi()
    {
        $data =  Sale::where('network',1)->get();
        $output = array();
        foreach($data as $row){
            $output[] = array(
                'code'=> $row->code,
                'customer_id'=> $row->customer_id,
                'date'=> $row->date,
                'time'=>  $row->time,
                'date_order' =>  $row->date_order,
                'date_payment' =>  $row->date_payment,
                'date_payment_confirmation'=> $row->date_payment_confirmation,
                'date_shipping'=> $row->date_shipping,
                'total_bill'=> $row->total_bill,
                'total_before_tax' =>  $row->total_before_tax,
                'total_price'=> $row->total_price,
                'total_price_product'=> $row->total_price_product,
                'discount'=> $row->discount,
                'status'=> $row->status,
                'status_order'=> $row->status_order,
                'payment_methode'=> $row->payment_methode,
                'payment_channel'=> $row->payment_channel,
                'no_kartu'=> $row->no_kartu,
                'coupon'=> $row->coupon,
                'creator_id'=> $row->creator_id,
                'transaction_by'=> $row->transaction_by,
                'jarak'=> $row->jarak,
                'address'=> $row->address,
                'network'=> $row->network,
                'detail_product' => SaleDetail::where('sale_id',$row->id)->get()
            );
        }

        return response()->json([
         'success' => true,
         'data' => $output
        ],200);
    }

    public function data_product()
    {
        $products = DB::table('product')
                    ->leftJoin('product_stock', function($join){
                        $join->on('product.id', '=', 'product_stock.product_id')
                            ->where('product_stock.source', '=', '1');
                    })

                    ->leftJoin('category', 'product.category_id', '=', 'category.id')
                    ->select('product.id','product.sku','product.barcode','product.name','product.category_id','product.creator_id',
                            'product.alert_quantity','category.name as category','product.sale_unit','product.purchase_unit','product.converse_unit',
                            'product.brand','product.price','product.price_modal','product.product_type','product.price_type','product.price_type_in',
                            'product.thumbnail','product.price_promo','product.start_promotion','end_promotion',
                                DB::raw('(CASE WHEN product_stock.stock IS NULL THEN NULL ELSE product_stock.stock END) AS stock')
                            )
                    ->where('product.is_active',1)
                    ->orderBy('product.id','asc');

        return response()->json([
            'success' => true,
            'data' => $products->get()
            ],200);
    }

    public function upload_transaksi(Request $request)
    {
        $userData = User::where('username',$request->creator_username)->where('role_id',3)->get();
        $userId = $request->creator_id;
        if($userData->count() > 0){
            $userId = $userData->first()->id;
        }

        $dataStore = [
            'code'=> $request->code,
            'customer_id'=> $request->customer_id,
            'date'=> $request->date,
            'time'=>  $request->time,
            'total_bill'=> $request->total_bill,
            'total_before_tax' =>  $request->total_before_tax,
            'total_price'=> $request->total_price,
            'total_price_product'=> $request->total_price_product,
            'discount'=> $request->discount,
            'status'=> $request->status,
            'status_order'=> $request->status_order,
            'payment_methode'=> $request->payment_methode,
            'payment_channel'=> $request->payment_channel,
            'no_kartu'=> $request->no_kartu,
            'coupon'=> $request->coupon,
            'creator_id'=> $userId,
            'transaction_by'=> $request->transaction_by,
            'jarak'=> $request->jarak,
            'address'=> $request->address,
            'network'=> $request->network,
            'date_complete' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        $transaksi = Sale::where('code',$request->code)->get()->count();
        if($transaksi>0){

        }else{
            try{
                DB::beginTransaction();
                    $sales = Sale::create($dataStore);
                    $detail_sales = $request->product_detail;

                    for($j=0;$j<count($detail_sales);$j++){
                        $dataDetail = [
                            'sale_id' => $sales->id,
                            'product_id' => $detail_sales[$j]['product_id'],
                            'price_product' => $detail_sales[$j]['price_product'],
                            'price_sale' => $detail_sales[$j]['price_sale'],
                            'quantity' => $detail_sales[$j]['quantity'],
                            'status' => $detail_sales[$j]['status'],
                            'type' => $detail_sales[$j]['type'],
                            'keterangan' => $detail_sales[$j]['keterangan'],
                        ];
                        DB::table('sale_detail')->updateOrInsert([
                        'sale_id' => $sales->id,
                        'product_id' =>$detail_sales[$j]['product_id']
                    ],$dataDetail);
                    }
                DB::commit();
                return response()->json([
                    'success' => true,
                    'code' => $request->code
                ],200);
            }catch (\PDOException $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'code' => $request->code
                ],200);
            }
        }

    }
}
