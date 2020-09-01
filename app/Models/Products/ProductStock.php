<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    protected $table = "product_stock";
    protected $fillable = ["product_id","stock","source","location"];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

}
