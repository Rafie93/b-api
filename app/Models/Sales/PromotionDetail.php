<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class PromotionDetail extends Model
{
    protected $table ='promotion_detail';
    protected $fillable = ['promotion_id','product_id','price_promo'];

    public function product()
    {
        return $this->belongsTo('App\Models\Products\Product','product_id');
    }
}
