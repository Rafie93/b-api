<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchases\Purchase;
use App\Models\Orders\Order;

class DashboardWarehouseController extends Controller
{
    public function index(Request $request)
    {
        $pesanan_datang = Purchase::whereIn('status',[4,5])->get()->count();
        $pesanan_store_baru = Order::where('type',1)->where('status',11)->whereNotNull('approved_order_date')->get()->count();

        $pesanan_dikirim =  Order::where('type',1)->whereIn('status',[4,5])->whereNotNull('code_gudang')->count();
        $pesanan_store_selesai = Order::where('type',1)->where('status',6)->get()->count();

        $pesanan_keu_baru =  Order::where('type',2)->whereIn('status',[1,'11'])->whereNotNull('approved_order_date')->get()->count();
        $pesanan_keu_selesai =  Order::where('type',2)->where('status',2)->whereNotNull('approved_order_date')->get()->count();

        return response()->json(
            [
            'success' => true,
            'dashboard' => array(
                'pesanan_datang' => $pesanan_datang,
                'pesanan_store_baru' => $pesanan_store_baru,
                'pesanan_dikirim' => $pesanan_dikirim,
                'pesanan_store_selesai'=> $pesanan_store_selesai,
                'pesanan_keu_baru' => $pesanan_keu_baru,
                'pesanan_keu_selesai' => $pesanan_keu_selesai
            )
        ],200);
    }
}
