<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = "product_image";
    protected $fillable = ["product_id","image"];

    public function image()
    {
        return $this->image=="" ? asset('images/empty.png') : asset('images').'/product/'.$this->product_id.'/'.$this->image ;
    }

}
