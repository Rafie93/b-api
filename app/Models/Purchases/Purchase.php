<?php

namespace App\Models\Purchases;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Supplier\Supplier;

class Purchase extends Model
{
    protected $table = "purchase";
    protected $fillable = ["code","date","notes","status","creator_id","supplier_id","receive_date","receive_id","approved_date","approved_id"];
    public function status()
    {
        $status = $this->status;
        if($status==1){
            return "Pembelian Baru";
        }else if($status==2){
            return "Barang Sudah Datang";
        }else if($status==3){
            return "Pembelian Dibatalkan";
        }else if($status==4){
            return "Pembelian Di Approve";
        }else{
            return "Draft";
        }
    }
    public function supplier()
    {
        $sup_id =$this->supplier_id;
        if($sup_id!=null){
            return Supplier::find($sup_id)->name;
        }else{
            return "Tanpa Supplier";
        }
    }

    public function creator()
    {
        $userId = $this->creator_id;
        return User::find($userId)->first()->name;
    }
    public function receive()
    {
        $userId = $this->receive_id;
        if ($userId!=null){
            return User::find($userId)->first()->name;
        }else{
            return "";
        }
    }
    public function approved()
    {
        $userId = $this->approved_id;
        if ($userId!=null){
            return User::find($userId)->first()->name;
        }else{
            return "";
        }
    }
    public function penanggung_jawab()
    {
        $userId = $this->creator_id;
        $penanggungId =  User::find($userId)->first()->penanggung_id;
        if($penanggungId == null){
            return User::where('id',$userId)->first()->name;
        }else{
            return User::where('id',$penanggungId)->first()->name;
        }
    }
}

