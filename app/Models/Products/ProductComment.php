<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductComment extends Model
{
    protected $table = "product_comment";
    protected $fillable = ["product_id","comment","creator_id","to","type","is_read"];

}
