<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\Products\Product;
use App\Models\Products\ProductStockHistory;
use App\Models\Products\ProductStock;

class HistoryStockReport implements  FromView,WithCalculatedFormulas
{

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public function view(): View
    {
        $productIdHistory = ProductStockHistory::select('product_id')
                                                ->where('source',$this->source)
                                                ->groupBy('product_id')
                                                ->get()
                                                ->toArray();

        $productStock = ProductStock::orderBy('product_id','asc')
                                    ->where('source',$this->source)
                                    ->whereIn('product_id',$productIdHistory);

        return view('stock.history', [
            'source' => $this->source,
            'stock' => $productStock->get()
        ]);

    }
}
