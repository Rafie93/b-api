<?php

namespace App\Models\Sistem;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = "setting";
    protected $fillable = ["code","name","content","is_active"];

    public function isAktif()
    {
       return $this->is_active==1 ? "Aktif" : "Non Aktif";
    }

    public function getContent()
    {
        $content = json_decode($this->content,true);
        return $content;
    }

    public function isLogo()
    {
        $myLogo = $this->getContent()['logo'];
        return $myLogo=='' || $myLogo==null ? asset('') : asset('images/setting').'/'.$myLogo ;
    }
}
