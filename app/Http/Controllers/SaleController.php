<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use Illuminate\Support\Facades\DB;
use Excel;
use App\Exports\SaleReport;
use App\Exports\PendapatanReport;
use App\Exports\PendapatanTotalReport;
use App\Exports\ProductSoldReport;
use App\Exports\ProductSumReport;

class SaleController extends Controller
{
    public function exportExcel(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $payment_method = $request->payment;
        $kasir = $request->kasir;
        $rinci = $request->rinci;

        return Excel::download(new SaleReport($date_start,$date_end,$payment_method,$kasir,$rinci), 'penjualan.xlsx');

    }
    public function pendapatanExcel(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $payment_method = $request->payment;
        $kasir = $request->kasir;
        return Excel::download(new PendapatanReport($date_start,$date_end,$payment_method,$kasir), 'pendapatan.xlsx');
    }
    public function pendapatanTotalExcel(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $type = $request->type;
        $kasir = $request->kasir;
        return Excel::download(new PendapatanTotalReport($date_start,$date_end,$type,$kasir), 'total_pendapatan.xlsx');
    }

    public function reportProductOut(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;

        return Excel::download(new ProductSoldReport($date_start,$date_end), 'barang_keluar_store.xlsx');

    }

    public function product_terjual(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $type = $request->type;
        return Excel::download(new ProductSumReport($date_start,$date_end,$type), 'product_terjual.xlsx');

    }

}
