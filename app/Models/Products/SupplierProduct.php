<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class SupplierProduct extends Model
{
    protected $table = "supplier_product";
    protected $fillable = ["supplier_id","product_id"];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier\Supplier','supplier_id');
    }
}
