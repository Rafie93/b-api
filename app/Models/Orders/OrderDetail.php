<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = "order_product_detail";
    protected $fillable = ["order_product_id","status","product_id","quantity_order","unit","notes","quantity_received","quantity_send"];

    public function product()
    {
        return $this->belongsTo('App\Models\Products\Product','product_id');
    }
    public function isStatus()
    {
        $status = $this->status;
        if($status==1){
            return "Order";
        }else if($status==2){
            return "On-Proses";
        }else if($status==3){
            return "Tertunda";
        }else if($status==4){
            return "Dikirim";
        }else if($status==5){
            return "Diterima";
        }
        else if($status==6){
            return "Diterima Sebagian";
        }
    }
}
