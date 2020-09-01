<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = "brand";
    protected $fillable = ["thumbnail","name","is_active"];
}
