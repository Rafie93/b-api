<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;
use App\Models\Products\Category;
class Product extends Model
{
    protected $table = "product";
    protected $fillable = ["sku","barcode_type","barcode","category_id","name","description","brand","price_modal","price",
    "thumbnail","image","alert_quantity","alert_quantity_warehouse","tax_id","is_tax_method","is_active","is_show_in_menu","creator_id","product_type"];

    public function thumbnail()
    {
        return $this->thumbnail=="" ? asset('images/empty.png') : asset('images').'/product/'.$this->id.'/'.$this->thumbnail ;
    }
    public function isAktif()
    {
       return $this->is_active==1 ? "Aktif" : "Non Aktif";
    }
    public function isProductType()
    {
       return $this->product_type==2 ? "Paket" : "Normal";
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }
    public function category_parent()
    {
      $categoryChild =  Category::where('id',$this->category_id)->first();
      $parentId = $categoryChild->parent_id;
      if($parentId!=null){
        $par =  Category::where('id',$parentId)->first();
         return $par->name;
      }else{
          return "";
      }
    }

    public function sale_detail()
    {
        return $this->hasMany('App\Models\Sales\SaleDetail','product_id');
    }
}
