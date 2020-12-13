<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductOutGudangReport implements  FromView,WithCalculatedFormulas
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

        $order = DB::table('order_product_detail')
                    ->leftJoin('order_product', 'order_product.id', '=', 'order_product_detail.order_product_id')
                    ->leftJoin('product', 'product.id', '=', 'order_product_detail.product_id')
                    ->select('order_product_detail.*','product.name','product.sku','product.barcode',
                    'order_product.code','order_product.date','order_product.creator_id','order_product.status')
                    ->where('order_product.status',6)
                    ->orderBy('product.name','asc');

        $date_start = replaceDate($date_start);
        $e = explode(" ",$date_start);
        $d_s = $e[0];
        $t_s = $e[1];

        $date_end = replaceDate($date_end);
        $ed = explode(" ",$date_end);
        $d_e = $ed[0];
        $t_e = $ed[1];

        $order = $order->whereBetween('order_product.date', [$d_s,$d_e]);

        return view('order.excel', [
            'date_start' => $d_s,
            'date_end' => $d_e,
            'orders' => $order->get()
        ]);
    }
}
