<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use App\Models\Products\ProductComment;

class DashboardCsController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            [
            'success' => true,
            'dashboard' => array(
                'chat' => $this->chat(),
                'pembayaran' => $this->pembayaran(),
                'pesanan'=> $this->pesanan()
            )
           ],200);
    }

    public function pembayaran()
    {
        $total_perlu_tindakan = Sale::where('transaction_by','Customer')
                                    ->where('status',1)->whereNotNull('date_payment')
                                    ->whereNull('date_payment_confirmation')
                                    ->get()
                                    ->count();
        return $total_perlu_tindakan;
    }
    public function pesanan()
    {
        $total_perlu_tindakan_cod = Sale::where('transaction_by','Customer')
                                    ->where('status_order',1)->where('payment_methode','Cash On Delivery')
                                    ->get()->count();

        $total_perlu_tindakan_transfer = Sale::where('transaction_by','Customer')
                                    ->where('status_order',1)->where('payment_methode','Transfer')
                                    ->whereNotNull('date_payment')
                                    ->whereNotNull('date_payment_confirmation')
                                    ->get()->count();

        return $total_perlu_tindakan_cod + $total_perlu_tindakan_transfer;
    }

    public function chat()
    {
       $total_chat =  ProductComment::where('to','admin')->where('is_read',0)->get()->count();
       return $total_chat;
    }
}
