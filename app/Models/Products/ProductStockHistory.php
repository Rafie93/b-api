<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductStockHistory extends Model
{
    protected $table = "product_stock_history";
    protected $fillable = ["date","product_id","quantity","unit","source"];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
}
