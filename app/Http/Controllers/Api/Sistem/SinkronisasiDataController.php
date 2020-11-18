<?php

namespace App\Http\Controllers\Api\Sistem;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;

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

    public function data_transaksi()
    {
        $data =  Sale::where('network',1)->get();
        return response()->json([
         'success' => true,
         'data' => $data
        ],200);
    }

    public function data_product()
    {

    }
}
