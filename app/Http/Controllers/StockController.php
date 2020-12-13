<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\Exports\StockReport;
use App\Exports\HistoryStockReport;

class StockController extends Controller
{
    public function excel(Request $request)
    {
        $source = $request->source;
        return Excel::download(new StockReport($source), 'stock-'.$source.'.xlsx');
    }
    public function history_excel(Request $request)
    {
        $source = $request->source;
        return Excel::download(new HistoryStockReport($source), 'history_stock-'.$source.'.xlsx');
    }
}
