<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use App\Models\Orders\Order;
use App\Models\Orders\OrderDetail;
use App\Exports\ProductOutGudangReport;
use Excel;
use App\Exports\ProductInStoreReport;

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

    public function excel(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        return Excel::download(new ProductOutGudangReport($date_start,$date_end), 'barang_keluar_gudang.xlsx');

    }
    public function product_in(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        return Excel::download(new ProductInStoreReport($date_start,$date_end), 'barang_masuk_store.xlsx');

    }
}
