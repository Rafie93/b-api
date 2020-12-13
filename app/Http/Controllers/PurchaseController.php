<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use App\Models\Purchases\Purchase;
use App\Exports\PembelianReport;
use App\Exports\ProductInGudangReport;

use Excel;

class PurchaseController extends Controller
{
    //
    public function pdf($id)
    {
        $purchase = Purchase::where('id',$id)->first();
        $pdf = PDF::setOptions(['isRemoteEnabled' => true])
                                ->loadView('purchase.pdf', compact('purchase'))
                                ->setPaper('A4','portrait');
         return $pdf->stream($id.'.e-surat.pdf');
    }

    public function excel(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $rinci = $request->rinci;
        return Excel::download(new PembelianReport($date_start,$date_end,$rinci), 'pembelian.xlsx');
    }
    public function product_in(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        return Excel::download(new ProductInGudangReport($date_start,$date_end), 'barang_masuk_gudang.xlsx');

    }
}
