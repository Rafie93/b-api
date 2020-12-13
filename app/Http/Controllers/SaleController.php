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
use App\Exports\SaleShiftReport;

class SaleController extends Controller
{
    public function exportExcel(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $payment_method = $request->payment;
        $kasir = $request->kasir;
        $rinci = $request->rinci;

        return Excel::download(new SaleReport($date_start,$date_end,$payment_method,$kasir,$rinci), 'penjualan-'.$date_start.'.xlsx');

    }
    public function pendapatanExcel(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $payment_method = $request->payment;
        $kasir = $request->kasir;
        return Excel::download(new PendapatanReport($date_start,$date_end,$payment_method,$kasir), 'pendapatan-'.$date_start.'.xlsx');
    }
    public function pendapatanTotalExcel(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $type = $request->type;
        $kasir = $request->kasir;
        return Excel::download(new PendapatanTotalReport($date_start,$date_end,$type,$kasir), 'total_pendapatan-'.$date_start.'-.xlsx');
    }

    public function reportProductOut(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        return Excel::download(new ProductSoldReport($date_start,$date_end), 'barang_keluar_store-'.$date_start.'-.xlsx');

    }

    public function product_terjual(Request $request)
    {
        $date_start = $request->date_start;
        $date_end = $request->date_end;
        $type = $request->type;
        return Excel::download(new ProductSumReport($date_start,$date_end,$type), 'product_terjual-'.$date_start.'-'.$type.'.xlsx');
    }

    public function shift_penjualan(Request $request)
    {
        $date = $request->date;
        $shift = $request->shift;
        $rinci = $request->rinci;
        $kasir = $request->kasir;
        return Excel::download(new SaleShiftReport($date,$shift,$rinci,$kasir), 'shift-penjualan-'.$date.'-'.$shift.'-.xlsx');

    }


}
