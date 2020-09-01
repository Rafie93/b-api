<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;
use App\User;
class Order extends Model
{
    protected $table = "order_product";
    protected $fillable = ["code","type","date","note","status","creator_id",
    "receiver_id","arrival_date","approved_order_date","approved_order_id",
    "approved_date","approved_id","proses_date","code_gudang","send_id","send_date"];

    public function status()
    {
        $status = $this->status;
        if($status==1){
            return "Pesanan Baru";
        }else if($status==11){
            return "Pesanan di Approve";
        }else if($status==2){
            return "Pesanan di Proses";
        }else if($status==3){
            return "Pesanan di Batalkan";
        }else if($status==4){
            return "Pengiriman di Approved";
        }
        else if($status==5){
            return "Pesanan Sedang Dikirim";
        }else if($status==6){
            return "Pesanan Sudah Datang";
        }else{
            return "Draft";
        }
    }
    public function creator()
    {
        $userId = $this->creator_id;
        return User::where('id',$userId)->first()->name;
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

    public function approved()
    {
        $userId = $this->approved_id;
        if($userId!=null){
            return User::where('id',$userId)->first()->name;
        }else{
            return "-";
        }
    }

    public function approved_order()
    {
        $userId = $this->approved_order_id;
        if($userId!=null){
            return User::where('id',$userId)->first()->name;
        }else{
            return "-";
        }
    }
}
