<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\Products\Product;
use App\Models\Products\ProductStockHistory;
use App\Models\Products\ProductStockExpired;
use App\Models\Products\ProductStock;

class StockReport implements  FromView,WithCalculatedFormulas
{

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    public function view(): View
    {
        $productStock = ProductStock::orderBy('product_id','asc')
                                    ->where('source',$this->source);

        return view('stock.excel', [
            'source' => $this->source,
            'stock' => $productStock->get()
        ]);

    }
}
