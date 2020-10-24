<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class PaketPromoDetail extends Model
{
    protected $table = "paket_promo_product_detail";
    protected $fillable = ["paket_product_id","product_id","quantity"];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
}
