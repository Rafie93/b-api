<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = "sale";
    protected $fillable = ["code","customer_id","date","time","total_price","total_before_tax","total_price_product","total_shipping","total_service","total_tax",
    "discount","status","payment_methode","notes","coupon","creator_id","invoice_id","transaction_by"];
}
