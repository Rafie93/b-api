<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use App\Models\Orders\Order;
use App\Models\Orders\OrderDetail;

class OrderController extends Controller
{
    public function order_pdf($id)
    {
        $order = Order::where('id',$id)->first();
        $type = "order";
        $pdf = PDF::setOptions(['isRemoteEnabled' => true])
                                ->loadView('order.pdf', compact('order','type'))
                                ->setPaper('a4','portrait');
         return $pdf->stream($id.'.e-surat.pdf');
        // return view('order.pdf',compact('order'));
    }
    public function pengiriman_pdf($id)
    {
        $order = Order::where('id',$id)
                        ->whereNotNull('send_date')
                        ->first();
        $type = "pengiriman";
        // return view('order.pdf',compact('order','type'));
        $pdf = PDF::setOptions(['isRemoteEnabled' => true])
                                ->loadView('order.pdf', compact('order','type'))
                                ->setPaper('a4','portrait');
        return $pdf->stream($id.'.e-surat.pdf');
    }
}
