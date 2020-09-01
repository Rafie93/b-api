<?php

namespace App\Models\Purchases;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $table = "purchase_detail";
    protected $fillable = ["purchase_id","product_id","price","price_received","quantity_received","quantity","unit"];

    public function product()
    {
        return $this->belongsTo('App\Models\Products\Product','product_id');
    }
}
