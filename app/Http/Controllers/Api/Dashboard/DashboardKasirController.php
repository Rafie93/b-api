<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use JWTAuth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use App\Models\Products\Product;
use App\Models\Products\ProductStock;
use App\User;

class DashboardKasirController extends Controller
{
    public $user;
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index(Request $request)
    {
        return response()->json(
            [
            'success' => true,
            'dashboard' => array(
                'cloud_data' => $this->cloud_data(),
                'offline_data' => $this->offline_data(2),
                'local_data' => $this->local_data(),
                'product_local' => $this->product_local(),
                'product_cloud' =>$this->product_cloud(),
                'stock_local' => $this->stock_local(),
                'stock_cloud' => $this->stock_cloud(),
            )
           ],200);
    }
    public function product_local()
    {
        return Product::where('is_active',1)->get()->count();
    }
    public function stock_local()
    {
        return ProductStock::where('source',1)->get()->count();
    }
    public function product_cloud()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'http://api.bahteramart.id/api/count/product');
        $contents = json_decode($response->getBody(),true);
        $total = $contents['total'];
        return $total;
    }
    public function stock_cloud()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'http://api.bahteramart.id/api/count/stock');
        $contents = json_decode($response->getBody(),true);
        $total = $contents['total'];
        return $total;
    }

    public function local_data()
    {
        return Sale::get()->count();
    }
    public function offline_data($net)
    {
        return Sale::where('creator_id',$this->user->id)->where('network',$net)->get()->count();
    }


    public function cloud_data()
    {
        $token = JWTAuth::fromUser($this->user);
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'http://api.bahteramart.id/api/transaksi/cloud_data');
        $contents = json_decode($response->getBody(),true);
        $total = $contents['total'];
        return $total;
    }

    public function upload_transaksi()
    {
        $penjualan = Sale::where('network',2)->where('creator_id',$this->user->id)->get();
        foreach ($penjualan as $row) {
            $username = "";
            $userData =  User::where('id',$row->creator_id)->get();
            if($userData->count()>0){
                $username = $userData->first()->username;
            }
            $product = SaleDetail::where('sale_id',$row->id)->get()->toArray();
            $data = array(
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
                'creator_username' => $username,
                'transaction_by'=> $row->transaction_by,
                'jarak'=> $row->jarak,
                'address'=> $row->address,
                'network'=> 1,
                'product_detail' => $product
            );
            $formData = json_encode($data);

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'http://api.bahteramart.id/api/transaksi/upload_transaksi',[
                'body' => $formData,
                'headers' => [
                    'Content-Type'     => 'application/json',
                ]
            ]);

            $contents = json_decode($response->getBody(),true);
            $sukses =  $contents['success'];
            if($sukses){
                Sale::where('code',$row->code)->update([
                    'network'=>1
                ]);
            }

        }

        return response()->json(
            [
            'success' => true,
            'message' => 'Sinkronisasi Selesai'
           ],200);

    }

    public function tarik_transaksi()
    {
        $token = JWTAuth::fromUser($this->user);

        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'http://api.bahteramart.id/api/transaksi/data_transaksi');
        $contents = json_decode($response->getBody(),true);
        $data = $contents['data'];
        for($i=0;$i<count($data);$i++){
            $code = $data[$i]['code'];
            $detail_product = $data[$i]['detail_product'];
            $dataStore = [
                'code'=> $code,
                'customer_id'=> $data[$i]['customer_id'],
                'date'=> $data[$i]['date'],
                'time'=>  $data[$i]['time'],
                'date_shipping'=>  $data[$i]['date_shipping'],
                'date_order' =>  $data[$i]['date_order'],
                'date_payment' =>  $data[$i]['date_payment'],
                'date_payment_confirmation'=> $data[$i]['date_payment_confirmation'],
                'date_shipping'=> $data[$i]['date_shipping'],
                'total_bill'=> $data[$i]['total_bill'],
                'total_before_tax' =>  $data[$i]['total_before_tax'],
                'total_price'=> $data[$i]['total_price'],
                'total_price_product'=> $data[$i]['total_price_product'],
                'discount'=> $data[$i]['discount'],
                'status'=> $data[$i]['status'],
                'status_order'=> $data[$i]['status_order'],
                'payment_methode'=> $data[$i]['payment_methode'],
                'payment_channel'=> $data[$i]['payment_channel'],
                'no_kartu'=> $data[$i]['no_kartu'],
                'coupon'=> $data[$i]['coupon'],
                'creator_id'=> $data[$i]['creator_id'],
                'transaction_by'=> $data[$i]['transaction_by'],
                'jarak'=> $data[$i]['jarak'],
                'address'=> $data[$i]['address'],
                'network'=> $data[$i]['network']
            ];
            $check = Sale::where('code',$code)->get()->count();
            if($check > 0){

            }else{
              $sales =  Sale::create($dataStore);
              $detail_sales = $detail_product;
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

            }
        }
        return response()->json(
            [
            'success' => true,
            'message' => 'Sinkronisasi Selesai'
           ],200);
    }

    public function tarik_product()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'http://api.bahteramart.id/api/transaksi/data_product');
        $contents = json_decode($response->getBody(),true);
        $data = $contents['data'];
        for($i=0;$i<count($data);$i++){
            $stock = $data[$i]['stock'];
            $dataStore = [
                'id'=> $data[$i]['id'],
                'sku'=> $data[$i]['sku'],
                'barcode'=> $data[$i]['barcode'],
                'category_id' => $data[$i]['category_id'],
                'creator_id' => $data[$i]['creator_id'],
                'name'=>  $data[$i]['name'],
                'alert_quantity' =>  $data[$i]['alert_quantity'],
                'sale_unit' =>  $data[$i]['sale_unit'],
                'purchase_unit'=> $data[$i]['purchase_unit'],
                'converse_unit'=> $data[$i]['converse_unit'],
                'brand'=> $data[$i]['brand'],
                'price' =>  $data[$i]['price'],
                'price_modal'=> $data[$i]['price_modal'],
                'product_type'=> $data[$i]['product_type'],
                'price_type'=> $data[$i]['price_type'],
                'price_type_in'=> $data[$i]['price_type_in'],
                'price_promo'=> $data[$i]['price_promo'],
                'start_promotion'=> $data[$i]['start_promotion'],
                'end_promotion'=> $data[$i]['end_promotion']
            ];
             DB::table('product')->updateOrInsert([
                 'id' =>  $data[$i]['id']
             ],$dataStore);
             if($stock!=null){
                $dataStock = [
                    'product_id' => $data[$i]['id'],
                    'unit' => $data[$i]['sale_unit'],
                    'source' => 1,
                    'stock' => $stock
                ];
                DB::table('product_stock')->updateOrInsert([
                    'product_id' => $data[$i]['id'],
                    'source' => 1
                ],$dataStock);
             }
        }

        return response()->json(
            [
            'success' => true,
            'message' => 'Sinkronisasi Selesai'
           ],200);
    }

}
