<?php

namespace App\Models\Purchases;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Models\Supplier\Supplier;

class Purchase extends Model
{
    protected $table = "purchase";
    protected $fillable = ["code","date","notes","status","creator_id","supplier_id","receive_date","receive_id","approved_date","approved_id","grand_total","sales"];
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
        }else if($status==5){
            return "Barang Datang Tidak Sesuai";
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
    public function supplier_pivot()
    {
        return $this->belongsTo('App\Models\Supplier\Supplier','supplier_id');
    }

    public function creator()
    {
        $creator = $this->creator_id;
        return User::where('id',$creator)->first()->name;
    }
    public function receive()
    {
        $userId = $this->receive_id;
        if ($userId!=null){
            return User::where('id',$userId)->first()->name;
        }else{
            return "";
        }
    }
    public function approved()
    {
        $userId = $this->approved_id;
        if ($userId!=null){
            return User::where('id',$userId)->first()->name;
        }else{
            return "";
        }
    }
    public function penanggung_jawab()
    {
        $userId = $this->approved_id;
        if ($userId!=null){
            return User::where('id',$userId)->first()->name;
        }else{
            return "";
        }
    }
}

