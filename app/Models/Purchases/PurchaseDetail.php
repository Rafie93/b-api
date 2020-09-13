<?php

namespace App\Models\Purchases;

use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    protected $table = "purchase_detail";
    protected $fillable = ["purchase_id","product_id","price","price_received","quantity_received","quantity","unit","status"];

    public function product()
    {
        return $this->belongsTo('App\Models\Products\Product','product_id');
    }
    public function isStatus()
    {
        if($this->status==1){
            return "Pembelian Baru";
        }else if($this->status==2){
            return "Barang Datang";
        }else if($this->status==3){
            return "Barang Datang Tidak Sesuai";
        }
    }
}
