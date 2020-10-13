<?php

namespace App\Models\Sistem;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $table = "bank_account";
    protected $fillable = ['bank_name','bank_logo','bank_account_no','bank_account_name','description','is_active'];

    public function isAktif()
    {
       return $this->is_active==1 ? "Aktif" : "Non Aktif";
    }

    public function logo()
    {
        return $this->bank_logo==null ? asset('images/image-not-available.png') : asset('images/bank').'/'.$this->bank_logo;
    }
}
