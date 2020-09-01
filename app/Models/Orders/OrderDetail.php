<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = "order_product_detail";
    protected $fillable = ["order_product_id","product_id","quantity_order","unit","notes","quantity_received","quantity_send"];

    public function product()
    {
        return $this->belongsTo('App\Models\Products\Product','product_id');
    }
}
