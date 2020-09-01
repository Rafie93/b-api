<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductStockExpired extends Model
{
    protected $table = "product_stock_expired";
    protected $fillable = ["product_id","stock","source","expried_date"];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
}
