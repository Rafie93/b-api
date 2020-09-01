<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = "product_variant";
    protected $fillable = ["product_id","name","price"];
}
