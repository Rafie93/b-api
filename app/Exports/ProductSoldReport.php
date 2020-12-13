<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

use Carbon\Carbon;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use Illuminate\Support\Facades\DB;

class ProductSoldReport implements  FromView,WithCalculatedFormulas
{

    public function __construct(string $date_start,string $date_end)
    {
        $this->date_start = $date_start;
        $this->date_end = $date_end;
    }

    public function view(): View
    {
        $date_start = $this->date_start;
        $date_end = $this->date_end;

        $sale = DB::table('sale_detail')
                    ->leftJoin('sale', 'sale.id', '=', 'sale_detail.sale_id')
                    ->leftJoin('product', 'product.id', '=', 'sale_detail.product_id')
                    ->select('sale_detail.*','product.name','product.sku','product.barcode','sale.code','sale.date','sale.time','sale.payment_methode','sale.creator_id')
                    ->where('sale.status',1)
                    ->where(function ($query) {
                        $query->whereNull('sale.status_order')
                            ->orWhere('sale.status_order', '=', 4);
                    })
                    ->orderBy('product.name','asc');

        //  if($this->payment_method != 'all'){
        //     $sale = $sale->where('sale.payment_methode', '=',$this->payment_method);
        // }
        // if($this->kasir != 'all'){
        //     $sale = $sale->where('sale.creator_id', '=',$this->kasir);
        // }

        $date_start = replaceDate($date_start);
        $e = explode(" ",$date_start);
        $d_s = $e[0];
        $t_s = $e[1];

        $date_end = replaceDate($date_end);
        $ed = explode(" ",$date_end);
        $d_e = $ed[0];
        $t_e = $ed[1];
        $sale = $sale->whereBetween('sale.date', [$d_s,$d_e]);

        return view('sale.product_out', [
            'date_start' => $d_s,
            'date_end' => $d_e,
            'sales' => $sale->get()
        ]);

    }
}
