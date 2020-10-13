<?php

namespace App\Models\Promotion;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = "banner";
    protected $fillable = ["title","description","image","is_active","creator_id"];

    public function isAktif()
    {
       return $this->is_active==1 ? "Aktif" : "Non Aktif";
    }

    public function image()
    {
        return $this->image==null ||$this->image=="" ? asset('images/image-not-available.png') : asset('images/banner').'/'.$this->id.'/'.$this->image;
    }

}
