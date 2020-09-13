<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orders\Order;
use App\Models\Products\ProductStock;
class DashboardStoreController extends Controller
{
    public function index(Request $request)
    {
        $pesanan_datang = Order::where('type',1)->whereIn('status',[4,7])->get()->count();
        $pesanan_selesai = Order::where('type',1)->where('status',6)->get()->count();
        $pesanan_going  = Order::where('type',1)->whereIn('status',[1,2,11,5])->get()->count();
        $pesanan_cancel =  Order::where('type',1)->where('status',3)->get()->count();

        return response()->json(
            [
            'success' => true,
            'dashboard' => array(
                'pesanan_datang' => $pesanan_datang,
                'pesanan_selesai' => $pesanan_selesai,
                'pesanan_proses' => $pesanan_going,
                'pesanan_cancel' => $pesanan_cancel
            )
           ],200);
    }
}
