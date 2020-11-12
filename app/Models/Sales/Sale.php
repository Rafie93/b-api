<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer\Customer;

class Sale extends Model
{
    protected $table = "sale";
    protected $fillable = ["code","customer_id","date","time","total_price","total_before_tax","total_price_product","total_shipping","total_service","total_tax",
    "discount","status","payment_methode","payment_channel","notes","coupon","jarak","creator_id","invoice_id",
    "transaction_by","address","lattitude","longitude","status_order","unik_code_transfer","total_bill","date_order","date_payment","date_payment_confirmation","date_cancel","image",
    "date_shipping","date_complete","no_kartu"
    ];

    public function customerName()
    {
        $cs = Customer::where('id',$this->customer_id)->get();
        if(count($cs)>0){
            return $cs->first()->name;
        }else{
            return 'Walk-Customer';
        }
    }

    public function isStatusOrder()
    {
        $s = $this->status_order;
        if($s==null){
            return "Selesai";
        }else  if($s==1){
            return "Pesanan Baru";
        }else  if($s==2){
            return "Pesanan Diproses";
        }else  if($s==3){
            return "Pesanan Dikirim";
        }else  if($s==4){
            return "Pesanan Diterima";
        }else  if($s==99){
            return "Pesanan Dibatalkan";
        }
        return "";
    }

    public function image()
    {
        return $this->image=="" ? '' : asset('images').'/sale/'.$this->id.'/'.$this->image ;
    }
}
