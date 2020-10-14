<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = "category";
    protected $fillable = ["parent_id","name","is_active","description","image"];

    public function image()
    {
        return !$this->image ? asset('images/kategori/separation.png') : asset('images').'/kategori/'.$this->image ;
    }

    public function parent()
    {
        return Category::where('id',$this->parent_id)->first();
    }

    public function isAktif()
    {
       return $this->is_active==1 ? "Aktif" : "Non Aktif";
    }
}
