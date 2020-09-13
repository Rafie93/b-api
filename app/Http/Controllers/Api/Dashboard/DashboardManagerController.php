<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchases\Purchase;
use App\Models\Orders\Order;

class DashboardManagerController extends Controller
{
    public function index(Request $request)
    {
        $pesanan_store = Order::where('type',1)->where('status',1)->get()->count();
        $pesanan_gudang = Order::where('type',2)->where('status',1)->get()->count();
        $pengiriman_to_store =  Order::where('type',1)->whereNotNull('send_id')->whereNull('approved_id')->get()->count();
        $pembelian = Purchase::whereNull('approved_id')->get()->count();

        $pesanan_store_acc =  Order::where('type',1)->whereNotNull('approved_order_date')->get()->count();
        $pesanan_gudang_acc =  Order::where('type',2)->whereNotNull('approved_order_date')->get()->count();
        $pengiriman_acc =  Order::where('type',1)->whereNotNull('approved_id')->get()->count();
        $pembelian_acc =  Purchase::whereNotNull('approved_id')->get()->count();

        return response()->json(
            [
            'success' => true,
            'dashboard' => array(
                'pesanan_store' => $pesanan_store,
                'pesanan_gudang' => $pesanan_gudang,
                'pengiriman_to_store' => $pengiriman_to_store,
                'pembelian_keuangan'=> $pembelian,
                'pesanan_store_acc' => $pesanan_store_acc,
                'pesanan_gudang_acc' => $pesanan_gudang_acc,
                'pengiriman_acc' => $pengiriman_acc,
                'pembelian_keuangan_acc'=>$pembelian_acc
            )
           ],200);
    }
}
