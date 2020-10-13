<?php

namespace App\Models\Promotion;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
     protected $table = "news";
     protected $fillable = ["title","description","category_news","is_status","creator_id","slug","thumbnail"];
     public function thumbnail()
     {
         return $this->thumbnail==null ||$this->thumbnail=="" ? asset('images/image-not-available.png') : asset('images/news').'/'.$this->id.'/'.$this->thumbnail;
     }
     public function isStatus()
     {
        return $this->is_status==1 ? "Publish" : "Draft";
     }
}
