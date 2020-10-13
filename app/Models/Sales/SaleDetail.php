<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    protected $table = "sale_detail";
    protected $fillable = ["sale_id","product_id","variant_id","price_product","price_sale","quantity","type","keterangan","status"];

    public function product()
    {
        return $this->belongsTo('App\Models\Products\Product','product_id');
    }
}
