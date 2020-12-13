<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleDetail;
use Illuminate\Support\Facades\DB;

class ProductSumReport implements  FromView,WithCalculatedFormulas
{

    public function __construct(string $date_start,string $date_end,string $type)
    {
        $this->date_start = $date_start;
        $this->date_end = $date_end;
        $this->type =$type;
    }

    public function view(): View
    {
        $date_start = $this->date_start;
        $date_end = $this->date_end;
        $type = $this->type;

        $date_start = replaceDate($date_start);
        $e = explode(" ",$date_start);
        $d_s = $e[0];
        $t_s = $e[1];

        $date_end = replaceDate($date_end);
        $ed = explode(" ",$date_end);
        $d_e = $ed[0];
        $t_e = $ed[1];

        if($type=='all'){
            $product_sold = DB::table("sale_detail")
                ->leftjoin("sale","sale.id","=","sale_detail.sale_id")
                ->leftjoin("product","product.id","=","sale_detail.product_id")
                ->select(DB::raw("SUM(quantity) count"), "product_id","product.name","product.barcode","product.sku")
                ->groupBy("product_id","product.name","product.barcode","product.sku")
                ->havingRaw("SUM(quantity) >= 1")
                ->whereBetween('sale.date', [$d_s,$d_e])
                ->where('sale.status',1)
                ->orderBy("count",'desc')
                ->get();

        }else if($type=='harian'){
            $product_sold = DB::table("sale_detail")
            ->leftjoin("sale","sale.id","=","sale_detail.sale_id")
            ->leftjoin("product","product.id","=","sale_detail.product_id")
            ->select(DB::raw("SUM(quantity) count"),"sale.date","product_id","product.name","product.barcode","product.sku")
            ->groupBy("product_id","product.name","product.barcode","product.sku","sale.date")
            ->havingRaw("SUM(quantity) >= 1")
            ->whereBetween('sale.date', [$d_s,$d_e])
            ->where('sale.status',1)
            ->orderBy("sale.date",'asc')
            ->orderBy("count",'desc')
            ->get();
        }else if($type=='mingguan'){
            $product_sold = DB::table('sale_detail')
            ->leftjoin("sale","sale.id","=","sale_detail.sale_id")
            ->leftjoin("product","product.id","=","sale_detail.product_id")
            ->select(DB::raw("SUM(quantity) count"), DB::Raw('WEEK(sale.date) week'),"product_id","product.name","product.barcode","product.sku")
            ->whereBetween('sale.date', [$d_s,$d_e])
            ->where('sale.status',1)
            ->groupBy("product_id","product.name","product.barcode","product.sku", "week")
            ->orderBy("week",'asc')
            ->orderBy("count",'desc')
            ->get();
        }
        else if($type=='bulanan'){
            $product_sold = DB::table('sale_detail')
            ->leftjoin("sale","sale.id","=","sale_detail.sale_id")
            ->leftjoin("product","product.id","=","sale_detail.product_id")
            ->select(DB::raw("SUM(quantity) count"), DB::Raw('MONTH(sale.date) month'),"product_id","product.name","product.barcode","product.sku")
            ->whereBetween('sale.date', [$d_s,$d_e])
            ->where('sale.status',1)
            ->groupBy("product_id","product.name","product.barcode","product.sku", "month")
            ->orderBy("month",'asc')
            ->orderBy("count",'desc')
            ->get();
        }

        return view('sale.product_sold', [
                    'date_start' => $d_s,
                    'date_end' => $d_e,
                    'type' => $type,
                    'product_sold' => $product_sold
                ]);
    }
}
