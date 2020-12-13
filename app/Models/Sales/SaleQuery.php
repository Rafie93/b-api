<?php

namespace App\Models\Sales;
use Illuminate\Support\Facades\DB;
use App\Models\Sales\SaleDetail;
use App\Models\Sales\Sale;

class SaleQuery
{
    public function getDetail($saleId)
    {
       return SaleDetail::where('sale_id',$saleId)->get();
    }
    public function sumTotalProduct($saleId)
    {
       return SaleDetail::where('sale_id',$saleId)->sum('price_product');
    }
}
