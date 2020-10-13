<?php

namespace App\Http\Resources\Sales;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Sales\SaleDetail;
use Carbon\Carbon;

class SaleItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $date_order = $this->resource->date_order;
        $date_jatuh_tempo = Carbon::parse($date_order);
        $date_jatuh_tempo2 = $date_jatuh_tempo->addHours(24)->format('Y-m-d H:i:s');
        return  [
            'id'    => $this->resource->id,
            'code'        => $this->resource->code,
            'coupon'        => $this->resource->coupon,
            'customer_id'      => $this->resource->customer_id,
            'customer_name'      => $this->resource->customerName(),
            'date'      => $this->resource->date,
            'time'      => $this->resource->time,
            'date_order'      => $date_order,
            'date_payment' => $this->resource->date_payment,
            'date_payment_confirmation'=> $this->resource->date_payment_confirmation,
            'date_shipping' => $this->resource->date_shipping,
            'date_complete' =>$this->resource->date_complete,
            'date_jatuh_tempo' => $date_jatuh_tempo2,
            'date_cancel' => $this->resource->date_cancel,
            'payment_methode' => $this->resource->payment_methode,
            'payment_channel' => $this->resource->payment_channel,
            'status' => $this->resource->status,
            'status_display' => $this->resource->status == 1 ? 'Dibayar' : 'Belum dibayar',
            'status_order' => $this->resource->status_order,
            'status_order_display' => $this->resource->isStatusOrder(),
            'total_bill' => $this->resource->total_bill,
            'total_before_tax' => $this->resource->total_before_tax,
            'total_price' => $this->resource->total_price,
            'total_price_product' => $this->resource->total_price_product,
            'total_service' => $this->resource->total_service,
            'total_shipping' => $this->resource->total_shipping,
            'total_tax' => $this->resource->total_tax,
            'total_item'=> SaleDetail::where('sale_id',$this->resource->id)->get()->count(),
            'address' =>$this->resource->address,
            'lattitude' =>$this->resource->lattitude,
            'longitude' =>$this->resource->longitude,
            'jarak' => $this->resource->jarak,
            'notes' => $this->resource->notes,
        ];
    }
}
