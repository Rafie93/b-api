<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductRating extends Model
{
    protected $table = "product_rating_ulasan";
    protected $fillable = ["sales_id","product_id","image","ulasan","rating","creator_id"];

    public function image()
    {
        return $this->image=="" ? asset('images/empty.png') : asset('images').'/ulasan/'.$this->product_id.'/'.$this->image ;
    }

}
